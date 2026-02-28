<?php
class EntradaController extends BaseController {

    private Entrada $model;

    public function __construct() {
        $this->model = new Entrada();
    }

    public function index(array $filters = []): array {
        $clean = [];
        if (!empty($filters['cliente_id']))   $clean['cliente_id']   = $this->sanitizeInt($filters['cliente_id']);
        if (!empty($filters['categoria_id'])) $clean['categoria_id'] = $this->sanitizeInt($filters['categoria_id']);
        if (!empty($filters['status']))        $clean['status']        = $this->sanitizeString($filters['status']);
        if (!empty($filters['data_inicio']))   $clean['data_inicio']   = $this->sanitizeString($filters['data_inicio']);
        if (!empty($filters['data_fim']))      $clean['data_fim']      = $this->sanitizeString($filters['data_fim']);
        if (isset($filters['valor_min']))      $clean['valor_min']     = $this->sanitizeFloat($filters['valor_min']);
        if (isset($filters['valor_max']))      $clean['valor_max']     = $this->sanitizeFloat($filters['valor_max']);

        $data = $this->model->getAll($clean);
        return ['success' => true, 'data' => $data];
    }

    public function store(array $data): array {
        $error = $this->validateRequired($data, ['cliente_id','categoria_id','tipo_honorario_id','data_entrada']);
        if ($error) return $this->errorResponse($error);

        // Handle file upload from $_FILES if present
        if (!empty($_FILES['comprovante'])) {
            $path = $this->handleUpload($_FILES['comprovante'], 'entradas');
            if ($path !== false) {
                $data['comprovante'] = $path;
            }
        }

        try {
            $id = $this->model->create($data);
            return $this->jsonResponse(['success' => true, 'id' => $id, 'message' => 'Entrada criada com sucesso.'], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao criar entrada: ' . $e->getMessage(), 500);
        }
    }

    public function update(int $id, array $data): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');

        if (!empty($_FILES['comprovante'])) {
            $path = $this->handleUpload($_FILES['comprovante'], 'entradas');
            if ($path !== false) {
                $data['comprovante'] = $path;
            }
        }

        try {
            $ok = $this->model->update($id, $data);
            if (!$ok) return $this->errorResponse('Entrada não encontrada.', 404);
            return ['success' => true, 'message' => 'Entrada atualizada com sucesso.'];
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao atualizar entrada: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(int $id): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');
        $ok = $this->model->delete($id);
        if (!$ok) return $this->errorResponse('Entrada não encontrada.', 404);
        return ['success' => true, 'message' => 'Entrada removida com sucesso.'];
    }
}
