<?php
class CentroCusto extends BaseModel {

    public function getAll(array $filters = []): array {
        return $this->fetchAll(
            "SELECT * FROM centros_custo WHERE deleted_at IS NULL ORDER BY nome ASC"
        );
    }

    public function getById(int $id): array|false {
        return $this->fetchOne(
            "SELECT * FROM centros_custo WHERE id = ? AND deleted_at IS NULL",
            [$id]
        );
    }

    public function create(array $data): int {
        $allowed = ['nome','descricao'];
        $clean   = array_intersect_key($data, array_flip($allowed));
        $id      = $this->insert('centros_custo', $clean);
        $this->logOperation('centros_custo', $id, 'INSERT', null, $clean);
        return $id;
    }

    public function update(int $id, array $data): bool {
        $allowed = ['nome','descricao'];
        $clean   = array_intersect_key($data, array_flip($allowed));
        $antigos = $this->getById($id);
        $rows    = parent::update('centros_custo', $clean, ['id' => $id]);
        if ($rows > 0) {
            $this->logOperation('centros_custo', $id, 'UPDATE', $antigos, $clean);
        }
        return $rows > 0;
    }

    public function delete(int $id): bool {
        $antigos = $this->getById($id);
        $rows    = $this->softDelete('centros_custo', $id);
        if ($rows > 0) {
            $this->logOperation('centros_custo', $id, 'DELETE', $antigos, null);
        }
        return $rows > 0;
    }
}
