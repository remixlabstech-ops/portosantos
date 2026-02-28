<?php
require_once '../config.php';

action = $_GET['action'] ?? '';

switch ($action) {
    case 'listar':
        listarEntradas();
        break;
    case 'criar':
        criarEntrada();
        break;
    case 'atualizar':
        atualizarEntrada();
        break;
    case 'deletar':
        deletarEntrada();
        break;
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
}

function listarEntradas() {
    global $conn;
    $query = "SELECT e.*, c.nome as cliente_nome, c.cpf, cat.nome as categoria_nome, th.nome as tipo_honorario
              FROM entradas e
              LEFT JOIN clientes c ON e.cliente_id = c.id
              LEFT JOIN categorias_receita cat ON e.categoria_id = cat.id
              LEFT JOIN tipos_honorarios th ON e.tipo_honorario_id = th.id
              ORDER BY e.data_entrada DESC";
    
    $result = $conn->query($query);
    
    $entradas = [];
    while ($row = $result->fetch_assoc()) {
        $entradas[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $entradas]);
}

function criarEntrada() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    
    $cliente_id = $data['cliente_id'] ?? null;
    $categoria_id = $data['categoria_id'] ?? null;
    $tipo_honorario_id = $data['tipo_honorario_id'] ?? null;
    $valor_entrada = $data['valor_entrada'] ?? 0;
    $valor_causa = $data['valor_causa'] ?? null;
    $percentual = $data['percentual'] ?? null;
    $data_entrada = $data['data_entrada'] ?? date('Y-m-d');
    $data_vencimento = $data['data_vencimento'] ?? null;
    $descricao = $data['descricao'] ?? '';
    $status = $data['status'] ?? 'Aberto';
    
    $query = "INSERT INTO entradas (cliente_id, categoria_id, tipo_honorario_id, valor_entrada, valor_causa, percentual, data_entrada, data_vencimento, descricao, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiidddsss', $cliente_id, $categoria_id, $tipo_honorario_id, $valor_entrada, $valor_causa, $percentual, $data_entrada, $data_vencimento, $descricao, $status);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id, 'message' => 'Entrada criada com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao criar entrada']);
    }
}

function atualizarEntrada() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = $data['id'] ?? 0;
    $status = $data['status'] ?? 'Aberto';
    
    $query = "UPDATE entradas SET status = ? WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('si', $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Entrada atualizada com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao atualizar entrada']);
    }
}

function deletarEntrada() {
    global $conn;
    $id = $_GET['id'] ?? 0;
    
    $query = "DELETE FROM entradas WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Entrada deletada com sucesso']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao deletar entrada']);
    }
}
?>