<?php
/**
 * API: Entradas (Honorários)
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../models/Entrada.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json; charset=UTF-8');

$entrada = new Entrada();
$log     = new Log();
$action  = $_GET['action'] ?? '';
$method  = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'listar':
            $filtros = [
                'cliente_id'  => $_GET['cliente_id'] ?? null,
                'status'      => $_GET['status'] ?? null,
                'data_inicio' => $_GET['data_inicio'] ?? null,
                'data_fim'    => $_GET['data_fim'] ?? null,
                'valor_min'   => $_GET['valor_min'] ?? null,
                'valor_max'   => $_GET['valor_max'] ?? null,
            ];
            $page      = max(1, (int) ($_GET['page'] ?? 1));
            $resultado = $entrada->listar(array_filter($filtros), $page);
            Response::success($resultado);
            break;

        case 'obter':
            $id  = (int) ($_GET['id'] ?? 0);
            $row = $entrada->findById($id);
            if (!$row) {
                Response::error('Entrada não encontrada', 404);
            }
            Response::success($row);
            break;

        case 'criar':
            if ($method !== 'POST') {
                Response::error('Método não permitido', 405);
            }
            $data = Response::input();

            // Validações obrigatórias
            if (empty($data['cliente_id'])) {
                Response::error('Cliente é obrigatório');
            }
            if (empty($data['valor']) || (float) $data['valor'] <= 0) {
                Response::error('Valor deve ser maior que zero');
            }
            if (empty($data['data_vencimento'])) {
                Response::error('Data de vencimento é obrigatória');
            }

            // Validação de tipo de honorário
            $tiposPermitidos = TIPOS_HONORARIO;
            if (!empty($data['tipo_honorario']) && !in_array($data['tipo_honorario'], $tiposPermitidos)) {
                Response::error('Tipo de honorário inválido');
            }

            // Cálculo automático para Sucumbência/Êxito
            if (in_array($data['tipo_honorario'] ?? '', ['Sucumbência', 'Êxito'])) {
                if (empty($data['valor_causa']) || empty($data['percentual'])) {
                    Response::error('Valor da causa e percentual são obrigatórios para este tipo');
                }
                $data['valor'] = round(((float) $data['valor_causa'] * (float) $data['percentual']) / 100, 2);
            }

            $parcelas = max(1, min(12, (int) ($data['parcelas'] ?? 1)));
            $id       = $entrada->criar($data, $parcelas);

            // Upload de comprovante
            if (!empty($_FILES['comprovante'])) {
                $caminho = salvarComprovante($_FILES['comprovante']);
                if ($caminho) {
                    $entrada->salvarComprovante($id, $caminho);
                }
            }

            $log->registrar('INSERT', 'entradas', $id, null, $data);
            Response::success(['id' => $id], 'Entrada criada com sucesso', 201);
            break;

        case 'atualizar_status':
            if ($method !== 'POST' && $method !== 'PUT') {
                Response::error('Método não permitido', 405);
            }
            $data   = Response::input();
            $id     = (int) ($data['id'] ?? 0);
            $status = $data['status'] ?? '';
            if (!$id || !$status) {
                Response::error('ID e status são obrigatórios');
            }
            $antes = $entrada->findById($id);
            if (!$antes) {
                Response::error('Entrada não encontrada', 404);
            }
            if (!$entrada->atualizarStatus($id, $status)) {
                Response::error('Status inválido');
            }
            $log->registrar('UPDATE', 'entradas', $id, $antes, ['status' => $status]);
            Response::success(null, 'Status atualizado com sucesso');
            break;

        case 'deletar':
            $id    = (int) ($_GET['id'] ?? 0);
            $antes = $entrada->findById($id);
            if (!$antes) {
                Response::error('Entrada não encontrada', 404);
            }
            $entrada->delete($id);
            $log->registrar('DELETE', 'entradas', $id, $antes, null);
            Response::success(null, 'Entrada removida com sucesso');
            break;

        case 'resumo_mes':
            Response::success($entrada->resumoMes());
            break;

        case 'dados_mensais':
            Response::success($entrada->dadosMensais());
            break;

        default:
            Response::error('Ação inválida', 400);
    }
} catch (PDOException $e) {
    Response::error('Erro de banco de dados: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Erro interno: ' . $e->getMessage(), 500);
}

/**
 * Salva comprovante PDF no diretório de uploads.
 *
 * @param array<string, mixed> $file $_FILES entry
 */
function salvarComprovante(array $file): string|false
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        return false;
    }
    // Valida MIME type via magic bytes (finfo lê o conteúdo do arquivo)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    if (!in_array($mimeType, UPLOAD_ALLOWED_TYPES)) {
        return false;
    }
    // Verificação adicional de magic bytes PDF (%PDF-)
    $handle = fopen($file['tmp_name'], 'rb');
    if ($handle === false) {
        return false;
    }
    $magic = fread($handle, 5);
    fclose($handle);
    if ($magic !== '%PDF-') {
        return false;
    }
    $nome    = time() . '_' . bin2hex(random_bytes(8)) . '.pdf';
    $destino = UPLOAD_DIR . $nome;
    if (!move_uploaded_file($file['tmp_name'], $destino)) {
        return false;
    }
    return $nome;
}
