<?php
/**
 * API: Inadimplência
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../models/Entrada.php';
require_once __DIR__ . '/../models/Log.php';
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/Response.php';

header('Content-Type: application/json; charset=UTF-8');

$action = $_GET['action'] ?? 'listar';
$log    = new Log();

try {
    switch ($action) {
        case 'listar':
            $tipoFiltro = $_GET['tipo_filtro'] ?? '30';
            $db         = Database::getInstance();

            $conds  = [
                'e.deleted_at IS NULL',
                "e.status != '" . STATUS_PAGO . "'",
                'e.data_vencimento < CURDATE()',
            ];
            $params = [];

            if ($tipoFiltro !== 'todos' && is_numeric($tipoFiltro)) {
                $conds[]  = 'DATEDIFF(CURDATE(), e.data_vencimento) >= ?';
                $params[] = (int) $tipoFiltro;
            }

            $where = implode(' AND ', $conds);
            $stmt  = $db->prepare(
                "SELECT e.*, c.nome AS cliente_nome, c.cpf,
                        DATEDIFF(CURDATE(), e.data_vencimento) AS dias_vencido
                 FROM entradas e
                 LEFT JOIN clientes c ON e.cliente_id = c.id
                 WHERE {$where}
                 ORDER BY e.data_vencimento ASC"
            );
            $stmt->execute($params);
            $dados = $stmt->fetchAll();

            // Indicadores
            $totalValor    = array_sum(array_column($dados, 'valor'));
            $diasTodos     = array_column($dados, 'dias_vencido');
            $mediasDias    = count($diasTodos) > 0 ? round(array_sum($diasTodos) / count($diasTodos), 1) : 0;
            $maiorAtraso   = count($diasTodos) > 0 ? max($diasTodos) : 0;

            Response::success([
                'registros'    => $dados,
                'total_valor'  => $totalValor,
                'media_dias'   => $mediasDias,
                'maior_atraso' => $maiorAtraso,
            ]);
            break;

        case 'faixas':
            $db        = Database::getInstance();
            $faixas    = [5, 10, 15, 20, 30, 45, 60, 90, 120, 180];
            $resultado = [];

            foreach ($faixas as $idx => $dias) {
                $proxima = $faixas[$idx + 1] ?? 999999;
                $stmt    = $db->prepare(
                    "SELECT COUNT(*) AS quantidade, COALESCE(SUM(valor), 0) AS valor_total
                     FROM entradas
                     WHERE deleted_at IS NULL
                       AND status != ?
                       AND data_vencimento < CURDATE()
                       AND DATEDIFF(CURDATE(), data_vencimento) >= ?
                       AND DATEDIFF(CURDATE(), data_vencimento) < ?"
                );
                $stmt->execute([STATUS_PAGO, $dias, $proxima]);
                $row = $stmt->fetch();
                $resultado[] = [
                    'faixa'       => "{$dias} dias",
                    'quantidade'  => (int) $row['quantidade'],
                    'valor_total' => (float) $row['valor_total'],
                ];
            }

            Response::success($resultado);
            break;

        case 'marcar_pago':
            $data   = Response::input();
            $id     = (int) ($data['id'] ?? 0);
            $entrada = new Entrada();
            $antes  = $entrada->findById($id);
            if (!$antes) {
                Response::error('Registro não encontrado', 404);
            }
            $entrada->atualizarStatus($id, STATUS_PAGO);
            $log->registrar('UPDATE', 'entradas', $id, $antes, ['status' => STATUS_PAGO]);
            Response::success(null, 'Marcado como pago com sucesso');
            break;

        case 'renegociar':
            $data    = Response::input();
            $id      = (int) ($data['id'] ?? 0);
            $novaData = $data['nova_data_vencimento'] ?? '';
            if (!$id || !$novaData) {
                Response::error('ID e nova data são obrigatórios');
            }
            $db    = Database::getInstance();
            $stmt  = $db->prepare(
                "UPDATE entradas SET data_vencimento = ?, status = ?, updated_at = NOW()
                 WHERE id = ? AND deleted_at IS NULL"
            );
            $stmt->execute([$novaData, STATUS_PENDENTE, $id]);
            $log->registrar('UPDATE', 'entradas', $id, null, ['data_vencimento' => $novaData, 'status' => STATUS_PENDENTE]);
            Response::success(null, 'Renegociado com sucesso');
            break;

        default:
            Response::error('Ação inválida', 400);
    }
} catch (PDOException $e) {
    Response::error('Erro de banco de dados: ' . $e->getMessage(), 500);
} catch (Throwable $e) {
    Response::error('Erro interno: ' . $e->getMessage(), 500);
}
