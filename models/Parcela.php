<?php
/**
 * Model: Parcela
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseModel.php';

class Parcela extends BaseModel
{
    protected string $table = 'parcelas';

    /**
     * Lista parcelas de uma entrada.
     */
    public function listarPorEntrada(int $entradaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM parcelas WHERE entrada_id = ? AND deleted_at IS NULL ORDER BY numero"
        );
        $stmt->execute([$entradaId]);
        return $stmt->fetchAll();
    }

    /**
     * Lista parcelas de uma saída.
     */
    public function listarPorSaida(int $saidaId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM parcelas WHERE saida_id = ? AND deleted_at IS NULL ORDER BY numero"
        );
        $stmt->execute([$saidaId]);
        return $stmt->fetchAll();
    }

    /**
     * Atualiza status de uma parcela.
     */
    public function atualizarStatus(int $id, string $status): bool
    {
        $allowed = [STATUS_PAGO, STATUS_PENDENTE, STATUS_ATRASADO];
        if (!in_array($status, $allowed)) {
            return false;
        }
        return $this->update($id, ['status' => $status]);
    }
}
