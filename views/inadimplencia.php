<?php
/**
 * View: Inadimplência
 * Porto Santos - Sistema ERP Jurídico
 *
 * @var array $faixas
 */
?>
<div class="page-header">
    <h1 class="page-title">Inadimplência</h1>
</div>

<!-- Indicadores Rápidos (preenchidos via JS) -->
<div class="cards-grid" id="indicadores-inadimplencia">
    <div class="card kpi-card kpi-red">
        <div class="kpi-icon"><i data-lucide="alert-circle"></i></div>
        <div class="kpi-body">
            <span class="kpi-label">Total em Atraso</span>
            <span class="kpi-value" id="ind-total-valor">—</span>
        </div>
    </div>
    <div class="card kpi-card kpi-orange">
        <div class="kpi-icon"><i data-lucide="clock"></i></div>
        <div class="kpi-body">
            <span class="kpi-label">Média de Dias em Atraso</span>
            <span class="kpi-value" id="ind-media-dias">—</span>
        </div>
    </div>
    <div class="card kpi-card kpi-red">
        <div class="kpi-icon"><i data-lucide="calendar-x"></i></div>
        <div class="kpi-body">
            <span class="kpi-label">Maior Atraso</span>
            <span class="kpi-value" id="ind-maior-atraso">—</span>
        </div>
    </div>
</div>

<!-- Filtros por faixa -->
<div class="card filters-card">
    <div class="faixas-btn-group">
        <button class="btn btn-outline faixa-btn active" data-dias="todos" onclick="filtrarInadimplencia('todos', this)">Todos</button>
        <?php foreach ([5,10,15,20,30,45,60,90,120,180] as $dias): ?>
        <button class="btn btn-outline faixa-btn" data-dias="<?= $dias ?>"
                onclick="filtrarInadimplencia(<?= $dias ?>, this)"><?= $dias ?>d</button>
        <?php endforeach; ?>
    </div>
    <div class="form-row" style="margin-top:0.75rem">
        <div class="form-group">
            <label>Personalizado - Data Início</label>
            <input type="date" id="filtro-data-inicio" class="form-control">
        </div>
        <div class="form-group">
            <label>Data Fim</label>
            <input type="date" id="filtro-data-fim" class="form-control">
        </div>
        <div class="form-group form-group-actions">
            <button class="btn btn-primary" onclick="filtrarPersonalizado()">Filtrar</button>
            <button class="btn btn-secondary" onclick="limparFiltros()">Limpar</button>
        </div>
    </div>
</div>

<!-- Tabela Inadimplência -->
<div class="card" id="tabela-inadimplencia">
    <p class="text-center text-muted">Carregando...</p>
</div>

<!-- Tabela Faixas -->
<div class="card" id="tabela-faixas-inadimplencia"></div>

<!-- Modal Renegociação -->
<div id="modal-renegociar" class="modal" style="display:none" role="dialog" aria-modal="true" aria-labelledby="modal-reneg-title">
    <div class="modal-backdrop" onclick="fecharModal('modal-renegociar')"></div>
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h2 id="modal-reneg-title">Renegociar</h2>
            <button class="btn-icon" onclick="fecharModal('modal-renegociar')" aria-label="Fechar">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-renegociar">
                <input type="hidden" id="reneg-id" name="id">
                <div class="form-group">
                    <label>Nova Data de Vencimento</label>
                    <input type="date" id="reneg-nova-data" name="nova_data_vencimento" class="form-control" required>
                </div>
                <div class="modal-footer-actions">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modal-renegociar')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/inadimplencia.js"></script>
<script>
    // Substitui inadimplencia.js legado pelo novo
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof carregarInadimplencia === 'function') carregarInadimplencia('todos');
    });
</script>
