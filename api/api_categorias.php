<?php
/**
 * API: Categorias
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json; charset=UTF-8');

$categoria = new Categoria();
$log       = new Log();
$action    = $_GET['action'] ?? '';
$method    = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'listar':
            $page      = max(1, (int) ($_GET['page'] ?? 1));
            $resultado = $categoria->paginate($page);
            Response::success($resultado);
            break;

        case 'listar_por_tipo':
            $tipo = $_GET['tipo'] ?? 'entrada';
            if (!in_array($tipo, ['entrada', 'saida'])) {
                Response::error('Tipo inválido');
            }
            Response::success($categoria->listarPorTipo($tipo));
            break;

        case 'criar':
            if ($method !== 'POST') {
                Response::error('Método não permitido', 405);
            }
            $data = Response::input();
            if (empty($data['nome'])) {
                Response::error('Nome é obrigatório');
            }
            $id = $categoria->criar($data);
            $log->registrar('INSERT', 'categorias', $id, null, $data);
            Response::success(['id' => $id], 'Categoria criada', 201);
            break;

        case 'deletar':
            $id    = (int) ($_GET['id'] ?? 0);
            $antes = $categoria->findById($id);
            if (!$antes) {
                Response::error('Categoria não encontrada', 404);
            }
            $categoria->delete($id);
            $log->registrar('DELETE', 'categorias', $id, $antes, null);
            Response::success(null, 'Categoria removida');
            break;

        default:
            Response::error('Ação inválida', 400);
    }
} catch (PDOException $e) {
    Response::error('Erro de banco de dados: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Erro interno: ' . $e->getMessage(), 500);
}
