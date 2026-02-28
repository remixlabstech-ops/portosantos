<?php
require_once '../config.php';

action = $_GET['action'] ?? 'listar';

if ($action === 'listar') {
    listarInadimplencia();
} elseif ($action === 'faixas') {
    obterFaixasInadimplencia();
}

function listarInadimplencia() {
    global $conn;
    
    $tipo_filtro = $_GET['tipo_filtro'] ?? '30';
    
    $query = "SELECT e.*, c.nome as cliente_nome, c.cpf, cat.nome as categoria_nome, 
              th.nome as tipo_honorario,
              DATEDIFF(CURDATE(), e.data_vencimento) as dias_vencido
              FROM entradas e
              LEFT JOIN clientes c ON e.cliente_id = c.id
              LEFT JOIN categorias_receita cat ON e.categoria_id = cat.id
              LEFT JOIN tipos_honorarios th ON e.tipo_honorario_id = th.id
              WHERE e.status = 'Aberto' AND e.data_vencimento < CURDATE()";
    
    if ($tipo_filtro !== 'todos') {
        $query .= " AND DATEDIFF(CURDATE(), e.data_vencimento) >= $tipo_filtro";
    }
    
    $query .= " ORDER BY e.data_vencimento ASC";
    
    $result = $conn->query($query);
    
    $inadimplencias = [];
    while ($row = $result->fetch_assoc()) {
        $inadimplencias[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $inadimplencias]);
}

function obterFaixasInadimplencia() {
    global $conn;
    
    $faixas = [
        ['label' => '5 dias', 'dias' => 5],
        ['label' => '10 dias', 'dias' => 10],
        ['label' => '15 dias', 'dias' => 15],
        ['label' => '20 dias', 'dias' => 20],
        ['label' => '30 dias', 'dias' => 30],
        ['label' => '45 dias', 'dias' => 45],
        ['label' => '60 dias', 'dias' => 60],
        ['label' => '90 dias', 'dias' => 90],
        ['label' => '120 dias', 'dias' => 120],
        ['label' => '180 dias', 'dias' => 180]
    ];
    
    $resultado = [];
    
    foreach ($faixas as $faixa) {
        $dias = $faixa['dias'];
        $query = "SELECT COUNT(*) as quantidade, SUM(e.valor_entrada) as valor_total
                  FROM entradas e
                  WHERE e.status = 'Aberto' 
                  AND e.data_vencimento < CURDATE()
                  AND DATEDIFF(CURDATE(), e.data_vencimento) >= $dias";
        
        // Para não contar duplicatas, pega a próxima faixa
        $proxima_faixa = 999999;
        $index = array_search($faixa, $faixas);
        if (isset($faixas[$index + 1])) {
            $proxima_faixa = $faixas[$index + 1]['dias'];
        }
        
        $query .= " AND DATEDIFF(CURDATE(), e.data_vencimento) < $proxima_faixa";
        
        $result = $conn->query($query);
        $row = $result->fetch_assoc();
        
        $resultado[] = [
            'faixa' => $faixa['label'],
            'quantidade' => $row['quantidade'] ?? 0,
            'valor_total' => $row['valor_total'] ?? 0
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $resultado]);
}
?>