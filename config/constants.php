<?php
/**
 * Application Constants
 * Porto Santos - Sistema ERP Jurídico
 */

define('APP_NAME', 'Porto Santos ERP');
define('APP_VERSION', '1.0.0');
define('APP_URL', '');

// Upload settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5 MB
define('UPLOAD_ALLOWED_TYPES', ['application/pdf']);

// Pagination
define('ITEMS_PER_PAGE', 20);

// Empresa padrão (preparado para SaaS multiempresa)
define('EMPRESA_PADRAO', 1);

// Status
define('STATUS_PAGO', 'Pago');
define('STATUS_PENDENTE', 'Pendente');
define('STATUS_ATRASADO', 'Atrasado');

// Tipos de honorário
define('TIPOS_HONORARIO', ['Contratual', 'Avulso', 'Sucumbência', 'Êxito']);

// Categorias jurídicas
define('CATEGORIAS_JURIDICAS', ['Cível', 'Trabalhista', 'Previdenciário', 'Criminal']);

// Tipos de rateio
define('TIPOS_RATEIO', ['cliente', 'multiplos_clientes', 'administrativo']);

// Tipos de divisão
define('TIPOS_DIVISAO', ['percentual', 'valor_fixo']);
