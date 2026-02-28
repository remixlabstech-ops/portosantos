<?php $pageScript = 'entradas.js'; ?>

<!-- Filter Bar -->
<div class="filter-bar">
    <div class="filter-group">
        <label>Data início</label>
        <input type="date" id="filtro-data-inicio" class="form-control">
    </div>
    <div class="filter-group">
        <label>Data fim</label>
        <input type="date" id="filtro-data-fim" class="form-control">
    </div>
    <div class="filter-group">
        <label>Status</label>
        <select id="filtro-status" class="form-control">
            <option value="">Todos</option>
            <option value="Aberto">Aberto</option>
            <option value="Recebido">Recebido</option>
            <option value="Cancelado">Cancelado</option>
        </select>
    </div>
    <div class="filter-group filter-group-wide">
        <label>Valor mínimo</label>
        <input type="number" id="filtro-valor-min" class="form-control" placeholder="0,00" min="0" step="0.01">
    </div>
    <div class="filter-group filter-group-wide">
        <label>Valor máximo</label>
        <input type="number" id="filtro-valor-max" class="form-control" placeholder="0,00" min="0" step="0.01">
    </div>
    <div class="filter-actions">
        <button class="btn btn-primary" id="btn-filtrar"><i class="fas fa-search"></i> Filtrar</button>
        <button class="btn btn-secondary" id="btn-limpar-filtro"><i class="fas fa-times"></i> Limpar</button>
    </div>
</div>

<!-- Action Bar -->
<div class="action-bar">
    <button class="btn btn-success" id="btn-nova-entrada">
        <i class="fas fa-plus"></i> Nova Entrada
    </button>
    <a href="api/api_export.php?tipo=entradas" class="btn btn-secondary">
        <i class="fas fa-file-csv"></i> Exportar CSV
    </a>
</div>

<!-- Table -->
<div class="table-responsive" id="tabela-entradas">
    <div class="loading-spinner"></div>
</div>

<!-- Modal: Criar/Editar Entrada -->
<div class="modal-backdrop" id="modal-entrada" style="display:none">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="modal-entrada-title"><i class="fas fa-plus-circle"></i> Nova Entrada</h3>
            <button class="modal-close" id="fechar-modal-entrada">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-entrada" enctype="multipart/form-data">
                <input type="hidden" id="entrada-id" name="id" value="0">

                <div class="form-row">
                    <div class="form-group form-col-8">
                        <label for="entrada-cliente">Cliente <span class="required">*</span></label>
                        <div class="autocomplete-wrapper">
                            <input type="text" id="entrada-cliente-nome" class="form-control" placeholder="Digite para buscar cliente..." autocomplete="off">
                            <input type="hidden" id="entrada-cliente-id" name="cliente_id">
                            <div class="autocomplete-dropdown" id="autocomplete-clientes"></div>
                        </div>
                        <a href="#" id="btn-novo-cliente-rapido" class="link-small">
                            <i class="fas fa-plus"></i> Cadastrar novo cliente
                        </a>
                    </div>
                    <div class="form-group form-col-4">
                        <label for="entrada-data">Data <span class="required">*</span></label>
                        <input type="date" id="entrada-data" name="data_entrada" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col-6">
                        <label for="entrada-categoria">Categoria <span class="required">*</span></label>
                        <select id="entrada-categoria" name="categoria_id" class="form-control" required>
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                    <div class="form-group form-col-6">
                        <label for="entrada-tipo-honorario">Tipo de Honorário <span class="required">*</span></label>
                        <select id="entrada-tipo-honorario" name="tipo_honorario_id" class="form-control" required>
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                </div>

                <!-- Fields for Sucumbência / Êxito -->
                <div class="form-row" id="row-calculo-automatico" style="display:none">
                    <div class="form-group form-col-5">
                        <label for="entrada-valor-causa">Valor da Causa</label>
                        <input type="number" id="entrada-valor-causa" name="valor_causa" class="form-control" step="0.01" min="0" placeholder="0,00">
                    </div>
                    <div class="form-group form-col-3">
                        <label for="entrada-percentual">Percentual (%)</label>
                        <input type="number" id="entrada-percentual" name="percentual" class="form-control" step="0.01" min="0" max="100" placeholder="0,00">
                    </div>
                    <div class="form-group form-col-4">
                        <label>Valor Calculado</label>
                        <input type="text" id="entrada-valor-calculado-display" class="form-control" readonly>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col-4">
                        <label for="entrada-valor">Valor <span class="required">*</span></label>
                        <input type="number" id="entrada-valor" name="valor_entrada" class="form-control" step="0.01" min="0" required placeholder="0,00">
                    </div>
                    <div class="form-group form-col-4">
                        <label for="entrada-vencimento">Vencimento</label>
                        <input type="date" id="entrada-vencimento" name="data_vencimento" class="form-control">
                    </div>
                    <div class="form-group form-col-4">
                        <label for="entrada-status">Status</label>
                        <select id="entrada-status" name="status" class="form-control">
                            <option value="Aberto">Aberto</option>
                            <option value="Recebido">Recebido</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col-3">
                        <label for="entrada-parcelas">Nº Parcelas</label>
                        <input type="number" id="entrada-parcelas" name="num_parcelas" class="form-control" min="1" max="60" value="1">
                    </div>
                    <div class="form-group form-col-9">
                        <label>Prévia das Parcelas</label>
                        <div id="preview-parcelas" class="parcelas-preview"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="entrada-descricao">Descrição</label>
                    <textarea id="entrada-descricao" name="descricao" class="form-control" rows="3"></textarea>
                </div>

                <div class="form-group">
                    <label for="entrada-comprovante">Comprovante (PDF)</label>
                    <input type="file" id="entrada-comprovante" name="comprovante" class="form-control" accept=".pdf">
                    <small class="form-hint">Somente arquivos PDF são aceitos.</small>
                    <div id="comprovante-atual" style="display:none">
                        <a id="link-comprovante-atual" href="#" target="_blank">
                            <i class="fas fa-file-pdf"></i> Ver comprovante atual
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelar-modal-entrada">Cancelar</button>
            <button class="btn btn-success" id="salvar-entrada">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </div>
</div>

<!-- Modal: Novo Cliente Rápido -->
<div class="modal-backdrop" id="modal-cliente-rapido" style="display:none">
    <div class="modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-plus"></i> Novo Cliente</h3>
            <button class="modal-close" id="fechar-modal-cliente-rapido">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-cliente-rapido">
                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" id="cr-nome" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group form-col-6">
                        <label>CPF</label>
                        <input type="text" id="cr-cpf" class="form-control" placeholder="000.000.000-00">
                    </div>
                    <div class="form-group form-col-6">
                        <label>Telefone</label>
                        <input type="text" id="cr-telefone" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" id="cr-email" class="form-control">
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelar-cliente-rapido">Cancelar</button>
            <button class="btn btn-success" id="salvar-cliente-rapido">
                <i class="fas fa-save"></i> Salvar Cliente
            </button>
        </div>
    </div>
</div>
