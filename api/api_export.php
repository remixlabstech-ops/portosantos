<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(0); }

require_once __DIR__ . '/../config/database.php';

$tipo = $_GET['tipo'] ?? 'entradas';
$tipo = in_array($tipo, ['entradas', 'saidas'], true) ? $tipo : 'entradas';

$pdo = getConnection();

if ($tipo === 'entradas') {
    $stmt = $pdo->query(
        "SELECT e.id, e.data_entrada, e.data_vencimento,
                c.nome AS cliente, cat.nome AS categoria, th.nome AS tipo_honorario,
                e.valor_entrada, e.status, e.descricao
         FROM entradas e
         LEFT JOIN clientes c ON e.cliente_id = c.id
         LEFT JOIN categorias_receita cat ON e.categoria_id = cat.id
         LEFT JOIN tipos_honorarios th ON e.tipo_honorario_id = th.id
         WHERE e.deleted_at IS NULL
         ORDER BY e.data_entrada DESC"
    );
    $cols = ['ID','Data Entrada','Vencimento','Cliente','Categoria','Tipo Honorário','Valor','Status','Descrição'];
} else {
    $stmt = $pdo->query(
        "SELECT s.id, s.data_saida, s.data_vencimento,
                f.nome AS fornecedor, cd.nome AS categoria, cc.nome AS centro_custo,
                s.valor, s.status, s.descricao
         FROM saidas s
         LEFT JOIN fornecedores f ON s.fornecedor_id = f.id
         LEFT JOIN categorias_despesa cd ON s.categoria_id = cd.id
         LEFT JOIN centros_custo cc ON s.centro_custo_id = cc.id
         WHERE s.deleted_at IS NULL
         ORDER BY s.data_saida DESC"
    );
    $cols = ['ID','Data Saída','Vencimento','Fornecedor','Categoria','Centro de Custo','Valor','Status','Descrição'];
}

$rows = $stmt->fetchAll(PDO::FETCH_NUM);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $tipo . '_' . date('Y-m-d') . '.csv"');

// BOM for Excel UTF-8
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, $cols, ';');

foreach ($rows as $row) {
    // Sanitize each cell to prevent CSV formula injection
    $safe = array_map(function ($cell) {
        $cell = (string)$cell;
        if (in_array($cell[0] ?? '', ['=', '+', '-', '@', '|', "\t", "\r"], true)) {
            $cell = "'" . $cell;
        }
        return $cell;
    }, $row);
    fputcsv($out, $safe, ';');
}

fclose($out);
exit;
