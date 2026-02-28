<?php
class ClienteController extends BaseController {

    private Cliente $model;

    public function __construct() {
        $this->model = new Cliente();
    }

    public function index(array $filters = []): array {
        return ['success' => true, 'data' => $this->model->getAll($filters)];
    }

    public function show(int $id): array {
        $item = $this->model->getById($id);
        if (!$item) return $this->errorResponse('Cliente não encontrado.', 404);
        return ['success' => true, 'data' => $item];
    }

    public function store(array $data): array {
        $error = $this->validateRequired($data, ['nome']);
        if ($error) return $this->errorResponse($error);

        $clean = [
            'nome'     => $this->sanitizeString($data['nome']),
            'cpf'      => $this->sanitizeString($data['cpf'] ?? ''),
            'email'    => $this->sanitizeString($data['email'] ?? ''),
            'telefone' => $this->sanitizeString($data['telefone'] ?? ''),
            'endereco' => $this->sanitizeString($data['endereco'] ?? ''),
        ];

        try {
            $id = $this->model->create($clean);
            return $this->jsonResponse(['success' => true, 'id' => $id, 'message' => 'Cliente criado com sucesso.'], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao criar cliente: ' . $e->getMessage(), 500);
        }
    }

    public function update(int $id, array $data): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');
        $clean = array_filter([
            'nome'     => isset($data['nome'])     ? $this->sanitizeString($data['nome'])     : null,
            'cpf'      => isset($data['cpf'])      ? $this->sanitizeString($data['cpf'])      : null,
            'email'    => isset($data['email'])    ? $this->sanitizeString($data['email'])    : null,
            'telefone' => isset($data['telefone']) ? $this->sanitizeString($data['telefone']) : null,
            'endereco' => isset($data['endereco']) ? $this->sanitizeString($data['endereco']) : null,
        ], fn($v) => $v !== null);

        try {
            $ok = $this->model->update($id, $clean);
            if (!$ok) return $this->errorResponse('Cliente não encontrado.', 404);
            return ['success' => true, 'message' => 'Cliente atualizado com sucesso.'];
        } catch (\Throwable $e) {
            return $this->errorResponse('Erro ao atualizar cliente: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(int $id): array {
        if ($id <= 0) return $this->errorResponse('ID inválido.');
        $ok = $this->model->delete($id);
        if (!$ok) return $this->errorResponse('Cliente não encontrado.', 404);
        return ['success' => true, 'message' => 'Cliente removido com sucesso.'];
    }

    public function search(string $termo): array {
        return ['success' => true, 'data' => $this->model->search($termo)];
    }
}
