<?php
/**
 * Model: CentroCusto
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class CentroCusto extends BaseModel
{
    protected string $table = 'centros_custo';

    /**
     * Cria novo centro de custo.
     *
     * @param array<string, mixed> $data
     */
    public function criar(array $data): int
    {
        return $this->insert([
            'nome'       => trim($data['nome']),
            'descricao'  => trim($data['descricao'] ?? ''),
            'empresa'    => (int) ($data['empresa'] ?? EMPRESA_PADRAO),
            'ativo'      => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Atualiza centro de custo.
     *
     * @param array<string, mixed> $data
     */
    public function atualizar(int $id, array $data): bool
    {
        return $this->update($id, [
            'nome'      => trim($data['nome']),
            'descricao' => trim($data['descricao'] ?? ''),
            'ativo'     => (int) ($data['ativo'] ?? 1),
        ]);
    }

    /**
     * Lista centros de custo ativos.
     */
    public function listarAtivos(): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, nome, descricao FROM centros_custo
             WHERE deleted_at IS NULL AND ativo = 1
             ORDER BY nome"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
