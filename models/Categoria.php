<?php
class Categoria extends BaseModel {

    public function getAllReceita(): array {
        return $this->fetchAll(
            "SELECT * FROM categorias_receita ORDER BY tipo, nome ASC"
        );
    }

    public function getAllDespesa(): array {
        return $this->fetchAll(
            "SELECT * FROM categorias_despesa ORDER BY nome ASC"
        );
    }

    public function getReceitaById(int $id): array|false {
        return $this->fetchOne("SELECT * FROM categorias_receita WHERE id = ?", [$id]);
    }

    public function getDespesaById(int $id): array|false {
        return $this->fetchOne("SELECT * FROM categorias_despesa WHERE id = ?", [$id]);
    }

    public function create(array $data, string $tipo): int {
        if ($tipo === 'receita') {
            $allowed = ['nome','tipo'];
            $table   = 'categorias_receita';
        } else {
            $allowed = ['nome'];
            $table   = 'categorias_despesa';
        }
        $clean = array_intersect_key($data, array_flip($allowed));
        $id    = $this->insert($table, $clean);
        $this->logOperation($table, $id, 'INSERT', null, $clean);
        return $id;
    }

    public function update(int $id, array $data, string $tipo): bool {
        if ($tipo === 'receita') {
            $allowed = ['nome','tipo'];
            $table   = 'categorias_receita';
            $antigos = $this->getReceitaById($id);
        } else {
            $allowed = ['nome'];
            $table   = 'categorias_despesa';
            $antigos = $this->getDespesaById($id);
        }
        $clean = array_intersect_key($data, array_flip($allowed));
        $rows  = parent::update($table, $clean, ['id' => $id]);
        if ($rows > 0) {
            $this->logOperation($table, $id, 'UPDATE', $antigos, $clean);
        }
        return $rows > 0;
    }

    public function delete(int $id, string $tipo): bool {
        $table   = ($tipo === 'receita') ? 'categorias_receita' : 'categorias_despesa';
        $antigos = ($tipo === 'receita') ? $this->getReceitaById($id) : $this->getDespesaById($id);
        $stmt    = $this->query("DELETE FROM `$table` WHERE id = ?", [$id]);
        if ($stmt->rowCount() > 0) {
            $this->logOperation($table, $id, 'DELETE', $antigos, null);
            return true;
        }
        return false;
    }
}
