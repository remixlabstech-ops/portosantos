<?php $pageScript = 'categorias.js'; ?>

<div class="two-col-layout">

    <!-- Receita -->
    <div class="section-card">
        <div class="section-card-header">
            <h3><i class="fas fa-arrow-down nav-icon-green"></i> Categorias de Receita</h3>
            <button class="btn btn-sm btn-success" id="btn-nova-receita">
                <i class="fas fa-plus"></i> Nova
            </button>
        </div>
        <div id="tabela-receita">
            <div class="loading-spinner"></div>
        </div>
    </div>

    <!-- Despesa -->
    <div class="section-card">
        <div class="section-card-header">
            <h3><i class="fas fa-arrow-up nav-icon-red"></i> Categorias de Despesa</h3>
            <button class="btn btn-sm btn-danger" id="btn-nova-despesa">
                <i class="fas fa-plus"></i> Nova
            </button>
        </div>
        <div id="tabela-despesa">
            <div class="loading-spinner"></div>
        </div>
    </div>

</div>

<!-- Modal Receita -->
<div class="modal-backdrop" id="modal-receita" style="display:none">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-receita-title">Nova Categoria de Receita</h3>
            <button class="modal-close" id="fechar-modal-receita">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-receita">
                <input type="hidden" id="receita-id" value="0">
                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" id="receita-nome" class="form-control" required maxlength="80">
                </div>
                <div class="form-group">
                    <label>Tipo (Área Jurídica) <span class="required">*</span></label>
                    <select id="receita-tipo" class="form-control" required>
                        <option value="Cível">Cível</option>
                        <option value="Trabalhista">Trabalhista</option>
                        <option value="Previdenciário">Previdenciário</option>
                        <option value="Criminal">Criminal</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelar-modal-receita">Cancelar</button>
            <button class="btn btn-success" id="salvar-receita"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </div>
</div>

<!-- Modal Despesa -->
<div class="modal-backdrop" id="modal-despesa" style="display:none">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-despesa-title">Nova Categoria de Despesa</h3>
            <button class="modal-close" id="fechar-modal-despesa">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-despesa">
                <input type="hidden" id="despesa-id" value="0">
                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" id="despesa-nome" class="form-control" required maxlength="80">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelar-modal-despesa">Cancelar</button>
            <button class="btn btn-danger" id="salvar-despesa"><i class="fas fa-save"></i> Salvar</button>
        </div>
    </div>
</div>
