<?php
class InadimplenciaController extends BaseController {

    private Cliente $clienteModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
    }

    public function listar(array $filtros = []): array {
        $dias = isset($filtros['tipo_filtro']) && $filtros['tipo_filtro'] !== 'todos'
            ? (int)$filtros['tipo_filtro']
            : 0;

        $dados = $this->clienteModel->getInadimplentes($dias);
        return ['success' => true, 'data' => $dados];
    }

    public function faixas(): array {
        $faixasDef = [5, 10, 15, 20, 30, 45, 60, 90, 120, 180];
        $pdo       = getConnection();
        $resultado = [];

        for ($i = 0; $i < count($faixasDef); $i++) {
            $min = $faixasDef[$i];
            $max = isset($faixasDef[$i + 1]) ? $faixasDef[$i + 1] : PHP_INT_MAX;

            if ($max === PHP_INT_MAX) {
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) AS quantidade, COALESCE(SUM(valor_entrada),0) AS valor_total
                     FROM entradas
                     WHERE deleted_at IS NULL AND status = 'Aberto'
                       AND data_vencimento < CURDATE()
                       AND DATEDIFF(CURDATE(), data_vencimento) >= ?"
                );
                $stmt->execute([$min]);
            } else {
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) AS quantidade, COALESCE(SUM(valor_entrada),0) AS valor_total
                     FROM entradas
                     WHERE deleted_at IS NULL AND status = 'Aberto'
                       AND data_vencimento < CURDATE()
                       AND DATEDIFF(CURDATE(), data_vencimento) >= ?
                       AND DATEDIFF(CURDATE(), data_vencimento) < ?"
                );
                $stmt->execute([$min, $max]);
            }

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $resultado[] = [
                'faixa'       => $min . ' dias',
                'quantidade'  => (int)($row['quantidade'] ?? 0),
                'valor_total' => (float)($row['valor_total'] ?? 0),
            ];
        }

        return ['success' => true, 'data' => $resultado];
    }

    public function rankingInadimplentes(): array {
        $pdo  = getConnection();
        $stmt = $pdo->prepare(
            "SELECT c.nome AS cliente_nome, c.cpf,
                    COUNT(e.id) AS quantidade,
                    COALESCE(SUM(e.valor_entrada),0) AS valor_total,
                    MAX(DATEDIFF(CURDATE(), e.data_vencimento)) AS max_dias
             FROM entradas e
             JOIN clientes c ON e.cliente_id = c.id
             WHERE e.deleted_at IS NULL AND e.status = 'Aberto'
               AND e.data_vencimento < CURDATE()
             GROUP BY e.cliente_id, c.nome, c.cpf
             ORDER BY valor_total DESC
             LIMIT 10"
        );
        $stmt->execute();
        return ['success' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }
}
