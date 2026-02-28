<?php
class Entrada extends BaseModel {

    public function getAll(array $filters = []): array {
        $sql    = "SELECT e.*, c.nome AS cliente_nome, c.cpf,
                          cat.nome AS categoria_nome, cat.tipo AS categoria_tipo,
                          th.nome AS tipo_honorario
                   FROM entradas e
                   LEFT JOIN clientes c ON e.cliente_id = c.id
                   LEFT JOIN categorias_receita cat ON e.categoria_id = cat.id
                   LEFT JOIN tipos_honorarios th ON e.tipo_honorario_id = th.id
                   WHERE e.deleted_at IS NULL";
        $params = [];

        if (!empty($filters['cliente_id'])) {
            $sql .= " AND e.cliente_id = ?";
            $params[] = (int)$filters['cliente_id'];
        }
        if (!empty($filters['categoria_id'])) {
            $sql .= " AND e.categoria_id = ?";
            $params[] = (int)$filters['categoria_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = ?";
            $params[] = $filters['status'];
        }
        if (!empty($filters['data_inicio'])) {
            $sql .= " AND e.data_entrada >= ?";
            $params[] = $filters['data_inicio'];
        }
        if (!empty($filters['data_fim'])) {
            $sql .= " AND e.data_entrada <= ?";
            $params[] = $filters['data_fim'];
        }
        if (isset($filters['valor_min']) && $filters['valor_min'] !== '') {
            $sql .= " AND e.valor_entrada >= ?";
            $params[] = (float)$filters['valor_min'];
        }
        if (isset($filters['valor_max']) && $filters['valor_max'] !== '') {
            $sql .= " AND e.valor_entrada <= ?";
            $params[] = (float)$filters['valor_max'];
        }

        $sql .= " ORDER BY e.data_entrada DESC, e.id DESC";
        return $this->fetchAll($sql, $params);
    }

    public function getById(int $id): array|false {
        return $this->fetchOne(
            "SELECT e.*, c.nome AS cliente_nome, cat.nome AS categoria_nome,
                    th.nome AS tipo_honorario
             FROM entradas e
             LEFT JOIN clientes c ON e.cliente_id = c.id
             LEFT JOIN categorias_receita cat ON e.categoria_id = cat.id
             LEFT JOIN tipos_honorarios th ON e.tipo_honorario_id = th.id
             WHERE e.id = ? AND e.deleted_at IS NULL",
            [$id]
        );
    }

    public function create(array $data): int {
        // Auto-calculate valor for Sucumbência / Êxito
        $tipoNome = $this->fetchOne(
            "SELECT nome FROM tipos_honorarios WHERE id = ?",
            [(int)($data['tipo_honorario_id'] ?? 0)]
        );
        if ($tipoNome && in_array($tipoNome['nome'], ['Sucumbência', 'Êxito'], true)) {
            $vc = (float)($data['valor_causa'] ?? 0);
            $pc = (float)($data['percentual'] ?? 0);
            if ($vc > 0 && $pc > 0) {
                $data['valor_entrada'] = round(($vc * $pc) / 100, 2);
            }
        }

        $allowed = [
            'cliente_id','categoria_id','tipo_honorario_id','valor_entrada',
            'valor_causa','percentual','data_entrada','data_vencimento',
            'descricao','status','comprovante','num_parcelas',
        ];
        $clean = array_intersect_key($data, array_flip($allowed));
        $clean['num_parcelas'] = (int)($clean['num_parcelas'] ?? 1);

        $id = $this->insert('entradas', $clean);

        if ($clean['num_parcelas'] > 1) {
            $this->criarParcelas(
                $id,
                (float)$clean['valor_entrada'],
                $clean['num_parcelas'],
                $clean['data_vencimento'] ?? $clean['data_entrada']
            );
        }

        $this->logOperation('entradas', $id, 'INSERT', null, $clean);
        return $id;
    }

    public function update(int $id, array $data): bool {
        $allowed = [
            'cliente_id','categoria_id','tipo_honorario_id','valor_entrada',
            'valor_causa','percentual','data_entrada','data_vencimento',
            'descricao','status','comprovante','num_parcelas',
        ];
        $clean   = array_intersect_key($data, array_flip($allowed));
        $antigos = $this->getById($id);
        $rows    = parent::update('entradas', $clean, ['id' => $id]);
        if ($rows > 0) {
            $this->logOperation('entradas', $id, 'UPDATE', $antigos, $clean);
        }
        return $rows > 0;
    }

    public function delete(int $id): bool {
        $antigos = $this->getById($id);
        $rows    = $this->softDelete('entradas', $id);
        if ($rows > 0) {
            $this->logOperation('entradas', $id, 'DELETE', $antigos, null);
        }
        return $rows > 0;
    }

    public function getTotalMes(int $mes, int $ano): float {
        $row = $this->fetchOne(
            "SELECT COALESCE(SUM(valor_entrada), 0) AS total
             FROM entradas
             WHERE deleted_at IS NULL
               AND status != 'Cancelado'
               AND MONTH(data_entrada) = ?
               AND YEAR(data_entrada) = ?",
            [$mes, $ano]
        );
        return (float)($row['total'] ?? 0);
    }

    public function criarParcelas(int $entrada_id, float $valor, int $num_parcelas, string $data_inicio): void {
        $valorParcela = round($valor / $num_parcelas, 2);
        $diff         = $valor - ($valorParcela * $num_parcelas);
        $data         = new DateTime($data_inicio);

        for ($i = 1; $i <= $num_parcelas; $i++) {
            $v = $valorParcela;
            if ($i === $num_parcelas) {
                $v += $diff; // ajuste de centavos na última parcela
            }
            $this->insert('parcelas', [
                'tipo'           => 'entrada',
                'referencia_id'  => $entrada_id,
                'numero_parcela' => $i,
                'valor'          => $v,
                'data_vencimento'=> $data->format('Y-m-d'),
                'status'         => 'Aberto',
            ]);
            $data->modify('+1 month');
        }
    }
}
