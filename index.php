<?php
session_start();

$page       = $_GET['page'] ?? 'dashboard';
$validPages = ['dashboard','entradas','saidas','clientes','fornecedores','centros_custo','categorias','inadimplencia'];

if (!in_array($page, $validPages, true)) {
    $page = 'dashboard';
}

$pageTitle = match ($page) {
    'dashboard'    => 'Dashboard',
    'entradas'     => 'Entradas',
    'saidas'       => 'Saídas',
    'clientes'     => 'Clientes',
    'fornecedores' => 'Fornecedores',
    'centros_custo'=> 'Centros de Custo',
    'categorias'   => 'Categorias',
    'inadimplencia'=> 'Inadimplência',
    default        => 'Dashboard',
};

include __DIR__ . '/views/layout/header.php';
include __DIR__ . "/views/{$page}/index.php";
include __DIR__ . '/views/layout/footer.php';
