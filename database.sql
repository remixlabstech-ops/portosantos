-- ============================================================
-- Porto Santos Advocacia - ERP Financial System
-- Database Schema
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------
-- Table: usuarios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(120) NOT NULL,
  `senha`      VARCHAR(255) NOT NULL,
  `perfil`     ENUM('admin','usuario') NOT NULL DEFAULT 'usuario',
  `ativo`      TINYINT(1) NOT NULL DEFAULT 1,
  `empresa`    VARCHAR(120) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuarios_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: configuracoes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `configuracoes` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `chave`      VARCHAR(80) NOT NULL,
  `valor`      TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_configuracoes_chave` (`chave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: clientes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `clientes` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(120) NOT NULL,
  `cpf`        VARCHAR(20) NULL,
  `email`      VARCHAR(120) NULL,
  `telefone`   VARCHAR(30) NULL,
  `endereco`   VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_clientes_nome` (`nome`),
  KEY `idx_clientes_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: fornecedores
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fornecedores` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(120) NOT NULL,
  `cnpj`       VARCHAR(20) NULL,
  `email`      VARCHAR(120) NULL,
  `telefone`   VARCHAR(30) NULL,
  `endereco`   VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fornecedores_nome` (`nome`),
  KEY `idx_fornecedores_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: centros_custo
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `centros_custo` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(80) NOT NULL,
  `descricao`  VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_centros_custo_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: categorias_receita
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias_receita` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(80) NOT NULL,
  `tipo`       ENUM('Cível','Trabalhista','Previdenciário','Criminal') NOT NULL DEFAULT 'Cível',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: categorias_despesa
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias_despesa` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`       VARCHAR(80) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: tipos_honorarios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tipos_honorarios` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome`       ENUM('Contratual','Avulso','Sucumbência','Êxito') NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: entradas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `entradas` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cliente_id`       INT UNSIGNED NOT NULL,
  `categoria_id`     INT UNSIGNED NOT NULL,
  `tipo_honorario_id` INT UNSIGNED NOT NULL,
  `valor_entrada`    DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `valor_causa`      DECIMAL(15,2) NULL,
  `percentual`       DECIMAL(5,2) NULL,
  `data_entrada`     DATE NOT NULL,
  `data_vencimento`  DATE NULL,
  `descricao`        TEXT NULL,
  `status`           ENUM('Aberto','Recebido','Cancelado') NOT NULL DEFAULT 'Aberto',
  `comprovante`      VARCHAR(255) NULL,
  `num_parcelas`     INT NOT NULL DEFAULT 1,
  `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`       TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entradas_cliente` (`cliente_id`),
  KEY `idx_entradas_categoria` (`categoria_id`),
  KEY `idx_entradas_tipo_honorario` (`tipo_honorario_id`),
  KEY `idx_entradas_status` (`status`),
  KEY `idx_entradas_data` (`data_entrada`),
  KEY `idx_entradas_vencimento` (`data_vencimento`),
  KEY `idx_entradas_deleted` (`deleted_at`),
  CONSTRAINT `fk_entradas_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_entradas_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_receita` (`id`),
  CONSTRAINT `fk_entradas_tipo_honorario` FOREIGN KEY (`tipo_honorario_id`) REFERENCES `tipos_honorarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: saidas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `saidas` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fornecedor_id`   INT UNSIGNED NULL,
  `categoria_id`    INT UNSIGNED NOT NULL,
  `centro_custo_id` INT UNSIGNED NULL,
  `descricao`       TEXT NOT NULL,
  `valor`           DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `taxa`            DECIMAL(10,4) NULL,
  `tipo_taxa`       ENUM('percentual','fixo') NULL,
  `data_saida`      DATE NOT NULL,
  `data_vencimento` DATE NULL,
  `status`          ENUM('Aberto','Pago','Cancelado') NOT NULL DEFAULT 'Aberto',
  `comprovante`     VARCHAR(255) NULL,
  `num_parcelas`    INT NOT NULL DEFAULT 1,
  `tipo_rateio`     ENUM('cliente','multiplos','administrativo') NOT NULL DEFAULT 'administrativo',
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at`      TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_saidas_fornecedor` (`fornecedor_id`),
  KEY `idx_saidas_categoria` (`categoria_id`),
  KEY `idx_saidas_centro_custo` (`centro_custo_id`),
  KEY `idx_saidas_status` (`status`),
  KEY `idx_saidas_data` (`data_saida`),
  KEY `idx_saidas_deleted` (`deleted_at`),
  CONSTRAINT `fk_saidas_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`),
  CONSTRAINT `fk_saidas_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_despesa` (`id`),
  CONSTRAINT `fk_saidas_centro_custo` FOREIGN KEY (`centro_custo_id`) REFERENCES `centros_custo` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: rateios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `rateios` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `saida_id`   INT UNSIGNED NOT NULL,
  `cliente_id` INT UNSIGNED NULL,
  `percentual` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rateios_saida` (`saida_id`),
  KEY `idx_rateios_cliente` (`cliente_id`),
  CONSTRAINT `fk_rateios_saida` FOREIGN KEY (`saida_id`) REFERENCES `saidas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rateios_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: parcelas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `parcelas` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo`             ENUM('entrada','saida') NOT NULL,
  `referencia_id`    INT UNSIGNED NOT NULL,
  `numero_parcela`   INT NOT NULL,
  `valor`            DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `data_vencimento`  DATE NOT NULL,
  `status`           ENUM('Aberto','Pago','Recebido') NOT NULL DEFAULT 'Aberto',
  `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parcelas_referencia` (`tipo`, `referencia_id`),
  KEY `idx_parcelas_vencimento` (`data_vencimento`),
  KEY `idx_parcelas_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Table: logs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `logs` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tabela`       VARCHAR(60) NOT NULL,
  `registro_id`  INT UNSIGNED NOT NULL,
  `acao`         VARCHAR(30) NOT NULL,
  `dados_antigos` JSON NULL,
  `dados_novos`  JSON NULL,
  `usuario`      VARCHAR(120) NOT NULL DEFAULT 'sistema',
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_logs_tabela` (`tabela`, `registro_id`),
  KEY `idx_logs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- Seed data
-- ------------------------------------------------------------

INSERT IGNORE INTO `tipos_honorarios` (`nome`) VALUES
  ('Contratual'),
  ('Avulso'),
  ('Sucumbência'),
  ('Êxito');

INSERT IGNORE INTO `categorias_receita` (`nome`, `tipo`) VALUES
  ('Honorários Contratuais', 'Cível'),
  ('Honorários Trabalhistas', 'Trabalhista'),
  ('Honorários Previdenciários', 'Previdenciário'),
  ('Honorários Criminais', 'Criminal'),
  ('Honorários Avulsos', 'Cível'),
  ('Sucumbência Cível', 'Cível'),
  ('Sucumbência Trabalhista', 'Trabalhista'),
  ('Êxito Cível', 'Cível'),
  ('Êxito Trabalhista', 'Trabalhista');

INSERT IGNORE INTO `categorias_despesa` (`nome`) VALUES
  ('Aluguel'),
  ('Água e Energia'),
  ('Telefone e Internet'),
  ('Material de Escritório'),
  ('Serviços de Terceiros'),
  ('Custas Judiciais'),
  ('Publicidade'),
  ('Salários e Pró-labore'),
  ('Impostos e Taxas'),
  ('Viagens e Hospedagem'),
  ('Honorários Periciais'),
  ('Despesas Bancárias'),
  ('Manutenção e Conservação'),
  ('Tecnologia e Software'),
  ('Outras Despesas');

INSERT IGNORE INTO `centros_custo` (`nome`, `descricao`) VALUES
  ('Administrativo', 'Despesas administrativas gerais do escritório'),
  ('Área Cível', 'Despesas relacionadas à área cível'),
  ('Área Trabalhista', 'Despesas relacionadas à área trabalhista'),
  ('Área Previdenciária', 'Despesas relacionadas à área previdenciária'),
  ('Área Criminal', 'Despesas relacionadas à área criminal'),
  ('Marketing', 'Despesas com marketing e publicidade'),
  ('Tecnologia', 'Despesas com TI e sistemas');

INSERT IGNORE INTO `configuracoes` (`chave`, `valor`) VALUES
  ('nome_empresa', 'Porto Santos Advocacia'),
  ('cnpj_empresa', ''),
  ('endereco_empresa', ''),
  ('telefone_empresa', ''),
  ('email_empresa', ''),
  ('logo_empresa', ''),
  ('moeda', 'BRL'),
  ('fuso_horario', 'America/Sao_Paulo');

-- WARNING: Change this password immediately after deployment.
-- Run: UPDATE usuarios SET senha = '$2y$12$...' WHERE email = 'admin@portosantos.adv.br';
INSERT IGNORE INTO `usuarios` (`nome`, `email`, `senha`, `perfil`, `ativo`) VALUES
  ('Administrador', 'admin@portosantos.adv.br', '$2y$12$placeholder_change_on_deploy', 'admin', 1);
