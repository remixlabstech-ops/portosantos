<?php
/**
 * Saídas (Despesas)
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/controllers/SaidasController.php';

$controller = new SaidasController();
$controller->index();
