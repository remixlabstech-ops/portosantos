<?php
/**
 * Model: Saida (despesas)
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class Saida extends BaseModel
{
    protected string $table = 'saidas';

    /**
     * Cria nova saída e seus rateios/parcelas.
     *
     * @param array<string, mixed> $data
     * @param int $parcelas
     * @param array<array<string, mixed>> $rateios
     */
    public function criar(array $data, int $parcelas = 1, array $rateios = []): int
    {
        $status = $this->calcularStatus($data['data_vencimento'] ?? date('Y-m-d'));
        $id = $this->insert([
            'fornecedor_id'  => !empty($data['fornecedor_id']) ? (int) $data['fornecedor_id'] : null,
            'categoria'      => htmlspecialchars(trim($data['categoria'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'centro_custo_id'=> !empty($data['centro_custo_id']) ? (int) $data['centro_custo_id'] : null,
            'tipo_taxa'      => in_array($data['tipo_taxa'] ?? '', ['percentual', 'valor_fixo']) ? $data['tipo_taxa'] : 'valor_fixo',
            'taxa_valor'     => !empty($data['taxa_valor']) ? (float) $data['taxa_valor'] : null,
            'valor'          => (float) $data['valor'],
            'data_vencimento'=> $data['data_vencimento'] ?? date('Y-m-d'),
            'status'         => $status,
            'observacoes'    => htmlspecialchars(trim($data['observacoes'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'empresa'        => (int) ($data['empresa'] ?? EMPRESA_PADRAO),
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        if (!empty($rateios)) {
            $this->criarRateios($id, $rateios);
        }

        if ($parcelas > 1) {
            $this->criarParcelas($id, (float) $data['valor'], $data['data_vencimento'], $parcelas);
        }

        return $id;
    }

    /**
     * Atualiza status de uma saída.
     */
    public function atualizarStatus(int $id, string $status): bool
    {
        $allowed = [STATUS_PAGO, STATUS_PENDENTE, STATUS_ATRASADO];
        if (!in_array($status, $allowed)) {
            return false;
        }
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Lista saídas com dados do fornecedor e centro de custo.
     *
     * @param array<string, mixed> $filtros
     */
    public function listar(array $filtros = [], int $page = 1): array
    {
        $conds  = ['s.deleted_at IS NULL'];
        $params = [];

        if (!empty($filtros['fornecedor_id'])) {
            $conds[]  = 's.fornecedor_id = ?';
            $params[] = (int) $filtros['fornecedor_id'];
        }
        if (!empty($filtros['status'])) {
            $conds[]  = 's.status = ?';
            $params[] = $filtros['status'];
        }
        if (!empty($filtros['data_inicio'])) {
            $conds[]  = 's.data_vencimento >= ?';
            $params[] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $conds[]  = 's.data_vencimento <= ?';
            $params[] = $filtros['data_fim'];
        }

        $where  = implode(' AND ', $conds);
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM saidas s WHERE {$where}"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $dataStmt = $this->db->prepare(
            "SELECT s.*, f.nome AS fornecedor_nome, cc.nome AS centro_custo_nome
             FROM saidas s
             LEFT JOIN fornecedores f ON s.fornecedor_id = f.id
             LEFT JOIN centros_custo cc ON s.centro_custo_id = cc.id
             WHERE {$where}
             ORDER BY s.created_at DESC
             LIMIT ? OFFSET ?"
        );
        $dataStmt->execute(array_merge($params, [ITEMS_PER_PAGE, $offset]));

        return [
            'data'  => $dataStmt->fetchAll(),
            'total' => $total,
            'pages' => (int) ceil($total / ITEMS_PER_PAGE),
        ];
    }

    /**
     * Resumo do mês para o dashboard.
     */
    public function resumoMes(): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                SUM(CASE WHEN MONTH(data_vencimento) = MONTH(CURDATE()) AND YEAR(data_vencimento) = YEAR(CURDATE()) THEN valor ELSE 0 END) AS total_mes,
                SUM(CASE WHEN MONTH(data_vencimento) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(data_vencimento) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN valor ELSE 0 END) AS total_mes_anterior,
                SUM(CASE WHEN status != ? AND deleted_at IS NULL THEN valor ELSE 0 END) AS total_pendente
             FROM saidas
             WHERE deleted_at IS NULL"
        );
        $stmt->execute([STATUS_PAGO]);
        return $stmt->fetch() ?: [];
    }

    /**
     * Dados mensais para gráfico (últimos 12 meses).
     */
    public function dadosMensais(): array
    {
        $stmt = $this->db->query(
            "SELECT DATE_FORMAT(data_vencimento, '%Y-%m') AS mes, SUM(valor) AS total
             FROM saidas
             WHERE deleted_at IS NULL
               AND data_vencimento >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY mes
             ORDER BY mes"
        );
        return $stmt->fetchAll();
    }

    /**
     * Top 5 centros de custo mais caros.
     */
    public function topCentrosCusto(): array
    {
        $stmt = $this->db->query(
            "SELECT cc.nome, SUM(s.valor) AS total
             FROM saidas s
             JOIN centros_custo cc ON s.centro_custo_id = cc.id
             WHERE s.deleted_at IS NULL
             GROUP BY s.centro_custo_id
             ORDER BY total DESC
             LIMIT 5"
        );
        return $stmt->fetchAll();
    }

    /**
     * Cria rateios para uma saída.
     *
     * @param array<array<string, mixed>> $rateios
     */
    private function criarRateios(int $saidaId, array $rateios): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO rateios
                (saida_id, tipo_rateio, cliente_id, centro_custo_id, tipo_divisao, percentual_divisao, valor_divisao, empresa, created_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );

        foreach ($rateios as $r) {
            $stmt->execute([
                $saidaId,
                htmlspecialchars($r['tipo_rateio'] ?? 'administrativo', ENT_QUOTES, 'UTF-8'),
                !empty($r['cliente_id']) ? (int) $r['cliente_id'] : null,
                !empty($r['centro_custo_id']) ? (int) $r['centro_custo_id'] : null,
                in_array($r['tipo_divisao'] ?? '', ['percentual', 'valor_fixo']) ? $r['tipo_divisao'] : 'percentual',
                !empty($r['percentual_divisao']) ? (float) $r['percentual_divisao'] : null,
                !empty($r['valor_divisao']) ? (float) $r['valor_divisao'] : null,
                EMPRESA_PADRAO,
            ]);
        }
    }

    /**
     * Cria parcelas para uma saída.
     */
    private function criarParcelas(int $saidaId, float $valor, string $dataVencimento, int $qtd): void
    {
        $valorParcela = round($valor / $qtd, 2);
        $stmt = $this->db->prepare(
            "INSERT INTO parcelas
                (saida_id, numero, valor, data_vencimento, status, empresa, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );

        for ($i = 1; $i <= $qtd; $i++) {
            $dataP = date('Y-m-d', strtotime("{$dataVencimento} +{$i} month"));
            $stmt->execute([
                $saidaId,
                $i,
                $valorParcela,
                $dataP,
                STATUS_PENDENTE,
                EMPRESA_PADRAO,
            ]);
        }
    }

    /**
     * Calcula status com base na data de vencimento.
     */
    public function calcularStatus(string $dataVencimento): string
    {
        $hoje = new DateTime();
        $venc = new DateTime($dataVencimento);
        return $venc < $hoje ? STATUS_ATRASADO : STATUS_PENDENTE;
    }
}
