<?php
class Cliente extends BaseModel {

    public function getAll(array $filters = []): array {
        $sql    = "SELECT * FROM clientes WHERE deleted_at IS NULL";
        $params = [];

        if (!empty($filters['nome'])) {
            $sql      .= " AND nome LIKE ?";
            $params[]  = '%' . $filters['nome'] . '%';
        }

        $sql .= " ORDER BY nome ASC";
        return $this->fetchAll($sql, $params);
    }

    public function getById(int $id): array|false {
        return $this->fetchOne(
            "SELECT * FROM clientes WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    public function create(array $data): int {
        $allowed = ['nome','cpf','email','telefone','endereco'];
        $clean   = array_intersect_key($data, array_flip($allowed));
        $id      = $this->insert('clientes', $clean);
        $this->logOperation('clientes', $id, 'INSERT', null, $clean);
        return $id;
    }

    public function update(int $id, array $data): bool {
        $allowed = ['nome','cpf','email','telefone','endereco'];
        $clean   = array_intersect_key($data, array_flip($allowed));
        $antigos = $this->getById($id);
        $rows    = parent::update('clientes', $clean, ['id' => $id]);
        if ($rows > 0) {
            $this->logOperation('clientes', $id, 'UPDATE', $antigos, $clean);
        }
        return $rows > 0;
    }

    public function delete(int $id): bool {
        $antigos = $this->getById($id);
        $rows    = $this->softDelete('clientes', $id);
        if ($rows > 0) {
            $this->logOperation('clientes', $id, 'DELETE', $antigos, null);
        }
        return $rows > 0;
    }

    public function search(string $termo): array {
        return $this->fetchAll(
            "SELECT id, nome, cpf, email, telefone FROM clientes
             WHERE deleted_at IS NULL AND (nome LIKE ? OR cpf LIKE ?)
             ORDER BY nome ASC LIMIT 20",
            ['%' . $termo . '%', '%' . $termo . '%']
        );
    }

    public function getInadimplentes(int $dias = 30): array {
        return $this->fetchAll(
            "SELECT e.*, c.nome AS cliente_nome, c.cpf,
                    cat.nome AS categoria_nome, th.nome AS tipo_honorario,
                    DATEDIFF(CURDATE(), e.data_vencimento) AS dias_vencido
             FROM entradas e
             LEFT JOIN clientes c ON e.cliente_id = c.id
             LEFT JOIN categorias_receita cat ON e.categoria_id = cat.id
             LEFT JOIN tipos_honorarios th ON e.tipo_honorario_id = th.id
             WHERE e.status = 'Aberto'
               AND e.data_vencimento < CURDATE()
               AND e.deleted_at IS NULL
               AND DATEDIFF(CURDATE(), e.data_vencimento) >= ?
             ORDER BY e.data_vencimento ASC",
            [$dias]
        );
    }
}
