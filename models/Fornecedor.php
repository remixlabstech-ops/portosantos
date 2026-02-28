<?php
/**
 * Model: Fornecedor
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class Fornecedor extends BaseModel
{
    protected string $table = 'fornecedores';

    /**
     * Cria novo fornecedor.
     *
     * @param array<string, mixed> $data
     */
    public function criar(array $data): int
    {
        return $this->insert([
            'nome'           => trim($data['nome']),
            'documento'      => trim($data['documento'] ?? ''),
            'conta_bancaria' => trim($data['conta_bancaria'] ?? ''),
            'empresa'        => (int) ($data['empresa'] ?? EMPRESA_PADRAO),
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Atualiza fornecedor existente.
     *
     * @param array<string, mixed> $data
     */
    public function atualizar(int $id, array $data): bool
    {
        return $this->update($id, [
            'nome'           => trim($data['nome']),
            'documento'      => trim($data['documento'] ?? ''),
            'conta_bancaria' => trim($data['conta_bancaria'] ?? ''),
        ]);
    }

    /**
     * Busca fornecedores por nome ou documento (autocomplete AJAX).
     */
    public function buscar(string $termo): array
    {
        $like = '%' . $termo . '%';
        $stmt = $this->db->prepare(
            "SELECT id, nome, documento, conta_bancaria
             FROM fornecedores
             WHERE deleted_at IS NULL
               AND (nome LIKE ? OR documento LIKE ?)
             ORDER BY nome
             LIMIT 10"
        );
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }
}
