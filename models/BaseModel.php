<?php
/**
 * Base Model - Funcionalidades comuns de CRUD
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../config/constants.php';

abstract class BaseModel
{
    protected PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
        // Validate table name against an allowlist to prevent SQL injection
        $allowed = [
            'clientes', 'fornecedores', 'centros_custo', 'categorias',
            'entradas', 'saidas', 'parcelas', 'rateios', 'logs',
        ];
        if ($this->table !== '' && !in_array($this->table, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid table name: {$this->table}");
        }
    }

    /**
     * Busca registro por ID (respeita soft delete).
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Lista registros com paginação (respeita soft delete).
     *
     * @return array{data: array, total: int, pages: int}
     */
    public function paginate(int $page = 1, int $perPage = ITEMS_PER_PAGE, array $where = []): array
    {
        $offset  = ($page - 1) * $perPage;
        $conds   = ['deleted_at IS NULL'];
        $params  = [];

        foreach ($where as $col => $val) {
            $conds[]  = "{$col} = ?";
            $params[] = $val;
        }

        $whereClause = implode(' AND ', $conds);

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$whereClause}");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $dataStmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?"
        );
        $dataStmt->execute(array_merge($params, [$perPage, $offset]));

        return [
            'data'  => $dataStmt->fetchAll(),
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Soft delete.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET deleted_at = NOW() WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute([$id]);
    }

    /**
     * Monta e executa INSERT genérico.
     *
     * @param array<string, mixed> $data
     */
    protected function insert(array $data): int
    {
        $cols   = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} ({$cols}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return (int) $this->db->lastInsertId();
    }

    /**
     * Monta e executa UPDATE genérico.
     *
     * @param array<string, mixed> $data
     */
    protected function update(int $id, array $data): bool
    {
        $sets  = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $stmt  = $this->db->prepare(
            "UPDATE {$this->table} SET {$sets}, updated_at = NOW() WHERE {$this->primaryKey} = ?"
        );
        return $stmt->execute(array_merge(array_values($data), [$id]));
    }
}
