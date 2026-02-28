<?php
/**
 * Dashboard
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/controllers/DashboardController.php';

$controller = new DashboardController();
$controller->index();
