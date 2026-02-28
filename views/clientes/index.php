<?php $pageScript = 'clientes.js'; ?>

<div class="action-bar">
    <button class="btn btn-primary" id="btn-novo-cliente">
        <i class="fas fa-user-plus"></i> Novo Cliente
    </button>
</div>

<div class="table-responsive" id="tabela-clientes">
    <div class="loading-spinner"></div>
</div>

<!-- Modal -->
<div class="modal-backdrop" id="modal-cliente" style="display:none">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modal-cliente-title"><i class="fas fa-user-plus"></i> Novo Cliente</h3>
            <button class="modal-close" id="fechar-modal-cliente">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-cliente">
                <input type="hidden" id="cliente-id" value="0">
                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" id="cliente-nome" class="form-control" required maxlength="120">
                </div>
                <div class="form-row">
                    <div class="form-group form-col-6">
                        <label>CPF</label>
                        <input type="text" id="cliente-cpf" class="form-control" placeholder="000.000.000-00" maxlength="20">
                    </div>
                    <div class="form-group form-col-6">
                        <label>Telefone</label>
                        <input type="text" id="cliente-telefone" class="form-control" maxlength="30">
                    </div>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" id="cliente-email" class="form-control" maxlength="120">
                </div>
                <div class="form-group">
                    <label>Endereço</label>
                    <input type="text" id="cliente-endereco" class="form-control" maxlength="255">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelar-modal-cliente">Cancelar</button>
            <button class="btn btn-primary" id="salvar-cliente">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </div>
</div>
