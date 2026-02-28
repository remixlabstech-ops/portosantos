<?php
/**
 * Base Controller - Funcionalidades comuns
 * Porto Santos - Sistema ERP Jurídico
 */

require_once __DIR__ . '/../config/constants.php';

abstract class BaseController
{
    /**
     * Renderiza uma view passando variáveis.
     *
     * @param string $view  Caminho relativo a views/ (sem .php)
     * @param array<string, mixed> $data Variáveis exportadas para a view
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        $viewFile = __DIR__ . "/../views/{$view}.php";
        if (!file_exists($viewFile)) {
            http_response_code(404);
            include __DIR__ . '/../views/error.php';
            return;
        }
        include __DIR__ . '/../views/layout/header.php';
        include __DIR__ . '/../views/layout/sidebar.php';
        include $viewFile;
        include __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Redireciona para URL.
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Obtém input sanitizado do POST.
     * Strings são escapadas; outros tipos são retornados sem modificação.
     */
    protected function post(string $key, mixed $default = null): mixed
    {
        if (!isset($_POST[$key])) {
            return $default;
        }
        $val = $_POST[$key];
        if (is_string($val)) {
            return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
        }
        // Arrays e outros tipos: não sanitizar aqui, chamar deve tratar
        return $val;
    }

    /**
     * Obtém input sanitizado do GET.
     * Strings são escapadas; outros tipos são retornados sem modificação.
     */
    protected function get(string $key, mixed $default = null): mixed
    {
        if (!isset($_GET[$key])) {
            return $default;
        }
        $val = $_GET[$key];
        if (is_string($val)) {
            return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
        }
        return $val;
    }
}
