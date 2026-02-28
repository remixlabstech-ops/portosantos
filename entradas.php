<?php
/**
 * Entradas (Honorários)
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/controllers/EntradasController.php';

$controller = new EntradasController();
$controller->index();
