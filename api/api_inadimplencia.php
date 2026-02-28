<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../controllers/InadimplenciaController.php';

$controller = new InadimplenciaController();
$action     = $_GET['action'] ?? 'listar';

switch ($action) {
    case 'listar':
        // Cast tipo_filtro to int or 'todos' - prevents SQL injection
        $filtros = [];
        $tf = $_GET['tipo_filtro'] ?? 'todos';
        $filtros['tipo_filtro'] = ($tf === 'todos') ? 'todos' : (int)$tf;
        echo json_encode($controller->listar($filtros));
        break;
    case 'faixas':
        echo json_encode($controller->faixas());
        break;
    case 'ranking':
        echo json_encode($controller->rankingInadimplentes());
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
