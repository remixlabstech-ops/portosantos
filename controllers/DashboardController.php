<?php
/**
 * Dashboard Controller
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Entrada.php';
require_once __DIR__ . '/../models/Saida.php';

class DashboardController extends BaseController
{
    private Entrada $entrada;
    private Saida $saida;

    public function __construct()
    {
        $this->entrada = new Entrada();
        $this->saida   = new Saida();
    }

    /**
     * Página principal do dashboard.
     */
    public function index(): void
    {
        $resumoEntradas = $this->entrada->resumoMes();
        $resumoSaidas   = $this->saida->resumoMes();

        $totalEntradas  = (float) ($resumoEntradas['total_mes'] ?? 0);
        $totalSaidas    = (float) ($resumoSaidas['total_mes'] ?? 0);
        $lucroLiquido   = $totalEntradas - $totalSaidas;

        $totalReceber   = (float) ($resumoEntradas['total_pendente'] ?? 0);
        $totalPagar     = (float) ($resumoSaidas['total_pendente'] ?? 0);

        // Variação % mês anterior
        $entAnt  = (float) ($resumoEntradas['total_mes_anterior'] ?? 0);
        $saidAnt = (float) ($resumoSaidas['total_mes_anterior'] ?? 0);
        $varEntradas = $entAnt > 0 ? round((($totalEntradas - $entAnt) / $entAnt) * 100, 1) : 0;
        $varSaidas   = $saidAnt > 0 ? round((($totalSaidas - $saidAnt) / $saidAnt) * 100, 1) : 0;

        $this->render('dashboard', [
            'totalEntradas'  => $totalEntradas,
            'totalSaidas'    => $totalSaidas,
            'lucroLiquido'   => $lucroLiquido,
            'totalReceber'   => $totalReceber,
            'totalPagar'     => $totalPagar,
            'varEntradas'    => $varEntradas,
            'varSaidas'      => $varSaidas,
            'topClientes'    => $this->entrada->topClientes(),
            'topCentros'     => $this->saida->topCentrosCusto(),
            'dadosMensaisEntradas' => $this->entrada->dadosMensais(),
            'dadosMensaisSaidas'   => $this->saida->dadosMensais(),
        ]);
    }
}
