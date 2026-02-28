<?php
/**
 * API Response Helper
 * Porto Santos - Sistema ERP Jurídico
 */

class Response
{
    /**
     * Envia resposta JSON de sucesso.
     *
     * @param mixed $data
     */
    public static function success(mixed $data = null, string $message = 'OK', int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Envia resposta JSON de erro.
     */
    public static function error(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => false,
            'message' => $message,
            'data'    => null,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Lê e decodifica o corpo JSON da requisição.
     *
     * @return array<string, mixed>
     */
    public static function input(): array
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return [];
        }
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }
}
