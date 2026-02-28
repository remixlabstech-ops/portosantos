<?php
class CategoriaController extends BaseController {

    private Categoria $model;

    public function __construct() {
        $this->model = new Categoria();
    }

    public function index(): array {
        return [
            'success'  => true,
            'receita'  => $this->model->getAllReceita(),
            'despesa'  => $this->model->getAllDespesa(),
        ];
    }

    public function store(array $data): array {
        $error = $this->validateRequired($data, ['nome','tipo_categoria']);
        if ($error) return $this->errorResponse($error);

        $tipo = $data['tipo_categoria'] === 'receita' ? 'receita' : 'despesa';
        $clean = ['nome' => $this->sanitizeString($data['nome'])];
        if ($tipo === 'receita' && !empty($data['tipo'])) {
            $clean['tipo'] = $this->sanitizeString($data['tipo']);
        }

        try {
            $id = $this->model->create($clean, $tipo);
            return $this->jsonResponse(['success' => true, 'id' => $id, 'message' => 'Categoria criada com sucesso.'], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao criar categoria: ' . $e->getMessage(), 500);
        }
    }

    public function update(int $id, array $data): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');
        $tipo = ($data['tipo_categoria'] ?? 'despesa') === 'receita' ? 'receita' : 'despesa';
        $clean = [];
        if (isset($data['nome'])) $clean['nome'] = $this->sanitizeString($data['nome']);
        if ($tipo === 'receita' && isset($data['tipo'])) $clean['tipo'] = $this->sanitizeString($data['tipo']);

        try {
            $ok = $this->model->update($id, $clean, $tipo);
            if (!$ok) return $this->errorResponse('Categoria não encontrada.', 404);
            return ['success' => true, 'message' => 'Categoria atualizada com sucesso.'];
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao atualizar categoria: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(int $id, string $tipo): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');
        $tipoClean = ($tipo === 'receita') ? 'receita' : 'despesa';
        try {
            $ok = $this->model->delete($id, $tipoClean);
            if (!$ok) return $this->errorResponse('Categoria não encontrada.', 404);
            return ['success' => true, 'message' => 'Categoria removida com sucesso.'];
        } catch (\Throwable $e) {
            return $this->errorResponse('Não é possível remover esta categoria pois ela está em uso.', 409);
        }
    }
}
