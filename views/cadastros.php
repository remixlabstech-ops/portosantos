<?php
/**
 * View: Cadastros (Categorias)
 * Porto Santos - Sistema ERP Jurídico
 */
?>
<div class="page-header">
    <h1 class="page-title">Cadastros</h1>
</div>

<div class="tab-group">
    <a href="/cadastros.php?tab=clientes"     class="tab-link <?= ($_GET['tab'] ?? 'clientes') === 'clientes'      ? 'active' : '' ?>">Clientes</a>
    <a href="/cadastros.php?tab=fornecedores" class="tab-link <?= ($_GET['tab'] ?? '') === 'fornecedores' ? 'active' : '' ?>">Fornecedores</a>
    <a href="/cadastros.php?tab=centros"      class="tab-link <?= ($_GET['tab'] ?? '') === 'centros'      ? 'active' : '' ?>">Centros de Custo</a>
    <a href="/cadastros.php?tab=categorias"   class="tab-link <?= ($_GET['tab'] ?? '') === 'categorias'   ? 'active' : '' ?>">Categorias</a>
</div>

<?php
$tab = htmlspecialchars($_GET['tab'] ?? 'clientes', ENT_QUOTES, 'UTF-8');
switch ($tab) {
    case 'fornecedores':
        include __DIR__ . '/fornecedores.php';
        break;
    case 'centros':
        include __DIR__ . '/centros_custo.php';
        break;
    case 'categorias':
        // Exibe categorias inline
        ?>
        <div class="card" id="tabela-categorias">
            <p class="text-center text-muted">Carregando...</p>
        </div>
        <script>
        fetch('/api/api_categorias.php?action=listar')
            .then(r => r.json())
            .then(json => {
                const container = document.getElementById('tabela-categorias');
                if (!json.success || !json.data.data.length) {
                    container.innerHTML = '<p class="text-center text-muted">Nenhuma categoria cadastrada.</p>';
                    return;
                }
                let html = '<table class="data-table"><thead><tr><th>#</th><th>Tipo</th><th>Nome</th></tr></thead><tbody>';
                json.data.data.forEach(c => {
                    html += `<tr><td>${c.id}</td><td>${c.tipo}</td><td>${c.nome}</td></tr>`;
                });
                html += '</tbody></table>';
                container.innerHTML = html;
            });
        </script>
        <?php
        break;
    default:
        include __DIR__ . '/clientes.php';
}
?>
