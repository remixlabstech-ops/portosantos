<?php
/**
 * API: Clientes
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json; charset=UTF-8');

$cliente = new Cliente();
$log     = new Log();
$action  = $_GET['action'] ?? '';
$method  = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'listar':
            $page      = max(1, (int) ($_GET['page'] ?? 1));
            $resultado = $cliente->paginate($page);
            Response::success($resultado);
            break;

        case 'buscar':
            $termo = htmlspecialchars(trim($_GET['q'] ?? ''), ENT_QUOTES, 'UTF-8');
            if (strlen($termo) < 2) {
                Response::error('Termo de busca muito curto');
            }
            Response::success($cliente->buscar($termo));
            break;

        case 'obter':
            $id  = (int) ($_GET['id'] ?? 0);
            $row = $cliente->findById($id);
            if (!$row) {
                Response::error('Cliente não encontrado', 404);
            }
            Response::success($row);
            break;

        case 'criar':
            if ($method !== 'POST') {
                Response::error('Método não permitido', 405);
            }
            $data = Response::input();
            if (empty($data['nome'])) {
                Response::error('Nome é obrigatório');
            }
            $id = $cliente->criar($data);
            $log->registrar('INSERT', 'clientes', $id, null, $data);
            Response::success(['id' => $id], 'Cliente criado com sucesso', 201);
            break;

        case 'atualizar':
            if ($method !== 'PUT' && $method !== 'POST') {
                Response::error('Método não permitido', 405);
            }
            $data = Response::input();
            $id   = (int) ($data['id'] ?? 0);
            if (!$id || empty($data['nome'])) {
                Response::error('ID e nome são obrigatórios');
            }
            $antes = $cliente->findById($id);
            $cliente->atualizar($id, $data);
            $log->registrar('UPDATE', 'clientes', $id, $antes, $data);
            Response::success(null, 'Cliente atualizado com sucesso');
            break;

        case 'deletar':
            $id    = (int) ($_GET['id'] ?? 0);
            $antes = $cliente->findById($id);
            if (!$antes) {
                Response::error('Cliente não encontrado', 404);
            }
            $cliente->delete($id);
            $log->registrar('DELETE', 'clientes', $id, $antes, null);
            Response::success(null, 'Cliente removido com sucesso');
            break;

        default:
            Response::error('Ação inválida', 400);
    }
} catch (PDOException $e) {
    Response::error('Erro de banco de dados: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Erro interno: ' . $e->getMessage(), 500);
}
