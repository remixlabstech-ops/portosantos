<?php
/**
 * View: Centros de Custo CRUD
 * Porto Santos - Sistema ERP Jurídico
 *
 * @var array $centros
 * @var int   $pages
 * @var int   $page
 */
?>
<div id="centros-section">
    <div class="section-header">
        <h3>Centros de Custo</h3>
        <button class="btn btn-primary btn-sm" onclick="abrirModalCentroCrud()">
            <i data-lucide="plus"></i> Novo
        </button>
    </div>
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th><th>Nome</th><th>Descrição</th><th>Ativo</th><th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($centros ?? [] as $cc): ?>
                <tr>
                    <td><?= (int) $cc['id'] ?></td>
                    <td><?= htmlspecialchars($cc['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($cc['descricao'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $cc['ativo'] ? 'Sim' : 'Não' ?></td>
                    <td class="actions-cell">
                        <button class="btn btn-sm btn-danger"
                                onclick="confirmarDelete('centros_custo', <?= (int) $cc['id'] ?>)">
                            <i data-lucide="trash-2"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($centros)): ?>
                <tr><td colspan="5" class="text-center text-muted">Nenhum centro de custo cadastrado.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
