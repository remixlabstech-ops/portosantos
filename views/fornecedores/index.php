<?php $pageScript = 'fornecedores.js'; ?>

<div class="action-bar">
    <button class="btn btn-primary" id="btn-novo-fornecedor">
        <i class="fas fa-plus"></i> Novo Fornecedor
    </button>
</div>

<div class="table-responsive" id="tabela-fornecedores">
    <div class="loading-spinner"></div>
</div>

<!-- Modal -->
<div class="modal-backdrop" id="modal-fornecedor" style="display:none">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-fornecedor-title"><i class="fas fa-truck"></i> Novo Fornecedor</h3>
            <button class="modal-close" id="fechar-modal-fornecedor">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-fornecedor">
                <input type="hidden" id="fornecedor-id" value="0">
                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" id="fornecedor-nome" class="form-control" required maxlength="120">
                </div>
                <div class="form-row">
                    <div class="form-group form-col-6">
                        <label>CNPJ</label>
                        <input type="text" id="fornecedor-cnpj" class="form-control" placeholder="00.000.000/0000-00" maxlength="20">
                    </div>
                    <div class="form-group form-col-6">
                        <label>Telefone</label>
                        <input type="text" id="fornecedor-telefone" class="form-control" maxlength="30">
                    </div>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" id="fornecedor-email" class="form-control" maxlength="120">
                </div>
                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" id="fornecedor-endereco" class="form-control" maxlength="255">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelar-modal-fornecedor">Cancelar</button>
            <button class="btn btn-primary" id="salvar-fornecedor">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </div>
</div>
