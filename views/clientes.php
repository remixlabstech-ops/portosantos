<?php
/**
 * View: Clientes CRUD
 * Porto Santos - Sistema ERP Jurídico
 *
 * @var array $clientes
 * @var int   $total
 * @var int   $pages
 * @var int   $page
 */
?>
<div id="clientes-section">
    <div class="section-header">
        <h3>Clientes</h3>
        <button class="btn btn-primary btn-sm" onclick="abrirModalClienteCrud()">
            <i data-lucide="plus"></i> Novo
        </button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th><th>Nome</th><th>CPF</th><th>Processo</th><th>E-mail</th><th>Telefone</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes ?? [] as $c): ?>
                <tr>
                    <td><?= (int) $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['cpf'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['processo'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['email'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($c['telefone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="actions-cell">
                        <button class="btn btn-sm btn-secondary"
                                onclick="editarCliente(<?= (int) $c['id'] ?>)">
                            <i data-lucide="pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger"
                                onclick="confirmarDelete('clientes', <?= (int) $c['id'] ?>)">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($clientes)): ?>
                <tr><td colspan="7" class="text-center text-muted">Nenhum cliente cadastrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (($pages ?? 0) > 1): ?>
    <div class="pagination">
        <?php for ($i = 1; $i <= $pages; $i++): ?>
        <a href="?tab=clientes&page=<?= $i ?>" class="page-link <?= $i === ($page ?? 1) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Edição Cliente -->
<div id="modal-cliente-crud" class="modal" style="display:none" role="dialog" aria-modal="true" aria-labelledby="modal-cliente-crud-title">
    <div class="modal-backdrop" onclick="fecharModal('modal-cliente-crud')"></div>
    <div class="modal-dialog modal-sm">
        <div class="modal-header">
            <h2 id="modal-cliente-crud-title">Cliente</h2>
            <button class="btn-icon" onclick="fecharModal('modal-cliente-crud')" aria-label="Fechar">&times;</button>
        </div>
        <div class="modal-body">
            <form id="form-cliente-crud">
                <input type="hidden" name="id" id="crud-cliente-id">
                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" name="nome" id="crud-cliente-nome" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>CPF</label>
                    <input type="text" name="cpf" id="crud-cliente-cpf" class="form-control">
                </div>
                <div class="form-group">
                    <label>Processo</label>
                    <input type="text" name="processo" id="crud-cliente-processo" class="form-control">
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" id="crud-cliente-email" class="form-control">
                </div>
                <div class="form-group">
                    <label>Telefone</label>
                    <input type="tel" name="telefone" id="crud-cliente-telefone" class="form-control">
                </div>
                <div class="modal-footer-actions">
                    <button type="button" class="btn btn-secondary" onclick="fecharModal('modal-cliente-crud')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
