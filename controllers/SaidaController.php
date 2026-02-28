<?php
class SaidaController extends BaseController {

    private Saida $model;

    public function __construct() {
        $this->model = new Saida();
    }

    public function index(array $filters = []): array {
        $clean = [];
        if (!empty($filters['fornecedor_id']))   $clean['fornecedor_id']   = $this->sanitizeInt($filters['fornecedor_id']);
        if (!empty($filters['categoria_id']))    $clean['categoria_id']    = $this->sanitizeInt($filters['categoria_id']);
        if (!empty($filters['centro_custo_id'])) $clean['centro_custo_id'] = $this->sanitizeInt($filters['centro_custo_id']);
        if (!empty($filters['status']))           $clean['status']           = $this->sanitizeString($filters['status']);
        if (!empty($filters['data_inicio']))      $clean['data_inicio']      = $this->sanitizeString($filters['data_inicio']);
        if (!empty($filters['data_fim']))         $clean['data_fim']         = $this->sanitizeString($filters['data_fim']);

        return ['success' => true, 'data' => $this->model->getAll($clean)];
    }

    public function store(array $data): array {
        $error = $this->validateRequired($data, ['categoria_id','descricao','valor','data_saida']);
        if ($error) return $this->errorResponse($error);

        if (!empty($_FILES['comprovante'])) {
            $path = $this->handleUpload($_FILES['comprovante'], 'saidas');
            if ($path !== false) {
                $data['comprovante'] = $path;
            }
        }

        $rateios = $data['rateios'] ?? [];
        unset($data['rateios']);

        try {
            $id = $this->model->create($data, $rateios);
            return $this->jsonResponse(['success' => true, 'id' => $id, 'message' => 'Saída criada com sucesso.'], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage());
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao criar saída: ' . $e->getMessage(), 500);
        }
    }

    public function update(int $id, array $data): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');

        if (!empty($_FILES['comprovante'])) {
            $path = $this->handleUpload($_FILES['comprovante'], 'saidas');
            if ($path !== false) {
                $data['comprovante'] = $path;
            }
        }

        try {
            $ok = $this->model->update($id, $data);
            if (!$ok) return $this->errorResponse('Saída não encontrada.', 404);
            return ['success' => true, 'message' => 'Saída atualizada com sucesso.'];
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao atualizar saída: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(int $id): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');
        $ok = $this->model->delete($id);
        if (!$ok) return $this->errorResponse('Saída não encontrada.', 404);
        return ['success' => true, 'message' => 'Saída removida com sucesso.'];
    }
}
