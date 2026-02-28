<?php
/**
 * API: Saídas (Despesas)
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../models/Saida.php';
require_once __DIR__ . '/../models/Rateio.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json; charset=UTF-8');

$saida  = new Saida();
$log    = new Log();
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'listar':
            $filtros = [
                'fornecedor_id' => $_GET['fornecedor_id'] ?? null,
                'status'        => $_GET['status'] ?? null,
                'data_inicio'   => $_GET['data_inicio'] ?? null,
                'data_fim'      => $_GET['data_fim'] ?? null,
            ];
            $page      = max(1, (int) ($_GET['page'] ?? 1));
            $resultado = $saida->listar(array_filter($filtros), $page);
            Response::success($resultado);
            break;

        case 'obter':
            $id  = (int) ($_GET['id'] ?? 0);
            $row = $saida->findById($id);
            if (!$row) {
                Response::error('Saída não encontrada', 404);
            }
            Response::success($row);
            break;

        case 'criar':
            if ($method !== 'POST') {
                Response::error('Método não permitido', 405);
            }
            $data    = Response::input();
            $rateios = $data['rateios'] ?? [];
            unset($data['rateios']);

            if (empty($data['valor']) || (float) $data['valor'] <= 0) {
                Response::error('Valor deve ser maior que zero');
            }
            if (empty($data['data_vencimento'])) {
                Response::error('Data de vencimento é obrigatória');
            }

            // Validação rateios (se houver)
            if (!empty($rateios) && !Rateio::validarSoma($rateios)) {
                Response::error('A soma dos rateios deve ser 100%');
            }

            // Cálculo de taxa
            if ($data['tipo_taxa'] === 'percentual' && !empty($data['taxa_valor']) && !empty($data['valor_base'])) {
                $data['valor'] = round(((float) $data['valor_base'] * (float) $data['taxa_valor']) / 100, 2);
            }

            $parcelas = max(1, min(12, (int) ($data['parcelas'] ?? 1)));
            $id       = $saida->criar($data, $parcelas, $rateios);
            $log->registrar('INSERT', 'saidas', $id, null, $data);
            Response::success(['id' => $id], 'Saída criada com sucesso', 201);
            break;

        case 'atualizar_status':
            $data   = Response::input();
            $id     = (int) ($data['id'] ?? 0);
            $status = $data['status'] ?? '';
            if (!$id || !$status) {
                Response::error('ID e status são obrigatórios');
            }
            $antes = $saida->findById($id);
            if (!$antes) {
                Response::error('Saída não encontrada', 404);
            }
            if (!$saida->atualizarStatus($id, $status)) {
                Response::error('Status inválido');
            }
            $log->registrar('UPDATE', 'saidas', $id, $antes, ['status' => $status]);
            Response::success(null, 'Status atualizado com sucesso');
            break;

        case 'deletar':
            $id    = (int) ($_GET['id'] ?? 0);
            $antes = $saida->findById($id);
            if (!$antes) {
                Response::error('Saída não encontrada', 404);
            }
            $saida->delete($id);
            $log->registrar('DELETE', 'saidas', $id, $antes, null);
            Response::success(null, 'Saída removida com sucesso');
            break;

        case 'resumo_mes':
            Response::success($saida->resumoMes());
            break;

        default:
            Response::error('Ação inválida', 400);
    }
} catch (PDOException $e) {
    Response::error('Erro de banco de dados: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Erro interno: ' . $e->getMessage(), 500);
}
