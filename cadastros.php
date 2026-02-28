<?php
/**
 * Cadastros
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/controllers/CadastrosController.php';

$controller = new CadastrosController();
$tab = htmlspecialchars($_GET['tab'] ?? 'clientes', ENT_QUOTES, 'UTF-8');

switch ($tab) {
    case 'fornecedores':
        $controller->fornecedores();
        break;
    case 'centros':
        $controller->centrosCusto();
        break;
    case 'categorias':
        $controller->categorias();
        break;
    default:
        $controller->clientes();
}
