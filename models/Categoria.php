<?php
/**
 * Model: Categoria
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class Categoria extends BaseModel
{
    protected string $table = 'categorias';

    /**
     * Cria nova categoria.
     *
     * @param array<string, mixed> $data
     */
    public function criar(array $data): int
    {
        $tipo = in_array($data['tipo'] ?? '', ['entrada', 'saida']) ? $data['tipo'] : 'entrada';
        return $this->insert([
            'tipo'       => $tipo,
            'nome'       => trim($data['nome']),
            'empresa'    => (int) ($data['empresa'] ?? EMPRESA_PADRAO),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Lista categorias por tipo.
     *
     * @param string $tipo 'entrada'|'saida'
     */
    public function listarPorTipo(string $tipo): array
    {
        $stmt = $this->db->prepare(
            "SELECT id, nome FROM categorias
             WHERE deleted_at IS NULL AND tipo = ?
             ORDER BY nome"
        );
        $stmt->execute([$tipo]);
        return $stmt->fetchAll();
    }
}
