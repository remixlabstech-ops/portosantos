<?php $pageScript = 'dashboard.js'; ?>
<div class="dashboard-grid">

    <!-- Summary Cards -->
    <div class="cards-row" id="cards-resumo">
        <div class="card card-green">
            <div class="card-icon"><i class="fas fa-arrow-down"></i></div>
            <div class="card-body">
                <span class="card-label">Entradas do Mês</span>
                <span class="card-value" id="card-entradas">R$ 0,00</span>
            </div>
        </div>
        <div class="card card-red">
            <div class="card-icon"><i class="fas fa-arrow-up"></i></div>
            <div class="card-body">
                <span class="card-label">Saídas do Mês</span>
                <span class="card-value" id="card-saidas">R$ 0,00</span>
            </div>
        </div>
        <div class="card card-blue">
            <div class="card-icon"><i class="fas fa-chart-line"></i></div>
            <div class="card-body">
                <span class="card-label">Lucro Líquido</span>
                <span class="card-value" id="card-lucro">R$ 0,00</span>
            </div>
        </div>
        <div class="card card-teal">
            <div class="card-icon"><i class="fas fa-clock"></i></div>
            <div class="card-body">
                <span class="card-label">A Receber</span>
                <span class="card-value" id="card-receber">R$ 0,00</span>
            </div>
        </div>
        <div class="card card-orange">
            <div class="card-icon"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="card-body">
                <span class="card-label">A Pagar</span>
                <span class="card-value" id="card-pagar">R$ 0,00</span>
            </div>
        </div>
        <div class="card card-danger">
            <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="card-body">
                <span class="card-label">Inadimplência</span>
                <span class="card-value" id="card-inadimplencia">R$ 0,00</span>
                <span class="card-sub" id="card-inadimplencia-qtd">0 registros</span>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="charts-row">
        <div class="chart-card">
            <div class="chart-card-header">
                <h3><i class="fas fa-chart-line"></i> Fluxo Mensal <?= date('Y') ?></h3>
            </div>
            <div class="chart-container">
                <canvas id="chartMensal"></canvas>
            </div>
        </div>
        <div class="chart-card chart-card-sm">
            <div class="chart-card-header">
                <h3><i class="fas fa-pie-chart"></i> Por Área Jurídica</h3>
            </div>
            <div class="chart-container">
                <canvas id="chartArea"></canvas>
            </div>
        </div>
    </div>

    <!-- Rankings Row -->
    <div class="tables-row">
        <div class="table-card">
            <div class="table-card-header">
                <h3><i class="fas fa-trophy"></i> Top 5 Clientes</h3>
            </div>
            <div id="ranking-clientes">
                <div class="loading-spinner"></div>
            </div>
        </div>
        <div class="table-card">
            <div class="table-card-header">
                <h3><i class="fas fa-building"></i> Top 5 Centros de Custo</h3>
            </div>
            <div id="ranking-centros">
                <div class="loading-spinner"></div>
            </div>
        </div>
    </div>

</div>
