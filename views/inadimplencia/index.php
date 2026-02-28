<?php $pageScript = 'inadimplencia.js'; ?>

<!-- Summary Cards -->
<div class="cards-row" id="cards-inadimplencia">
    <div class="card card-danger">
        <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="card-body">
            <span class="card-label">Total Inadimplente</span>
            <span class="card-value" id="total-inadimplente">R$ 0,00</span>
        </div>
    </div>
    <div class="card card-orange">
        <div class="card-icon"><i class="fas fa-list-ol"></i></div>
        <div class="card-body">
            <span class="card-label">Qtd. Registros</span>
            <span class="card-value" id="qtd-inadimplente">0</span>
        </div>
    </div>
    <div class="card card-red">
        <div class="card-icon"><i class="fas fa-arrow-up"></i></div>
        <div class="card-body">
            <span class="card-label">Maior Inadimplência</span>
            <span class="card-value" id="maior-inadimplente">R$ 0,00</span>
        </div>
    </div>
</div>

<!-- Filter Buttons -->
<div class="filter-dias">
    <span class="filter-label"><i class="fas fa-filter"></i> Filtrar por:</span>
    <div class="filter-dias-btns" id="filtro-dias-btns">
        <button class="btn-dias active" data-dias="0">Todos</button>
        <button class="btn-dias" data-dias="5">5 dias</button>
        <button class="btn-dias" data-dias="10">10 dias</button>
        <button class="btn-dias" data-dias="15">15 dias</button>
        <button class="btn-dias" data-dias="20">20 dias</button>
        <button class="btn-dias" data-dias="30">30 dias</button>
        <button class="btn-dias" data-dias="45">45 dias</button>
        <button class="btn-dias" data-dias="60">60 dias</button>
        <button class="btn-dias" data-dias="90">90 dias</button>
        <button class="btn-dias" data-dias="120">120 dias</button>
        <button class="btn-dias" data-dias="180">180 dias</button>
    </div>
    <div class="filter-personalizado">
        <label>Personalizado:</label>
        <input type="number" id="dias-personalizado" class="form-control form-control-sm" min="1" max="3650" placeholder="dias">
        <button class="btn btn-sm btn-primary" id="btn-dias-personalizado">Aplicar</button>
    </div>
</div>

<!-- Table -->
<div class="table-responsive" id="tabela-inadimplencia">
    <div class="loading-spinner"></div>
</div>

<!-- Faixas -->
<div id="tabela-faixas-inadimplencia" class="mt-4"></div>

<!-- Ranking -->
<div id="ranking-inadimplentes" class="mt-4"></div>
