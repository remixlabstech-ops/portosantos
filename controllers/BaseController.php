<?php
abstract class BaseController {

    protected function jsonResponse(mixed $data, int $status = 200): array {
        http_response_code($status);
        return $data;
    }

    protected function errorResponse(string $message, int $status = 400): array {
        http_response_code($status);
        return ['success' => false, 'message' => $message];
    }

    protected function validateRequired(array $data, array $fields): ?string {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                return "O campo '$field' é obrigatório.";
            }
        }
        return null;
    }

    protected function sanitizeString(mixed $val): string {
        return trim(strip_tags((string)$val));
    }

    protected function sanitizeInt(mixed $val): int {
        return (int)filter_var($val, FILTER_SANITIZE_NUMBER_INT);
    }

    protected function sanitizeFloat(mixed $val): float {
        return (float)filter_var(str_replace(',', '.', (string)$val), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    protected function handleUpload(array $file, string $campo): string|false {
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedMimes = ['application/pdf'];
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $allowedMimes, true)) {
            return false;
        }

        $dir = __DIR__ . "/../uploads/{$campo}/";
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $ext      = 'pdf';
        $filename = uniqid("{$campo}_", true) . ".{$ext}";
        $dest     = $dir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return false;
        }

        return "uploads/{$campo}/{$filename}";
    }
}
