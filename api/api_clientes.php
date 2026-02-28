<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../controllers/ClienteController.php';

$controller = new ClienteController();
$action     = $_GET['action'] ?? '';

switch ($action) {
    case 'listar':
        echo json_encode($controller->index($_GET));
        break;
    case 'buscar':
        $termo = trim($_GET['q'] ?? '');
        echo json_encode($controller->search($termo));
        break;
    case 'obter':
        $id = (int)($_GET['id'] ?? 0);
        echo json_encode($controller->show($id));
        break;
    case 'criar':
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        echo json_encode($controller->store($data));
        break;
    case 'atualizar':
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $id   = (int)($_GET['id'] ?? $data['id'] ?? 0);
        echo json_encode($controller->update($id, $data));
        break;
    case 'deletar':
        $id = (int)($_GET['id'] ?? 0);
        echo json_encode($controller->destroy($id));
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}
