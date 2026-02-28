<?php
/**
 * View: Página de Erro
 * Porto Santos - Sistema ERP Jurídico
 */
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página não encontrada — Porto Santos ERP</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="error-page">
    <h1>404</h1>
    <p>Página não encontrada.</p>
    <a href="/dashboard.php" class="btn btn-primary">Voltar ao Dashboard</a>
</div>
</body>
</html>
