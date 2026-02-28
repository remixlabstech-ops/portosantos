<?php
/**
 * View: Fornecedores CRUD
 * Porto Santos - Sistema ERP Jurídico
 *
 * @var array $fornecedores
 * @var int   $pages
 * @var int   $page
 */
?>
<div id="fornecedores-section">
    <div class="section-header">
        <h3>Fornecedores</h3>
        <button class="btn btn-primary btn-sm" onclick="abrirModalFornecedorCrud()">
            <i data-lucide="plus"></i> Novo
        </button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th><th>Nome</th><th>Documento</th><th>Conta Bancária</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores ?? [] as $f): ?>
                <tr>
                    <td><?= (int) $f['id'] ?></td>
                    <td><?= htmlspecialchars($f['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($f['documento'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($f['conta_bancaria'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="actions-cell">
                        <button class="btn btn-sm btn-secondary"
                                onclick="editarFornecedor(<?= (int) $f['id'] ?>)">
                            <i data-lucide="pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger"
                                onclick="confirmarDelete('fornecedores', <?= (int) $f['id'] ?>)">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($fornecedores)): ?>
                <tr><td colspan="5" class="text-center text-muted">Nenhum fornecedor cadastrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
