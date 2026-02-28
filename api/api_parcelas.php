<?php
/**
 * API: Parcelas
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../models/Parcela.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json; charset=UTF-8');

$parcela = new Parcela();
$log     = new Log();
$action  = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'listar_entrada':
            $id = (int) ($_GET['entrada_id'] ?? 0);
            if (!$id) {
                Response::error('entrada_id é obrigatório');
            }
            Response::success($parcela->listarPorEntrada($id));
            break;

        case 'listar_saida':
            $id = (int) ($_GET['saida_id'] ?? 0);
            if (!$id) {
                Response::error('saida_id é obrigatório');
            }
            Response::success($parcela->listarPorSaida($id));
            break;

        case 'atualizar_status':
            $data   = Response::input();
            $id     = (int) ($data['id'] ?? 0);
            $status = $data['status'] ?? '';
            if (!$id || !$status) {
                Response::error('ID e status são obrigatórios');
            }
            $antes = $parcela->findById($id);
            if (!$antes) {
                Response::error('Parcela não encontrada', 404);
            }
            if (!$parcela->atualizarStatus($id, $status)) {
                Response::error('Status inválido');
            }
            $log->registrar('UPDATE', 'parcelas', $id, $antes, ['status' => $status]);
            Response::success(null, 'Status da parcela atualizado');
            break;

        default:
            Response::error('Ação inválida', 400);
    }
} catch (PDOException $e) {
    Response::error('Erro de banco de dados: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Erro interno: ' . $e->getMessage(), 500);
}
