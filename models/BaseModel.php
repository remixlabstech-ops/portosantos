<?php
abstract class BaseModel {
    protected PDO $pdo;

    public function __construct() {
        $this->pdo = getConnection();
    }

    protected function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    protected function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }

    protected function fetchOne(string $sql, array $params = []): array|false {
        return $this->query($sql, $params)->fetch();
    }

    protected function insert(string $table, array $data): int {
        $cols   = implode(', ', array_map(fn($c) => "`$c`", array_keys($data)));
        $places = implode(', ', array_fill(0, count($data), '?'));
        $this->query("INSERT INTO `$table` ($cols) VALUES ($places)", array_values($data));
        return (int)$this->pdo->lastInsertId();
    }

    protected function update(string $table, array $data, array $where): int {
        $sets  = implode(', ', array_map(fn($c) => "`$c` = ?", array_keys($data)));
        $conds = implode(' AND ', array_map(fn($c) => "`$c` = ?", array_keys($where)));
        $params = array_merge(array_values($data), array_values($where));
        $stmt = $this->query("UPDATE `$table` SET $sets WHERE $conds", $params);
        return $stmt->rowCount();
    }

    protected function softDelete(string $table, int $id): int {
        $stmt = $this->query(
            "UPDATE `$table` SET `deleted_at` = NOW() WHERE `id` = ? AND `deleted_at` IS NULL",
            [$id]
        );
        return $stmt->rowCount();
    }

    protected function logOperation(
        string $tabela,
        int $id,
        string $acao,
        mixed $antigos,
        mixed $novos,
        string $usuario = 'sistema'
    ): void {
        $this->insert('logs', [
            'tabela'        => $tabela,
            'registro_id'   => $id,
            'acao'          => $acao,
            'dados_antigos' => $antigos !== null ? json_encode($antigos, JSON_UNESCAPED_UNICODE) : null,
            'dados_novos'   => $novos   !== null ? json_encode($novos,   JSON_UNESCAPED_UNICODE) : null,
            'usuario'       => $usuario,
        ]);
    }
}
