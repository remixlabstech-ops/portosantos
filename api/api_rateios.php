<?php
/**
 * API: Rateios
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../models/Rateio.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json; charset=UTF-8');

$rateio = new Rateio();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'listar':
            $saidaId = (int) ($_GET['saida_id'] ?? 0);
            if (!$saidaId) {
                Response::error('saida_id é obrigatório');
            }
            Response::success($rateio->listarPorSaida($saidaId));
            break;

        case 'validar':
            $data    = Response::input();
            $rateios = $data['rateios'] ?? [];
            $valido  = Rateio::validarSoma($rateios);
            Response::success(['valido' => $valido]);
            break;

        default:
            Response::error('Ação inválida', 400);
    }
} catch (PDOException $e) {
    Response::error('Erro de banco de dados: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Erro interno: ' . $e->getMessage(), 500);
}
