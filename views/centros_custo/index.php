<?php $pageScript = 'centros_custo.js'; ?>

<div class="action-bar">
    <button class="btn btn-primary" id="btn-novo-centro">
        <i class="fas fa-plus"></i> Novo Centro de Custo
    </button>
</div>

<div class="table-responsive" id="tabela-centros">
    <div class="loading-spinner"></div>
</div>

<!-- Modal -->
<div class="modal-backdrop" id="modal-centro" style="display:none">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-centro-title"><i class="fas fa-building"></i> Novo Centro de Custo</h3>
            <button class="modal-close" id="fechar-modal-centro">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-centro">
                <input type="hidden" id="centro-id" value="0">
                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" id="centro-nome" class="form-control" required maxlength="80">
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <textarea id="centro-descricao" class="form-control" rows="3" maxlength="255"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelar-modal-centro">Cancelar</button>
            <button class="btn btn-primary" id="salvar-centro">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </div>
</div>
