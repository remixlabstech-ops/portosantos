<?php
/**
 * View: Entradas (Honorários)
 * Porto Santos - Sistema ERP Jurídico
 *
 * @var array  $entradas
 * @var int    $total
 * @var int    $pages
 * @var int    $page
 * @var array  $filtros
 * @var array  $categorias
 */
?>
<div class="page-header">
    <h1 class="page-title">Entradas — Honorários</h1>
    <button class="btn btn-primary" onclick="abrirModalEntrada()">
        <i data-lucide="plus"></i> Nova Entrada
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
            <div class="form-group">
                <label>Valor Mín</label>
                <input type="number" name="valor_min" class="form-control" step="0.01"
                       value="<?= htmlspecialchars($filtros['valor_min'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
                <label>Valor Máx</label>
                <input type="number" name="valor_max" class="form-control" step="0.01"
                       value="<?= htmlspecialchars($filtros['valor_max'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group form-group-actions">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <a href="/entradas.php" class="btn btn-secondary">Limpar</a>
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
                    <th>Cliente</th>
                    <th>Categoria</th>
                    <th>Tipo Honorário</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entradas as $e): ?>
                <tr>
                    <td><?= (int) $e['id'] ?></td>
                    <td><?= htmlspecialchars($e['cliente_nome'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['categoria'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($e['tipo_honorario'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>R$ <?= number_format((float) $e['valor'], 2, ',', '.') ?></td>
                    <td><?= !empty($e['data_vencimento']) ? date('d/m/Y', strtotime($e['data_vencimento'])) : '—' ?></td>
                    <td>
                        <span class="badge badge-<?= strtolower($e['status']) ?>">
                            <?= htmlspecialchars($e['status'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </td>
                    <td class="actions-cell">
                        <button class="btn btn-sm btn-success"
                                onclick="marcarPago('entradas', <?= (int) $e['id'] ?>)"
                                title="Marcar como Pago">
                            <i data-lucide="check"></i>
                        </button>
                        <button class="btn btn-sm btn-danger"
                                onclick="confirmarDelete('entradas', <?= (int) $e['id'] ?>)"
                                title="Remover">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($entradas)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">Nenhuma entrada encontrada.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <!-- Paginação -->
    <?php if ($pages > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?page=<?= $i ?>&status=<?= urlencode($filtros['status'] ?? '') ?>"
           class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Nova Entrada -->
<div id="modal-entrada" class="modal" style="display:none" role="dialog" aria-modal="true" aria-labelledby="modal-entrada-title">
    <div class="modal-backdrop" onclick="fecharModal('modal-entrada')"></div>
    <div class="modal-dialog">
        <div class="modal-header">
            <h2 id="modal-entrada-title">Nova Entrada</h2>
            <button class="btn-icon" onclick="fecharModal('modal-entrada')" aria-label="Fechar">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-entrada" novalidate>
                <!-- Busca de cliente -->
                <div class="form-group">
                    <label for="entrada-cliente-busca">Cliente <span class="required">*</span></label>
                    <input type="text" id="entrada-cliente-busca" class="form-control"
                           placeholder="Digite para buscar..." autocomplete="off">
                    <input type="hidden" id="entrada-cliente-id" name="cliente_id">
                    <div id="entrada-cliente-sugestoes" class="autocomplete-dropdown"></div>
                    <button type="button" class="btn btn-link btn-sm" onclick="abrirModalCliente()">
                        + Novo cliente
                    </button>
                </div>
                <!-- Categoria -->
                <div class="form-group">
                    <label for="entrada-categoria">Categoria <span class="required">*</span></label>
                    <select id="entrada-categoria" name="categoria" class="form-control" required>
                        <option value="">Selecione...</option>
                        <?php foreach (CATEGORIAS_JURIDICAS as $cat): ?>
                        <option value="<?= htmlspecialchars($cat, ENT_QUOTES, 'UTF-8') ?>"><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Tipo de Honorário -->
                <div class="form-group">
                    <label for="entrada-tipo-honorario">Tipo de Honorário <span class="required">*</span></label>
                    <select id="entrada-tipo-honorario" name="tipo_honorario" class="form-control"
                            onchange="onChangeTipoHonorario(this.value)" required>
                        <option value="">Selecione...</option>
                        <?php foreach (TIPOS_HONORARIO as $tipo): ?>
                        <option value="<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>"><?= $tipo ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- Valor da Causa + Percentual (exibido condicionalmente) -->
                <div id="grupo-causa-perc" style="display:none">
                    <div class="form-group">
                        <label for="entrada-valor-causa">Valor da Causa</label>
                        <input type="number" id="entrada-valor-causa" name="valor_causa"
                               class="form-control" step="0.01" min="0"
                               oninput="calcularValorHonorario()">
                    </div>
                    <div class="form-group">
                        <label for="entrada-percentual">Percentual (%)</label>
                        <input type="number" id="entrada-percentual" name="percentual"
                               class="form-control" step="0.01" min="0" max="100"
                               oninput="calcularValorHonorario()">
                    </div>
                </div>
                <!-- Valor -->
                <div class="form-group">
                    <label for="entrada-valor">Valor (R$) <span class="required">*</span></label>
                    <input type="number" id="entrada-valor" name="valor"
                           class="form-control" step="0.01" min="0.01" required>
                </div>
                <!-- Data de Vencimento -->
                <div class="form-group">
                    <label for="entrada-data-vencimento">Data de Vencimento <span class="required">*</span></label>
                    <input type="date" id="entrada-data-vencimento" name="data_vencimento"
                           class="form-control" required>
                </div>
                <!-- Parcelas -->
                <div class="form-group">
                    <label for="entrada-parcelas">Número de Parcelas</label>
                    <input type="number" id="entrada-parcelas" name="parcelas"
                           class="form-control" min="1" max="12" value="1"
                           oninput="previewParcelas()">
                </div>
                <div id="preview-parcelas"></div>
                <!-- Comprovante -->
                <div class="form-group">
                    <label for="entrada-comprovante">Comprovante (PDF, máx 5 MB)</label>
                    <input type="file" id="entrada-comprovante" name="comprovante"
                           class="form-control" accept="application/pdf">
                </div>
                <!-- Observações -->
                <div class="form-group">
                    <label for="entrada-obs">Observações</label>
                    <textarea id="entrada-obs" name="observacoes" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer-actions">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modal-entrada')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cadastro Rápido de Cliente -->
<div id="modal-cliente" class="modal" style="display:none" role="dialog" aria-modal="true" aria-labelledby="modal-cliente-title">
    <div class="modal-backdrop" onclick="fecharModal('modal-cliente')"></div>
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h2 id="modal-cliente-title">Novo Cliente</h2>
            <button class="btn-icon" onclick="fecharModal('modal-cliente')" aria-label="Fechar">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-cliente-rapido" novalidate>
                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" name="nome" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" class="form-control" maxlength="14">
                </div>
                <div class="form-group">
                    <label>Número do Processo</label>
                    <input type="text" name="processo" class="form-control">
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" name="telefone" class="form-control">
                </div>
                <div class="modal-footer-actions">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modal-cliente')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/assets/js/entradas.js"></script>
