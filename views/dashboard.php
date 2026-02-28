<?php
/**
 * View: Dashboard
 * Porto Santos - Sistema ERP Jurídico
 *
 * @var float $totalEntradas
 * @var float $totalSaidas
 * @var float $lucroLiquido
 * @var float $totalReceber
 * @var float $totalPagar
 * @var float $varEntradas
 * @var float $varSaidas
 * @var array $topClientes
 * @var array $topCentros
 * @var array $dadosMensaisEntradas
 * @var array $dadosMensaisSaidas
 */
?>
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

<!-- KPI Cards -->
<div class="cards-grid">
    <div class="card kpi-card kpi-green">
        <div class="kpi-icon"><i data-lucide="trending-up"></i></div>
        <div class="kpi-body">
            <span class="kpi-label">Entradas do Mês</span>
            <span class="kpi-value"><?= formatBRL($totalEntradas) ?></span>
            <span class="kpi-var <?= $varEntradas >= 0 ? 'pos' : 'neg' ?>">
                <?= $varEntradas >= 0 ? '+' : '' ?><?= $varEntradas ?>% vs mês anterior
            </span>
        </div>
    </div>
    <div class="card kpi-card kpi-red">
        <div class="kpi-icon"><i data-lucide="trending-down"></i></div>
        <div class="kpi-body">
            <span class="kpi-label">Saídas do Mês</span>
            <span class="kpi-value"><?= formatBRL($totalSaidas) ?></span>
            <span class="kpi-var <?= $varSaidas <= 0 ? 'pos' : 'neg' ?>">
                <?= $varSaidas >= 0 ? '+' : '' ?><?= $varSaidas ?>% vs mês anterior
            </span>
        </div>
    </div>
    <div class="card kpi-card <?= $lucroLiquido >= 0 ? 'kpi-green' : 'kpi-red' ?>">
        <div class="kpi-icon"><i data-lucide="dollar-sign"></i></div>
        <div class="kpi-body">
            <span class="kpi-label">Lucro Líquido</span>
            <span class="kpi-value"><?= formatBRL($lucroLiquido) ?></span>
        </div>
    </div>
    <div class="card kpi-card kpi-blue">
        <div class="kpi-icon"><i data-lucide="clock"></i></div>
        <div class="kpi-body">
            <span class="kpi-label">A Receber</span>
            <span class="kpi-value"><?= formatBRL($totalReceber) ?></span>
        </div>
    </div>
    <div class="card kpi-card kpi-orange">
        <div class="kpi-icon"><i data-lucide="credit-card"></i></div>
        <div class="kpi-body">
            <span class="kpi-label">A Pagar</span>
            <span class="kpi-value"><?= formatBRL($totalPagar) ?></span>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="charts-row">
    <div class="card chart-card">
        <h3 class="card-title">Entradas vs Saídas (últimos 12 meses)</h3>
        <canvas id="chartMensal" height="120"></canvas>
    </div>
</div>

<!-- Rankings -->
<div class="rankings-row">
    <div class="card">
        <h3 class="card-title">Top 5 Clientes Mais Rentáveis</h3>
        <table class="table-mini">
            <thead><tr><th>Cliente</th><th>Total Pago</th></tr></thead>
            <tbody>
                <?php foreach ($topClientes as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= formatBRL((float) $c['total']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($topClientes)): ?>
                <tr><td colspan="2" class="text-center text-muted">Nenhum dado disponível</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card">
        <h3 class="card-title">Top 5 Centros de Custo</h3>
        <table class="table-mini">
            <thead><tr><th>Centro</th><th>Total</th></tr></thead>
            <tbody>
                <?php foreach ($topCentros as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= formatBRL((float) $c['total']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($topCentros)): ?>
                <tr><td colspan="2" class="text-center text-muted">Nenhum dado disponível</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
(function() {
    const entradasData = <?= json_encode($dadosMensaisEntradas, JSON_UNESCAPED_UNICODE) ?>;
    const saidasData   = <?= json_encode($dadosMensaisSaidas, JSON_UNESCAPED_UNICODE) ?>;

    // Monta labels unificados
    const mesesSet = new Set([
        ...entradasData.map(d => d.mes),
        ...saidasData.map(d => d.mes),
    ]);
    const labels = [...mesesSet].sort();
    const entMap = Object.fromEntries(entradasData.map(d => [d.mes, parseFloat(d.total)]));
    const saidMap = Object.fromEntries(saidasData.map(d => [d.mes, parseFloat(d.total)]));

    new Chart(document.getElementById('chartMensal'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Entradas',
                    data: labels.map(m => entMap[m] || 0),
                    backgroundColor: 'rgba(39, 174, 96, 0.7)',
                },
                {
                    label: 'Saídas',
                    data: labels.map(m => saidMap[m] || 0),
                    backgroundColor: 'rgba(231, 76, 60, 0.7)',
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } },
            scales: {
                y: {
                    ticks: {
                        callback: v => 'R$ ' + v.toLocaleString('pt-BR'),
                    },
                },
            },
        },
    });
})();
</script>

<?php
/**
 * Formata valor em BRL para exibição nas views.
 */
function formatBRL(float $val): string {
    return 'R$ ' . number_format($val, 2, ',', '.');
}
?>
