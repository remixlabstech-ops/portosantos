<?php $currentPage = $_GET['page'] ?? 'dashboard'; ?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">PS</div>
        <div class="sidebar-brand">
            <span class="brand-name">Porto Santos</span>
            <span class="brand-sub">Advocacia</span>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="?page=dashboard" class="nav-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="?page=entradas" class="nav-item <?= $currentPage === 'entradas' ? 'active' : '' ?>">
            <i class="fas fa-arrow-down nav-icon-green"></i>
            <span>Entradas</span>
        </a>
        <a href="?page=saidas" class="nav-item <?= $currentPage === 'saidas' ? 'active' : '' ?>">
            <i class="fas fa-arrow-up nav-icon-red"></i>
            <span>Saídas</span>
        </a>
        <a href="?page=inadimplencia" class="nav-item <?= $currentPage === 'inadimplencia' ? 'active' : '' ?>">
            <i class="fas fa-exclamation-triangle nav-icon-orange"></i>
            <span>Inadimplência</span>
        </a>
        <div class="nav-divider"></div>
        <a href="?page=clientes" class="nav-item <?= $currentPage === 'clientes' ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>Clientes</span>
        </a>
        <a href="?page=fornecedores" class="nav-item <?= $currentPage === 'fornecedores' ? 'active' : '' ?>">
            <i class="fas fa-truck"></i>
            <span>Fornecedores</span>
        </a>
        <a href="?page=centros_custo" class="nav-item <?= $currentPage === 'centros_custo' ? 'active' : '' ?>">
            <i class="fas fa-building"></i>
            <span>Centros de Custo</span>
        </a>
        <a href="?page=categorias" class="nav-item <?= $currentPage === 'categorias' ? 'active' : '' ?>">
            <i class="fas fa-tags"></i>
            <span>Categorias</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <i class="fas fa-balance-scale"></i>
        <span>v1.0.0</span>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
