<?php
class Saida extends BaseModel {

    public function getAll(array $filters = []): array {
        $sql    = "SELECT s.*, f.nome AS fornecedor_nome,
                          cd.nome AS categoria_nome, cc.nome AS centro_custo_nome
                   FROM saidas s
                   LEFT JOIN fornecedores f ON s.fornecedor_id = f.id
                   LEFT JOIN categorias_despesa cd ON s.categoria_id = cd.id
                   LEFT JOIN centros_custo cc ON s.centro_custo_id = cc.id
                   WHERE s.deleted_at IS NULL";
        $params = [];

        if (!empty($filters['fornecedor_id'])) {
            $sql .= " AND s.fornecedor_id = ?";
            $params[] = (int)$filters['fornecedor_id'];
        }
        if (!empty($filters['categoria_id'])) {
            $sql .= " AND s.categoria_id = ?";
            $params[] = (int)$filters['categoria_id'];
        }
        if (!empty($filters['centro_custo_id'])) {
            $sql .= " AND s.centro_custo_id = ?";
            $params[] = (int)$filters['centro_custo_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND s.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['data_inicio'])) {
            $sql .= " AND s.data_saida >= ?";
            $params[] = $filters['data_inicio'];
        }
        if (!empty($filters['data_fim'])) {
            $sql .= " AND s.data_saida <= ?";
            $params[] = $filters['data_fim'];
        }

        $sql .= " ORDER BY s.data_saida DESC, s.id DESC";
        return $this->fetchAll($sql, $params);
    }

    public function getById(int $id): array|false {
        $saida = $this->fetchOne(
            "SELECT s.*, f.nome AS fornecedor_nome, cd.nome AS categoria_nome,
                    cc.nome AS centro_custo_nome
             FROM saidas s
             LEFT JOIN fornecedores f ON s.fornecedor_id = f.id
             LEFT JOIN categorias_despesa cd ON s.categoria_id = cd.id
             LEFT JOIN centros_custo cc ON s.centro_custo_id = cc.id
             WHERE s.id = ? AND s.deleted_at IS NULL",
            [$id]
        );
        if ($saida) {
            $saida['rateios'] = $this->fetchAll(
                "SELECT r.*, c.nome AS cliente_nome FROM rateios r
                 LEFT JOIN clientes c ON r.cliente_id = c.id
                 WHERE r.saida_id = ?",
                [$id]
            );
        }
        return $saida;
    }

    public function create(array $data, array $rateios = []): int {
        $tipo = $data['tipo_rateio'] ?? 'administrativo';

        if (in_array($tipo, ['cliente', 'multiplos'], true) && !empty($rateios)) {
            $soma = array_sum(array_column($rateios, 'percentual'));
            if (abs($soma - 100) > 0.01) {
                throw new \InvalidArgumentException('A soma dos rateios deve ser 100%.');
            }
        }

        $allowed = [
            'fornecedor_id','categoria_id','centro_custo_id','descricao',
            'valor','taxa','tipo_taxa','data_saida','data_vencimento',
            'status','comprovante','num_parcelas','tipo_rateio',
        ];
        $clean = array_intersect_key($data, array_flip($allowed));
        $clean['num_parcelas'] = (int)($clean['num_parcelas'] ?? 1);

        // Nullable FKs
        if (empty($clean['fornecedor_id']))   unset($clean['fornecedor_id']);
        if (empty($clean['centro_custo_id'])) unset($clean['centro_custo_id']);
        if (empty($clean['taxa']))            { unset($clean['taxa']); unset($clean['tipo_taxa']); }

        $id = $this->insert('saidas', $clean);

        if (!empty($rateios)) {
            $this->salvarRateios($id, $rateios);
        }

        if ($clean['num_parcelas'] > 1) {
            $this->criarParcelas(
                $id,
                (float)$clean['valor'],
                $clean['num_parcelas'],
                $clean['data_vencimento'] ?? $clean['data_saida']
            );
        }

        $this->logOperation('saidas', $id, 'INSERT', null, $clean);
        return $id;
    }

    public function update(int $id, array $data): bool {
        $allowed = [
            'fornecedor_id','categoria_id','centro_custo_id','descricao',
            'valor','taxa','tipo_taxa','data_saida','data_vencimento',
            'status','comprovante','num_parcelas','tipo_rateio',
        ];
        $clean   = array_intersect_key($data, array_flip($allowed));
        $antigos = $this->getById($id);
        $rows    = parent::update('saidas', $clean, ['id' => $id]);
        if ($rows > 0) {
            $this->logOperation('saidas', $id, 'UPDATE', $antigos, $clean);
        }
        return $rows > 0;
    }

    public function delete(int $id): bool {
        $antigos = $this->getById($id);
        $rows    = $this->softDelete('saidas', $id);
        if ($rows > 0) {
            $this->logOperation('saidas', $id, 'DELETE', $antigos, null);
        }
        return $rows > 0;
    }

    public function getTotalMes(int $mes, int $ano): float {
        $row = $this->fetchOne(
            "SELECT COALESCE(SUM(valor), 0) AS total
             FROM saidas
             WHERE deleted_at IS NULL
               AND status != 'Cancelado'
               AND MONTH(data_saida) = ?
               AND YEAR(data_saida) = ?",
            [$mes, $ano]
        );
        return (float)($row['total'] ?? 0);
    }

    public function criarParcelas(int $saida_id, float $valor, int $num_parcelas, string $data_inicio): void {
        $valorParcela = round($valor / $num_parcelas, 2);
        $diff         = $valor - ($valorParcela * $num_parcelas);
        $data         = new DateTime($data_inicio);

        for ($i = 1; $i <= $num_parcelas; $i++) {
            $v = $valorParcela;
            if ($i === $num_parcelas) {
                $v += $diff;
            }
            $this->insert('parcelas', [
                'tipo'           => 'saida',
                'referencia_id'  => $saida_id,
                'numero_parcela' => $i,
                'valor'          => $v,
                'data_vencimento'=> $data->format('Y-m-d'),
                'status'         => 'Aberto',
            ]);
            $data->modify('+1 month');
        }
    }

    public function salvarRateios(int $saida_id, array $rateios): void {
        $this->query("DELETE FROM rateios WHERE saida_id = ?", [$saida_id]);
        foreach ($rateios as $r) {
            $clienteId = !empty($r['cliente_id']) ? (int)$r['cliente_id'] : null;
            $this->insert('rateios', [
                'saida_id'   => $saida_id,
                'cliente_id' => $clienteId,
                'percentual' => (float)$r['percentual'],
            ]);
        }
    }
}
