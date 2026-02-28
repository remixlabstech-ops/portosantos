<?php
/**
 * View: Saídas (Despesas)
 * Porto Santos - Sistema ERP Jurídico
 *
 * @var array  $saidas
 * @var int    $total
 * @var int    $pages
 * @var int    $page
 * @var array  $filtros
 * @var array  $categorias
 * @var array  $centrosCusto
 */
?>
<div class="page-header">
    <h1 class="page-title">Saídas — Despesas</h1>
    <button class="btn btn-primary" onclick="abrirModalSaida()">
        <i data-lucide="plus"></i> Nova Saída
    </button>
</div>

<!-- Filtros -->
<div class="card filters-card">
    <form method="GET" class="filters-form">
        <div class="form-row">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">Todos</option>
                    <option value="Pendente" <?= ($filtros['status'] ?? '') === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="Pago"     <?= ($filtros['status'] ?? '') === 'Pago'     ? 'selected' : '' ?>>Pago</option>
                    <option value="Atrasado" <?= ($filtros['status'] ?? '') === 'Atrasado' ? 'selected' : '' ?>>Atrasado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Data Início</label>
                <input type="date" name="data_inicio" class="form-control"
                       value="<?= htmlspecialchars($filtros['data_inicio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Data Fim</label>
                <input type="date" name="data_fim" class="form-control"
                       value="<?= htmlspecialchars($filtros['data_fim'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group form-group-actions">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="/saidas.php" class="btn btn-secondary">Limpar</a>
            </div>
        </div>
    </form>
</div>

<!-- Tabela -->
<div class="card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fornecedor</th>
                    <th>Categoria</th>
                    <th>Centro de Custo</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saidas as $s): ?>
                <tr>
                    <td><?= (int) $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['fornecedor_nome'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($s['categoria'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($s['centro_custo_nome'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>R$ <?= number_format((float) $s['valor'], 2, ',', '.') ?></td>
                    <td><?= !empty($s['data_vencimento']) ? date('d/m/Y', strtotime($s['data_vencimento'])) : '—' ?></td>
                    <td>
                        <span class="badge badge-<?= strtolower($s['status']) ?>">
                            <?= htmlspecialchars($s['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <button class="btn btn-sm btn-success"
                                onclick="marcarPago('saidas', <?= (int) $s['id'] ?>)"
                                title="Marcar como Pago">
                            <i data-lucide="check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger"
                                onclick="confirmarDelete('saidas', <?= (int) $s['id'] ?>)"
                                title="Remover">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($saidas)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">Nenhuma saída encontrada.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=<?= $i ?>" class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Nova Saída -->
<div id="modal-saida" class="modal" style="display:none" role="dialog" aria-modal="true" aria-labelledby="modal-saida-title">
    <div class="modal-backdrop" onclick="fecharModal('modal-saida')"></div>
    <div class="modal-dialog">
        <div class="modal-header">
            <h2 id="modal-saida-title">Nova Saída</h2>
            <button class="btn-icon" onclick="fecharModal('modal-saida')" aria-label="Fechar">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-saida" novalidate>
                <!-- Fornecedor -->
                <div class="form-group">
                    <label for="saida-fornecedor-busca">Fornecedor</label>
                    <input type="text" id="saida-fornecedor-busca" class="form-control"
                           placeholder="Digite para buscar..." autocomplete="off">
                    <input type="hidden" id="saida-fornecedor-id" name="fornecedor_id">
                    <div id="saida-fornecedor-sugestoes" class="autocomplete-dropdown"></div>
                    <button type="button" class="btn btn-link btn-sm" onclick="abrirModalFornecedor()">
                        + Novo fornecedor
                    </button>
                </div>
                <!-- Categoria -->
                <div class="form-group">
                    <label for="saida-categoria">Categoria</label>
                    <input type="text" id="saida-categoria" name="categoria" class="form-control"
                           list="lista-categorias-saida" placeholder="Ex: Aluguel, Telefonia...">
                    <datalist id="lista-categorias-saida">
                        <?php foreach ($categorias as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['nome'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <!-- Centro de Custo -->
                <div class="form-group">
                    <label for="saida-centro-custo">Centro de Custo</label>
                    <select id="saida-centro-custo" name="centro_custo_id" class="form-control">
                        <option value="">Selecione...</option>
                        <?php foreach ($centrosCusto as $cc): ?>
                        <option value="<?= (int) $cc['id'] ?>"><?= htmlspecialchars($cc['nome'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Taxa -->
                <div class="form-group">
                    <label>Tipo de Taxa</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="tipo_taxa" value="valor_fixo" checked
                                   onchange="onChangeTipoTaxa(this.value)"> Valor Fixo
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="tipo_taxa" value="percentual"
                                   onchange="onChangeTipoTaxa(this.value)"> Percentual
                        </label>
                    </div>
                </div>
                <div id="grupo-taxa-perc" style="display:none">
                    <div class="form-group">
                        <label for="saida-valor-base">Valor Base</label>
                        <input type="number" id="saida-valor-base" name="valor_base"
                               class="form-control" step="0.01" min="0"
                               oninput="calcularValorSaida()">
                    </div>
                    <div class="form-group">
                        <label for="saida-taxa-valor">Taxa (%)</label>
                        <input type="number" id="saida-taxa-valor" name="taxa_valor"
                               class="form-control" step="0.01" min="0" max="100"
                               oninput="calcularValorSaida()">
                    </div>
                </div>
                <!-- Valor -->
                <div class="form-group">
                    <label for="saida-valor">Valor (R$) <span class="required">*</span></label>
                    <input type="number" id="saida-valor" name="valor"
                           class="form-control" step="0.01" min="0.01" required>
                </div>
                <!-- Data de Vencimento -->
                <div class="form-group">
                    <label for="saida-data-vencimento">Data de Vencimento <span class="required">*</span></label>
                    <input type="date" id="saida-data-vencimento" name="data_vencimento"
                           class="form-control" required>
                </div>
                <!-- Parcelas -->
                <div class="form-group">
                    <label for="saida-parcelas">Número de Parcelas</label>
                    <input type="number" id="saida-parcelas" name="parcelas"
                           class="form-control" min="1" max="12" value="1">
                </div>
                <!-- Rateio -->
                <div class="form-group">
                    <label>Rateio</label>
                    <div id="rateio-container">
                        <div class="rateio-item">
                            <select name="rateio_tipo[]" class="form-control form-control-sm">
                                <option value="administrativo">Administrativo</option>
                                <option value="cliente">Cliente Específico</option>
                            </select>
                            <input type="number" name="rateio_perc[]" placeholder="%"
                                   class="form-control form-control-sm" min="0" max="100" value="100">
                        </div>
                    </div>
                    <button type="button" class="btn btn-link btn-sm" onclick="adicionarRateio()">+ Adicionar</button>
                    <span id="rateio-soma-aviso" class="text-danger" style="display:none">
                        Soma dos rateios deve ser 100%
                    </span>
                </div>
                <!-- Observações -->
                <div class="form-group">
                    <label for="saida-obs">Observações</label>
                    <textarea id="saida-obs" name="observacoes" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer-actions">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modal-saida')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/saidas.js"></script>
