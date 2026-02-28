<?php
/**
 * Model: Entrada (honorários)
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class Entrada extends BaseModel
{
    protected string $table = 'entradas';

    /**
     * Cria nova entrada e suas parcelas.
     *
     * @param array<string, mixed> $data
     * @param int $parcelas Número de parcelas (1-12)
     * @return int ID da entrada criada
     */
    public function criar(array $data, int $parcelas = 1): int
    {
        $status = $this->calcularStatus($data['data_vencimento'] ?? date('Y-m-d'));
        $id = $this->insert([
            'cliente_id'      => (int) $data['cliente_id'],
            'categoria'       => htmlspecialchars(trim($data['categoria'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'tipo_honorario'  => htmlspecialchars(trim($data['tipo_honorario'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'valor_causa'     => !empty($data['valor_causa']) ? (float) $data['valor_causa'] : null,
            'percentual'      => !empty($data['percentual']) ? (float) $data['percentual'] : null,
            'valor'           => (float) $data['valor'],
            'data_vencimento' => $data['data_vencimento'] ?? date('Y-m-d'),
            'status'          => $status,
            'observacoes'     => htmlspecialchars(trim($data['observacoes'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'empresa'         => (int) ($data['empresa'] ?? EMPRESA_PADRAO),
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        if ($parcelas > 1) {
            $this->criarParcelas($id, (float) $data['valor'], $data['data_vencimento'], $parcelas);
        }

        return $id;
    }

    /**
     * Atualiza status de uma entrada.
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
     * Salva caminho do comprovante.
     */
    public function salvarComprovante(int $id, string $caminho): bool
    {
        return $this->update($id, ['comprovante' => $caminho]);
    }

    /**
     * Lista entradas com dados do cliente e categoria, com filtros opcionais.
     *
     * @param array<string, mixed> $filtros
     */
    public function listar(array $filtros = [], int $page = 1): array
    {
        $conds  = ['e.deleted_at IS NULL'];
        $params = [];

        if (!empty($filtros['cliente_id'])) {
            $conds[]  = 'e.cliente_id = ?';
            $params[] = (int) $filtros['cliente_id'];
        }
        if (!empty($filtros['status'])) {
            $conds[]  = 'e.status = ?';
            $params[] = $filtros['status'];
        }
        if (!empty($filtros['data_inicio'])) {
            $conds[]  = 'e.data_vencimento >= ?';
            $params[] = $filtros['data_inicio'];
        }
        if (!empty($filtros['data_fim'])) {
            $conds[]  = 'e.data_vencimento <= ?';
            $params[] = $filtros['data_fim'];
        }
        if (!empty($filtros['valor_min'])) {
            $conds[]  = 'e.valor >= ?';
            $params[] = (float) $filtros['valor_min'];
        }
        if (!empty($filtros['valor_max'])) {
            $conds[]  = 'e.valor <= ?';
            $params[] = (float) $filtros['valor_max'];
        }

        $where  = implode(' AND ', $conds);
        $offset = ($page - 1) * ITEMS_PER_PAGE;

        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM entradas e WHERE {$where}"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $dataStmt = $this->db->prepare(
            "SELECT e.*, c.nome AS cliente_nome, c.cpf
             FROM entradas e
             LEFT JOIN clientes c ON e.cliente_id = c.id
             WHERE {$where}
             ORDER BY e.created_at DESC
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
     * Cria parcelas para uma entrada.
     */
    private function criarParcelas(int $entradaId, float $valor, string $dataVencimento, int $qtd): void
    {
        $valorParcela = round($valor / $qtd, 2);
        $stmtParcela  = $this->db->prepare(
            "INSERT INTO parcelas
                (entrada_id, numero, valor, data_vencimento, status, empresa, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );

        for ($i = 1; $i <= $qtd; $i++) {
            $dataP = date('Y-m-d', strtotime("{$dataVencimento} +{$i} month"));
            $stmtParcela->execute([
                $entradaId,
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

    /**
     * Dados de resumo para o dashboard.
     */
    public function resumoMes(): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                SUM(CASE WHEN MONTH(data_vencimento) = MONTH(CURDATE()) AND YEAR(data_vencimento) = YEAR(CURDATE()) THEN valor ELSE 0 END) AS total_mes,
                SUM(CASE WHEN MONTH(data_vencimento) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(data_vencimento) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN valor ELSE 0 END) AS total_mes_anterior,
                SUM(CASE WHEN status != ? AND deleted_at IS NULL THEN valor ELSE 0 END) AS total_pendente
             FROM entradas
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
             FROM entradas
             WHERE deleted_at IS NULL
               AND data_vencimento >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY mes
             ORDER BY mes"
        );
        return $stmt->fetchAll();
    }

    /**
     * Top 5 clientes mais rentáveis.
     */
    public function topClientes(): array
    {
        $stmt = $this->db->query(
            "SELECT c.nome, SUM(e.valor) AS total
             FROM entradas e
             JOIN clientes c ON e.cliente_id = c.id
             WHERE e.deleted_at IS NULL AND e.status = 'Pago'
             GROUP BY e.cliente_id
             ORDER BY total DESC
             LIMIT 5"
        );
        return $stmt->fetchAll();
    }
}
