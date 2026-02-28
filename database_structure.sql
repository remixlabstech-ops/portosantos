-- ============================================================
-- Porto Santos ERP - Estrutura do Banco de Dados
-- MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Tabela: clientes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `nome`       VARCHAR(255)     NOT NULL,
    `cpf`        VARCHAR(14)               DEFAULT NULL,
    `processo`   VARCHAR(100)              DEFAULT NULL,
    `email`      VARCHAR(255)              DEFAULT NULL,
    `telefone`   VARCHAR(20)               DEFAULT NULL,
    `empresa`    INT UNSIGNED     NOT NULL DEFAULT 1,
    `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME                  DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_clientes_nome`    (`nome`),
    INDEX `idx_clientes_cpf`     (`cpf`),
    INDEX `idx_clientes_empresa` (`empresa`),
    INDEX `idx_clientes_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: fornecedores
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fornecedores` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome`           VARCHAR(255) NOT NULL,
    `documento`      VARCHAR(20)           DEFAULT NULL,
    `conta_bancaria` VARCHAR(100)          DEFAULT NULL,
    `empresa`        INT UNSIGNED NOT NULL DEFAULT 1,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`     DATETIME              DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_forn_nome`    (`nome`),
    INDEX `idx_forn_empresa` (`empresa`),
    INDEX `idx_forn_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: centros_custo
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `centros_custo` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome`       VARCHAR(150) NOT NULL,
    `descricao`  TEXT                  DEFAULT NULL,
    `empresa`    INT UNSIGNED NOT NULL DEFAULT 1,
    `ativo`      TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME              DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_cc_empresa` (`empresa`),
    INDEX `idx_cc_ativo`   (`ativo`),
    INDEX `idx_cc_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: categorias
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
    `id`         INT UNSIGNED             NOT NULL AUTO_INCREMENT,
    `tipo`       ENUM('entrada','saida')  NOT NULL DEFAULT 'entrada',
    `nome`       VARCHAR(150)             NOT NULL,
    `empresa`    INT UNSIGNED             NOT NULL DEFAULT 1,
    `created_at` DATETIME                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME                 NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME                          DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_cat_tipo`    (`tipo`),
    INDEX `idx_cat_empresa` (`empresa`),
    INDEX `idx_cat_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: entradas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `entradas` (
    `id`              INT UNSIGNED                              NOT NULL AUTO_INCREMENT,
    `cliente_id`      INT UNSIGNED                             NOT NULL,
    `categoria`       VARCHAR(100)                                      DEFAULT NULL,
    `tipo_honorario`  VARCHAR(50)                                       DEFAULT NULL,
    `valor_causa`     DECIMAL(15,2)                                     DEFAULT NULL,
    `percentual`      DECIMAL(5,2)                                      DEFAULT NULL,
    `valor`           DECIMAL(15,2)                            NOT NULL DEFAULT 0.00,
    `data_vencimento` DATE                                     NOT NULL,
    `status`          ENUM('Pendente','Pago','Atrasado')       NOT NULL DEFAULT 'Pendente',
    `comprovante`     VARCHAR(255)                                      DEFAULT NULL,
    `observacoes`     TEXT                                              DEFAULT NULL,
    `empresa`         INT UNSIGNED                             NOT NULL DEFAULT 1,
    `created_at`      DATETIME                                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME                                 NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      DATETIME                                          DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_ent_cliente`   (`cliente_id`),
    INDEX `idx_ent_status`    (`status`),
    INDEX `idx_ent_venc`      (`data_vencimento`),
    INDEX `idx_ent_empresa`   (`empresa`),
    INDEX `idx_ent_deleted`   (`deleted_at`),
    CONSTRAINT `fk_ent_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: saidas (declarada antes de parcelas por dependência de FK)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `saidas` (
    `id`               INT UNSIGNED                             NOT NULL AUTO_INCREMENT,
    `fornecedor_id`    INT UNSIGNED                                      DEFAULT NULL,
    `categoria`        VARCHAR(100)                                      DEFAULT NULL,
    `centro_custo_id`  INT UNSIGNED                                      DEFAULT NULL,
    `tipo_taxa`        ENUM('percentual','valor_fixo')          NOT NULL DEFAULT 'valor_fixo',
    `taxa_valor`       DECIMAL(10,2)                                     DEFAULT NULL,
    `valor`            DECIMAL(15,2)                            NOT NULL DEFAULT 0.00,
    `data_vencimento`  DATE                                     NOT NULL,
    `status`           ENUM('Pendente','Pago','Atrasado')       NOT NULL DEFAULT 'Pendente',
    `comprovante`      VARCHAR(255)                                      DEFAULT NULL,
    `observacoes`      TEXT                                              DEFAULT NULL,
    `empresa`          INT UNSIGNED                             NOT NULL DEFAULT 1,
    `created_at`       DATETIME                                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       DATETIME                                 NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`       DATETIME                                          DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_said_forn`    (`fornecedor_id`),
    INDEX `idx_said_cc`      (`centro_custo_id`),
    INDEX `idx_said_status`  (`status`),
    INDEX `idx_said_venc`    (`data_vencimento`),
    INDEX `idx_said_empresa` (`empresa`),
    INDEX `idx_said_deleted` (`deleted_at`),
    CONSTRAINT `fk_said_forn` FOREIGN KEY (`fornecedor_id`)   REFERENCES `fornecedores`  (`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_said_cc`   FOREIGN KEY (`centro_custo_id`) REFERENCES `centros_custo` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: parcelas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `parcelas` (
    `id`              INT UNSIGNED                        NOT NULL AUTO_INCREMENT,
    `entrada_id`      INT UNSIGNED                                 DEFAULT NULL,
    `saida_id`        INT UNSIGNED                                 DEFAULT NULL,
    `numero`          TINYINT UNSIGNED                    NOT NULL DEFAULT 1,
    `valor`           DECIMAL(15,2)                       NOT NULL DEFAULT 0.00,
    `data_vencimento` DATE                                NOT NULL,
    `status`          ENUM('Pendente','Pago','Atrasado')  NOT NULL DEFAULT 'Pendente',
    `empresa`         INT UNSIGNED                        NOT NULL DEFAULT 1,
    `created_at`      DATETIME                            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME                            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at`      DATETIME                                     DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_parc_entrada` (`entrada_id`),
    INDEX `idx_parc_saida`   (`saida_id`),
    INDEX `idx_parc_status`  (`status`),
    INDEX `idx_parc_empresa` (`empresa`),
    CONSTRAINT `fk_parc_entrada` FOREIGN KEY (`entrada_id`) REFERENCES `entradas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_parc_saida`   FOREIGN KEY (`saida_id`)   REFERENCES `saidas`   (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: rateios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `rateios` (
    `id`                 INT UNSIGNED                              NOT NULL AUTO_INCREMENT,
    `saida_id`           INT UNSIGNED                             NOT NULL,
    `tipo_rateio`        ENUM('cliente','multiplos_clientes','administrativo') NOT NULL DEFAULT 'administrativo',
    `cliente_id`         INT UNSIGNED                                      DEFAULT NULL,
    `centro_custo_id`    INT UNSIGNED                                      DEFAULT NULL,
    `tipo_divisao`       ENUM('percentual','valor_fixo')          NOT NULL DEFAULT 'percentual',
    `percentual_divisao` DECIMAL(5,2)                                      DEFAULT NULL,
    `valor_divisao`      DECIMAL(15,2)                                     DEFAULT NULL,
    `empresa`            INT UNSIGNED                             NOT NULL DEFAULT 1,
    `created_at`         DATETIME                                 NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at`         DATETIME                                          DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_rat_saida`    (`saida_id`),
    INDEX `idx_rat_cliente`  (`cliente_id`),
    INDEX `idx_rat_cc`       (`centro_custo_id`),
    CONSTRAINT `fk_rat_saida`   FOREIGN KEY (`saida_id`)        REFERENCES `saidas`       (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_rat_cliente` FOREIGN KEY (`cliente_id`)      REFERENCES `clientes`     (`id`) ON UPDATE CASCADE,
    CONSTRAINT `fk_rat_cc`      FOREIGN KEY (`centro_custo_id`) REFERENCES `centros_custo`(`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabela: logs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `logs` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id`     INT UNSIGNED             DEFAULT NULL,
    `acao`           VARCHAR(20)     NOT NULL,
    `tabela`         VARCHAR(60)     NOT NULL,
    `registro_id`    INT UNSIGNED    NOT NULL,
    `valores_antes`  LONGTEXT                 DEFAULT NULL,
    `valores_depois` LONGTEXT                 DEFAULT NULL,
    `ip`             VARCHAR(45)              DEFAULT NULL,
    `empresa`        INT UNSIGNED    NOT NULL DEFAULT 1,
    `created_at`     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_log_tabela`  (`tabela`, `registro_id`),
    INDEX `idx_log_empresa` (`empresa`),
    INDEX `idx_log_acao`    (`acao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Dados iniciais: categorias jurídicas
-- ------------------------------------------------------------
INSERT IGNORE INTO `categorias` (`tipo`, `nome`, `empresa`) VALUES
    ('entrada', 'Cível',          1),
    ('entrada', 'Trabalhista',    1),
    ('entrada', 'Previdenciário', 1),
    ('entrada', 'Criminal',       1),
    ('saida',   'Aluguel',        1),
    ('saida',   'Telefonia',      1),
    ('saida',   'Energia',        1),
    ('saida',   'Software',       1),
    ('saida',   'Material',       1);

-- ------------------------------------------------------------
-- Dados iniciais: centros de custo
-- ------------------------------------------------------------
INSERT IGNORE INTO `centros_custo` (`nome`, `descricao`, `empresa`) VALUES
    ('Administrativo', 'Gastos gerais do escritório', 1),
    ('Jurídico',       'Despesas de processos',       1),
    ('TI',             'Infraestrutura e tecnologia', 1);

SET FOREIGN_KEY_CHECKS = 1;
