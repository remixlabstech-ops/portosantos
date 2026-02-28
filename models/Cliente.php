<?php
/**
 * Model: Cliente
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class Cliente extends BaseModel
{
    protected string $table = 'clientes';

    /**
     * Cria novo cliente.
     *
     * @param array<string, mixed> $data
     */
    public function criar(array $data): int
    {
        return $this->insert([
            'nome'      => trim($data['nome']),
            'cpf'       => trim($data['cpf'] ?? ''),
            'processo'  => trim($data['processo'] ?? ''),
            'email'     => trim($data['email'] ?? ''),
            'telefone'  => trim($data['telefone'] ?? ''),
            'empresa'   => (int) ($data['empresa'] ?? EMPRESA_PADRAO),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Atualiza cliente existente.
     *
     * @param array<string, mixed> $data
     */
    public function atualizar(int $id, array $data): bool
    {
        return $this->update($id, [
            'nome'     => trim($data['nome']),
            'cpf'      => trim($data['cpf'] ?? ''),
            'processo' => trim($data['processo'] ?? ''),
            'email'    => trim($data['email'] ?? ''),
            'telefone' => trim($data['telefone'] ?? ''),
        ]);
    }

    /**
     * Busca clientes por nome ou CPF (autocomplete AJAX).
     */
    public function buscar(string $termo): array
    {
        $like = '%' . $termo . '%';
        $stmt = $this->db->prepare(
            "SELECT id, nome, cpf, email, processo
             FROM clientes
             WHERE deleted_at IS NULL
               AND (nome LIKE ? OR cpf LIKE ?)
             ORDER BY nome
             LIMIT 10"
        );
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }
}
