<?php
/**
 * Layout: Header
 * Porto Santos - Sistema ERP Jurídico
 */

$paginaAtual = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <link rel="stylesheet" href="/assets/css/responsive.css">
    <!-- Lucide Icons (pinned version) -->
    <script src="https://unpkg.com/lucide@0.294.0/dist/umd/lucide.min.js"
            crossorigin="anonymous"></script>
    <!-- Chart.js (pinned version) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"
            crossorigin="anonymous"></script>
</head>
<body>
<div class="app-wrapper">
    <!-- Header Topo -->
    <header class="app-header">
        <div class="header-left">
            <button id="sidebar-toggle" class="btn-icon" aria-label="Menu">
                <i data-lucide="menu"></i>
            </button>
            <a href="/dashboard.php" class="logo-link">
                <div class="logo-circle">PS</div>
                <span class="logo-text"><?= APP_NAME ?></span>
            </a>
        </div>
        <div class="header-right">
            <button id="theme-toggle" class="btn-icon" aria-label="Alternar tema" title="Alternar tema claro/escuro">
                <i data-lucide="sun" id="icon-sun"></i>
                <i data-lucide="moon" id="icon-moon" style="display:none"></i>
            </button>
            <span class="user-badge">
                <i data-lucide="user"></i>
                <span>Administrador</span>
            </span>
        </div>
    </header>
    <div class="app-body">
