<?php
class CentroCustoController extends BaseController {

    private CentroCusto $model;

    public function __construct() {
        $this->model = new CentroCusto();
    }

    public function index(): array {
        return ['success' => true, 'data' => $this->model->getAll()];
    }

    public function store(array $data): array {
        $error = $this->validateRequired($data, ['nome']);
        if ($error) return $this->errorResponse($error);

        $clean = [
            'nome'      => $this->sanitizeString($data['nome']),
            'descricao' => $this->sanitizeString($data['descricao'] ?? ''),
        ];

        try {
            $id = $this->model->create($clean);
            return $this->jsonResponse(['success' => true, 'id' => $id, 'message' => 'Centro de custo criado com sucesso.'], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao criar centro de custo: ' . $e->getMessage(), 500);
        }
    }

    public function update(int $id, array $data): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');
        $clean = [];
        if (isset($data['nome']))      $clean['nome']      = $this->sanitizeString($data['nome']);
        if (isset($data['descricao'])) $clean['descricao'] = $this->sanitizeString($data['descricao']);

        try {
            $ok = $this->model->update($id, $clean);
            if (!$ok) return $this->errorResponse('Centro de custo não encontrado.', 404);
            return ['success' => true, 'message' => 'Centro de custo atualizado com sucesso.'];
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao atualizar: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(int $id): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');
        $ok = $this->model->delete($id);
        if (!$ok) return $this->errorResponse('Centro de custo não encontrado.', 404);
        return ['success' => true, 'message' => 'Centro de custo removido com sucesso.'];
    }
}
