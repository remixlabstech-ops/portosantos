<?php
/**
 * Inadimplência Controller
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Entrada.php';
require_once __DIR__ . '/../models/Database.php';

class InadimplenciaController extends BaseController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Página de inadimplência.
     */
    public function index(): void
    {
        $this->render('inadimplencia', [
            'faixas' => $this->obterFaixas(),
        ]);
    }

    /**
     * Retorna faixas de inadimplência para exibição no indicador.
     */
    private function obterFaixas(): array
    {
        $faixasDias = [5, 10, 15, 20, 30, 45, 60, 90, 120, 180];
        $resultado  = [];

        foreach ($faixasDias as $idx => $dias) {
            $proxima = $faixasDias[$idx + 1] ?? 999999;

            $stmt = $this->db->prepare(
                "SELECT COUNT(*) AS quantidade, COALESCE(SUM(valor), 0) AS valor_total
                 FROM entradas
                 WHERE deleted_at IS NULL
                   AND status != ?
                   AND data_vencimento < CURDATE()
                   AND DATEDIFF(CURDATE(), data_vencimento) >= ?
                   AND DATEDIFF(CURDATE(), data_vencimento) < ?"
            );
            $stmt->execute([STATUS_PAGO, $dias, $proxima]);
            $row = $stmt->fetch();

            $resultado[] = [
                'faixa'       => "{$dias} dias",
                'quantidade'  => (int) $row['quantidade'],
                'valor_total' => (float) $row['valor_total'],
            ];
        }

        return $resultado;
    }
}
