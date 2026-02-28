<?php
/**
 * Inadimplência
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/controllers/InadimplenciaController.php';

$controller = new InadimplenciaController();
$controller->index();
