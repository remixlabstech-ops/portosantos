<?php
class Fornecedor extends BaseModel {

    public function getAll(array $filters = []): array {
        $sql    = "SELECT * FROM fornecedores WHERE deleted_at IS NULL";
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
            "SELECT * FROM fornecedores WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    public function create(array $data): int {
        $allowed = ['nome','cnpj','email','telefone','endereco'];
        $clean   = array_intersect_key($data, array_flip($allowed));
        $id      = $this->insert('fornecedores', $clean);
        $this->logOperation('fornecedores', $id, 'INSERT', null, $clean);
        return $id;
    }

    public function update(int $id, array $data): bool {
        $allowed = ['nome','cnpj','email','telefone','endereco'];
        $clean   = array_intersect_key($data, array_flip($allowed));
        $antigos = $this->getById($id);
        $rows    = parent::update('fornecedores', $clean, ['id' => $id]);
        if ($rows > 0) {
            $this->logOperation('fornecedores', $id, 'UPDATE', $antigos, $clean);
        }
        return $rows > 0;
    }

    public function delete(int $id): bool {
        $antigos = $this->getById($id);
        $rows    = $this->softDelete('fornecedores', $id);
        if ($rows > 0) {
            $this->logOperation('fornecedores', $id, 'DELETE', $antigos, null);
        }
        return $rows > 0;
    }

    public function search(string $termo): array {
        return $this->fetchAll(
            "SELECT id, nome, cnpj, email, telefone FROM fornecedores
             WHERE deleted_at IS NULL AND (nome LIKE ? OR cnpj LIKE ?)
             ORDER BY nome ASC LIMIT 20",
            ['%' . $termo . '%', '%' . $termo . '%']
        );
    }
}
