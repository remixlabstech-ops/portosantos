<?php
/**
 * Layout: Sidebar
 * Porto Santos - Sistema ERP Jurídico
 */

$paginaAtual = basename($_SERVER['PHP_SELF'], '.php');
$menu = [
    ['href' => '/dashboard.php',    'icon' => 'layout-dashboard', 'label' => 'Dashboard',      'page' => 'dashboard'],
    ['href' => '/entradas.php',     'icon' => 'trending-up',      'label' => 'Entradas',        'page' => 'entradas'],
    ['href' => '/saidas.php',       'icon' => 'trending-down',    'label' => 'Saídas',          'page' => 'saidas'],
    ['href' => '/inadimplencia.php','icon' => 'alert-triangle',   'label' => 'Inadimplência',   'page' => 'inadimplencia'],
    ['href' => '/cadastros.php',    'icon' => 'database',         'label' => 'Cadastros',       'page' => 'cadastros'],
];
?>
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <?php foreach ($menu as $item): ?>
                <a href="<?= $item['href'] ?>"
                   class="nav-item <?= $paginaAtual === $item['page'] ? 'active' : '' ?>">
                    <i data-lucide="<?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <!-- Main Content -->
        <main class="main-content">
