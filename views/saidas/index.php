<?php $pageScript = 'saidas.js'; ?>

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
            <option value="Pago">Pago</option>
            <option value="Cancelado">Cancelado</option>
        </select>
    </div>
    <div class="filter-actions">
        <button class="btn btn-primary" id="btn-filtrar"><i class="fas fa-search"></i> Filtrar</button>
        <button class="btn btn-secondary" id="btn-limpar-filtro"><i class="fas fa-times"></i> Limpar</button>
    </div>
</div>

<!-- Action Bar -->
<div class="action-bar">
    <button class="btn btn-danger" id="btn-nova-saida">
        <i class="fas fa-plus"></i> Nova Saída
    </button>
    <a href="api/api_export.php?tipo=saidas" class="btn btn-secondary">
        <i class="fas fa-file-csv"></i> Exportar CSV
    </a>
</div>

<!-- Table -->
<div class="table-responsive" id="tabela-saidas">
    <div class="loading-spinner"></div>
</div>

<!-- Modal: Criar/Editar Saída -->
<div class="modal-backdrop" id="modal-saida" style="display:none">
    <div class="modal modal-lg">
        <div class="modal-header">
            <h3 id="modal-saida-title"><i class="fas fa-plus-circle"></i> Nova Saída</h3>
            <button class="modal-close" id="fechar-modal-saida">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-saida" enctype="multipart/form-data">
                <input type="hidden" id="saida-id" name="id" value="0">

                <div class="form-row">
                    <div class="form-group form-col-8">
                        <label>Fornecedor</label>
                        <div class="autocomplete-wrapper">
                            <input type="text" id="saida-fornecedor-nome" class="form-control" placeholder="Digite para buscar fornecedor..." autocomplete="off">
                            <input type="hidden" id="saida-fornecedor-id" name="fornecedor_id">
                            <div class="autocomplete-dropdown" id="autocomplete-fornecedores"></div>
                        </div>
                    </div>
                    <div class="form-group form-col-4">
                        <label>Data <span class="required">*</span></label>
                        <input type="date" id="saida-data" name="data_saida" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col-6">
                        <label>Categoria <span class="required">*</span></label>
                        <select id="saida-categoria" name="categoria_id" class="form-control" required>
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                    <div class="form-group form-col-6">
                        <label>Centro de Custo</label>
                        <select id="saida-centro-custo" name="centro_custo_id" class="form-control">
                            <option value="">Nenhum</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col-4">
                        <label>Valor <span class="required">*</span></label>
                        <input type="number" id="saida-valor" name="valor" class="form-control" step="0.01" min="0" required placeholder="0,00">
                    </div>
                    <div class="form-group form-col-4">
                        <label>Tipo de Taxa</label>
                        <select id="saida-tipo-taxa" name="tipo_taxa" class="form-control">
                            <option value="">Sem taxa</option>
                            <option value="percentual">Percentual (%)</option>
                            <option value="fixo">Fixo (R$)</option>
                        </select>
                    </div>
                    <div class="form-group form-col-4" id="row-taxa" style="display:none">
                        <label>Taxa</label>
                        <input type="number" id="saida-taxa" name="taxa" class="form-control" step="0.01" min="0" placeholder="0,00">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group form-col-4">
                        <label>Vencimento</label>
                        <input type="date" id="saida-vencimento" name="data_vencimento" class="form-control">
                    </div>
                    <div class="form-group form-col-4">
                        <label>Status</label>
                        <select id="saida-status" name="status" class="form-control">
                            <option value="Aberto">Aberto</option>
                            <option value="Pago">Pago</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="form-group form-col-4">
                        <label>Nº Parcelas</label>
                        <input type="number" id="saida-parcelas" name="num_parcelas" class="form-control" min="1" max="60" value="1">
                    </div>
                </div>

                <!-- Rateio -->
                <div class="form-group">
                    <label>Tipo de Rateio <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="tipo_rateio" value="administrativo" checked> Administrativo
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="tipo_rateio" value="cliente"> Um Cliente
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="tipo_rateio" value="multiplos"> Múltiplos Clientes
                        </label>
                    </div>
                </div>

                <div id="rateio-cliente" style="display:none" class="rateio-box">
                    <label>Cliente</label>
                    <div class="autocomplete-wrapper">
                        <input type="text" id="rateio-cliente-nome" class="form-control" placeholder="Buscar cliente..." autocomplete="off">
                        <input type="hidden" id="rateio-cliente-id">
                        <div class="autocomplete-dropdown" id="autocomplete-rateio-cliente"></div>
                    </div>
                </div>

                <div id="rateio-multiplos" style="display:none" class="rateio-box">
                    <div class="rateio-header">
                        <span>Distribuição de Rateio</span>
                        <span id="rateio-soma-display" class="rateio-soma">Soma: 0%</span>
                    </div>
                    <div id="rateio-rows"></div>
                    <button type="button" class="btn btn-sm btn-secondary" id="btn-add-rateio">
                        <i class="fas fa-plus"></i> Adicionar Cliente
                    </button>
                </div>

                <div class="form-group">
                    <label>Descrição <span class="required">*</span></label>
                    <textarea id="saida-descricao" name="descricao" class="form-control" rows="3" required></textarea>
                </div>

                <div class="form-group">
                    <label>Comprovante (PDF)</label>
                    <input type="file" id="saida-comprovante" name="comprovante" class="form-control" accept=".pdf">
                    <small class="form-hint">Somente arquivos PDF são aceitos.</small>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancelar-modal-saida">Cancelar</button>
            <button class="btn btn-danger" id="salvar-saida">
                <i class="fas fa-save"></i> Salvar
            </button>
        </div>
    </div>
</div>
