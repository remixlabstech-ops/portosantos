<?php
/**
 * API: Centros de Custo
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../models/CentroCusto.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json; charset=UTF-8');

$cc     = new CentroCusto();
$log    = new Log();
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'listar':
            $page      = max(1, (int) ($_GET['page'] ?? 1));
            $resultado = $cc->paginate($page);
            Response::success($resultado);
            break;

        case 'listar_ativos':
            Response::success($cc->listarAtivos());
            break;

        case 'criar':
            if ($method !== 'POST') {
                Response::error('Método não permitido', 405);
            }
            $data = Response::input();
            if (empty($data['nome'])) {
                Response::error('Nome é obrigatório');
            }
            $id = $cc->criar($data);
            $log->registrar('INSERT', 'centros_custo', $id, null, $data);
            Response::success(['id' => $id], 'Centro de custo criado', 201);
            break;

        case 'atualizar':
            $data = Response::input();
            $id   = (int) ($data['id'] ?? 0);
            if (!$id || empty($data['nome'])) {
                Response::error('ID e nome são obrigatórios');
            }
            $antes = $cc->findById($id);
            $cc->atualizar($id, $data);
            $log->registrar('UPDATE', 'centros_custo', $id, $antes, $data);
            Response::success(null, 'Centro de custo atualizado');
            break;

        case 'deletar':
            $id    = (int) ($_GET['id'] ?? 0);
            $antes = $cc->findById($id);
            if (!$antes) {
                Response::error('Centro de custo não encontrado', 404);
            }
            $cc->delete($id);
            $log->registrar('DELETE', 'centros_custo', $id, $antes, null);
            Response::success(null, 'Centro de custo removido');
            break;

        default:
            Response::error('Ação inválida', 400);
    }
} catch (PDOException $e) {
    Response::error('Erro de banco de dados: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Erro interno: ' . $e->getMessage(), 500);
}
