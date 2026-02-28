<?php
/**
 * Model: Rateio
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class Rateio extends BaseModel
{
    protected string $table = 'rateios';

    /**
     * Lista rateios de uma saída.
     */
    public function listarPorSaida(int $saidaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT r.*, c.nome AS cliente_nome, cc.nome AS centro_custo_nome
             FROM rateios r
             LEFT JOIN clientes c ON r.cliente_id = c.id
             LEFT JOIN centros_custo cc ON r.centro_custo_id = cc.id
             WHERE r.saida_id = ? AND r.deleted_at IS NULL"
        );
        $stmt->execute([$saidaId]);
        return $stmt->fetchAll();
    }

    /**
     * Valida que a soma dos rateios é 100%.
     *
     * @param array<array<string, mixed>> $rateios
     */
    public static function validarSoma(array $rateios): bool
    {
        $total = 0.0;
        foreach ($rateios as $r) {
            $total += (float) ($r['percentual_divisao'] ?? 0);
        }
        return abs($total - 100.0) < 0.01;
    }
}
