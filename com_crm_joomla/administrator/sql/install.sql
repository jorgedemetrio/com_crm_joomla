-- =====================================================================
-- install.mysql.utf8.sql — com_crm_joomla (Joomla 5 · Admin-only)
-- Padrões: ENGINE=InnoDB, CHARSET=utf8mb4, auditoria padrão em todas.
-- PK: CHAR(36) (UUID) para entidades principais; INT AUTO_INCREMENT p/ logs.
-- =====================================================================

-- ==============================================
-- 0) Segurança: SQL_MODE e engine
-- ==============================================
SET sql_mode = '';
SET NAMES utf8mb4;

-- ==============================================
-- 1) GRUPOS DE LEAD
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_lead_groups` (
  `id` CHAR(36) NOT NULL,
  `nome` VARCHAR(150) NOT NULL,

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,
  `criacao_session_id` VARCHAR(200) NULL,
  `criacao_tracking_id` VARCHAR(36) NULL,
  `criacao_ip` VARCHAR(45) NULL,
  `criacao_ip_proxy` VARCHAR(45) NULL,
  `alteracao_session_id` VARCHAR(200) NULL,
  `alteracao_tracking_id` VARCHAR(36) NULL,
  `alteracao_ip` VARCHAR(45) NULL,
  `alteracao_ip_proxy` VARCHAR(45) NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_crm_lead_groups_nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 2) LEADS
-- Regra: lead válido = (email válido) OU (telefone válido); site OPCIONAL.
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_leads` (
  `id` CHAR(36) NOT NULL,
  `cnpj` VARCHAR(14) NULL,
  `razao_social` VARCHAR(255) NULL,
  `nome_fantasia` VARCHAR(255) NULL,
  `matriz_filial` VARCHAR(50) NULL,
  `situacao` VARCHAR(50) NULL,
  `porte` VARCHAR(50) NULL,
  `natureza` VARCHAR(50) NULL,
  `natureza_desc` VARCHAR(255) NULL,
  `qualificacao_responsavel_id` VARCHAR(50) NULL,
  `qualificacao_responsavel_desc` VARCHAR(255) NULL,
  `capital_social` DECIMAL(18,2) NULL,

  -- Endereço
  `logradouro` VARCHAR(255) NULL,
  `numero` VARCHAR(20) NULL,
  `complemento` VARCHAR(100) NULL,
  `bairro` VARCHAR(255) NULL,
  `cep` VARCHAR(9) NULL,
  `cidade` VARCHAR(255) NULL,
  `estado` CHAR(2) NULL,
  `pais` VARCHAR(100) DEFAULT 'Brasil',

  -- Contatos
  `email` VARCHAR(255) NULL,
  `telefone1` VARCHAR(30) NULL,
  `tipo_telefone1` ENUM('celular','fixo','whatsapp','sms') NULL,
  `telefone2` VARCHAR(30) NULL,
  `tipo_telefone2` ENUM('celular','fixo','whatsapp','sms') NULL,

  -- Normalizações (para deduplicação)
  `email_norm` VARCHAR(255) GENERATED ALWAYS AS (LOWER(`email`)) VIRTUAL,
  `telefone_norm` VARCHAR(32) NULL,

  -- Sócios (até 4 campos “achatados” vindos do CSV)
  `nome_socio1` VARCHAR(255) NULL,
  `tipo_socio_id1` VARCHAR(50) NULL,
  `tipo_socio_desc1` VARCHAR(255) NULL,
  `telefone1_socio1` VARCHAR(30) NULL,
  `email1_socio1` VARCHAR(255) NULL,
  `email2_socio1` VARCHAR(255) NULL,
  `nome_socio2` VARCHAR(255) NULL,
  `tipo_socio_id2` VARCHAR(50) NULL,
  `tipo_socio_desc2` VARCHAR(255) NULL,
  `telefone1_socio2` VARCHAR(30) NULL,
  `email1_socio2` VARCHAR(255) NULL,
  `email2_socio2` VARCHAR(255) NULL,
  `nome_socio3` VARCHAR(255) NULL,
  `tipo_socio_id3` VARCHAR(50) NULL,
  `tipo_socio_desc3` VARCHAR(255) NULL,
  `telefone1_socio3` VARCHAR(30) NULL,
  `email1_socio3` VARCHAR(255) NULL,
  `email2_socio3` VARCHAR(255) NULL,
  `nome_socio4` VARCHAR(255) NULL,
  `tipo_socio_id4` VARCHAR(50) NULL,
  `tipo_socio_desc4` VARCHAR(255) NULL,
  `telefone1_socio4` VARCHAR(30) NULL,
  `email1_socio4` VARCHAR(255) NULL,
  `email2_socio4` VARCHAR(255) NULL,

  -- Origem & enriquecimento
  `origem` ENUM('CSV','Google','LinkedIn','Instagram','Facebook','Manual') NOT NULL DEFAULT 'CSV',
  `url_origem` VARCHAR(500) NULL,
  `site` VARCHAR(255) NULL,
  `descricao` TEXT NULL,
  `observacoes` TEXT NULL,

  -- Status
  `status` ENUM('NOVO','VALIDADO','REPROVADO','MIGRADO') NOT NULL DEFAULT 'NOVO',

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,
  `criacao_session_id` VARCHAR(200) NULL,
  `criacao_tracking_id` VARCHAR(36) NULL,
  `criacao_ip` VARCHAR(45) NULL,
  `criacao_ip_proxy` VARCHAR(45) NULL,
  `alteracao_session_id` VARCHAR(200) NULL,
  `alteracao_tracking_id` VARCHAR(36) NULL,
  `alteracao_ip` VARCHAR(45) NULL,
  `alteracao_ip_proxy` VARCHAR(45) NULL,

  PRIMARY KEY (`id`),
  KEY `idx_crm_leads_email_norm` (`email_norm`),
  KEY `idx_crm_leads_tel_norm` (`telefone_norm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 3) MAPEAMENTO: Lead x Grupo (N:M)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_lead_group_map` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `lead_id` CHAR(36) NOT NULL,
  `group_id` CHAR(36) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_crm_lead_group_map` (`lead_id`,`group_id`),
  KEY `idx_crm_lead_group_map_lead` (`lead_id`),
  KEY `idx_crm_lead_group_map_group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 4) CAMPANHAS
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_campanhas` (
  `id` CHAR(36) NOT NULL,
  `nome` VARCHAR(150) NOT NULL,
  `status` ENUM('rascunho','aprovada','em_envio','finalizada','pausada') NOT NULL DEFAULT 'rascunho',

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,
  `criacao_session_id` VARCHAR(200) NULL,
  `criacao_tracking_id` VARCHAR(36) NULL,
  `criacao_ip` VARCHAR(45) NULL,
  `criacao_ip_proxy` VARCHAR(45) NULL,
  `alteracao_session_id` VARCHAR(200) NULL,
  `alteracao_tracking_id` VARCHAR(36) NULL,
  `alteracao_ip` VARCHAR(45) NULL,
  `alteracao_ip_proxy` VARCHAR(45) NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 5) MAPEAMENTO: Campanha x Grupo (N:M)
-- Limita quais grupos podem ser usados nos templates de envio.
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_campanha_group_map` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `campanha_id` CHAR(36) NOT NULL,
  `group_id` CHAR(36) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_crm_campanha_group_map` (`campanha_id`,`group_id`),
  KEY `idx_crm_campanha_group_map_camp` (`campanha_id`),
  KEY `idx_crm_campanha_group_map_group` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 6) TEMPLATES: E-mail de Campanha
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_campanha_emails` (
  `id` CHAR(36) NOT NULL,
  `campanha_id` CHAR(36) NOT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `html` MEDIUMTEXT NOT NULL,

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,
  `criacao_session_id` VARCHAR(200) NULL,
  `criacao_tracking_id` VARCHAR(36) NULL,
  `criacao_ip` VARCHAR(45) NULL,
  `criacao_ip_proxy` VARCHAR(45) NULL,
  `alteracao_session_id` VARCHAR(200) NULL,
  `alteracao_tracking_id` VARCHAR(36) NULL,
  `alteracao_ip` VARCHAR(45) NULL,
  `alteracao_ip_proxy` VARCHAR(45) NULL,

  PRIMARY KEY (`id`),
  KEY `idx_crm_campanha_emails_camp` (`campanha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 7) TEMPLATES: SMS de Campanha
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_campanha_sms` (
  `id` CHAR(36) NOT NULL,
  `campanha_id` CHAR(36) NOT NULL,
  `texto` VARCHAR(612) NOT NULL, -- ~4 SMS concatenados

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,
  `criacao_session_id` VARCHAR(200) NULL,
  `criacao_tracking_id` VARCHAR(36) NULL,
  `criacao_ip` VARCHAR(45) NULL,
  `criacao_ip_proxy` VARCHAR(45) NULL,
  `alteracao_session_id` VARCHAR(200) NULL,
  `alteracao_tracking_id` VARCHAR(36) NULL,
  `alteracao_ip` VARCHAR(45) NULL,
  `alteracao_ip_proxy` VARCHAR(45) NULL,

  PRIMARY KEY (`id`),
  KEY `idx_crm_campanha_sms_camp` (`campanha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 8) LINKS DE CAMPANHA (rastreamento + shortlink)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_campanha_links` (
  `id` CHAR(36) NOT NULL,
  `campanha_id` CHAR(36) NOT NULL,
  `nome` VARCHAR(200) NOT NULL,
  `url_destino` TEXT NOT NULL,
  `provider` VARCHAR(50) NOT NULL,
  `alias_local` VARCHAR(64) NOT NULL UNIQUE,
  `clicks_total` INT NOT NULL DEFAULT 0,
  `last_click` DATETIME NULL,

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,
  `criacao_session_id` VARCHAR(200) NULL,
  `criacao_tracking_id` VARCHAR(36) NULL,
  `criacao_ip` VARCHAR(45) NULL,
  `criacao_ip_proxy` VARCHAR(45) NULL,
  `alteracao_session_id` VARCHAR(200) NULL,
  `alteracao_tracking_id` VARCHAR(36) NULL,
  `alteracao_ip` VARCHAR(45) NULL,
  `alteracao_ip_proxy` VARCHAR(45) NULL,

  PRIMARY KEY (`id`),
  KEY `idx_crm_campanha_links_camp` (`campanha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 9) ASSOCIATIVA: Link x Lead (acessos agregados)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_campanha_link_lead` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `link_id` CHAR(36) NOT NULL,
  `lead_id` CHAR(36) NOT NULL,
  `acessos` INT NOT NULL DEFAULT 0,
  `ultimo_acesso` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_crm_link_lead` (`link_id`,`lead_id`),
  KEY `idx_crm_link_lead_link` (`link_id`),
  KEY `idx_crm_link_lead_lead` (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 10) LOG DE CADA CLIQUE
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_campanha_link_clicks` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `link_id` CHAR(36) NOT NULL,
  `lead_id` CHAR(36) NOT NULL,
  `campanha_id` CHAR(36) NOT NULL,
  `clicked_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` VARCHAR(45) NULL,
  `ip_proxy` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_crm_link_clicks_link` (`link_id`),
  KEY `idx_crm_link_clicks_camp` (`campanha_id`),
  KEY `idx_crm_link_clicks_lead` (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 11) AGENDAMENTOS DE DISPARO (jobs)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_agendamentos` (
  `id` CHAR(36) NOT NULL,
  `tipo` ENUM('email','sms') NOT NULL,
  `campanha_id` CHAR(36) NOT NULL,
  `template_id` CHAR(36) NOT NULL, -- email ou sms
  `inicio_em` DATETIME NULL,
  `lote_qtde` INT NULL,
  `delay_item_ms` INT NULL,
  `gap_lote_s` INT NULL,
  `status_job` ENUM('agendado','em_execucao','finalizado','pausado','erro') NOT NULL DEFAULT 'agendado',
  `erro_msg` VARCHAR(1000) NULL,

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,
  `criacao_session_id` VARCHAR(200) NULL,
  `criacao_tracking_id` VARCHAR(36) NULL,
  `criacao_ip` VARCHAR(45) NULL,
  `criacao_ip_proxy` VARCHAR(45) NULL,
  `alteracao_session_id` VARCHAR(200) NULL,
  `alteracao_tracking_id` VARCHAR(36) NULL,
  `alteracao_ip` VARCHAR(45) NULL,
  `alteracao_ip_proxy` VARCHAR(45) NULL,

  PRIMARY KEY (`id`),
  KEY `idx_crm_agenda_camp` (`campanha_id`),
  KEY `idx_crm_agenda_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 12) ENVIOS DE E-MAIL (instâncias por lead)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_email_envios` (
  `id` INT NOT NULL AUTO_INCREMENT,          -- envio_id (mid)
  `campanha_id` CHAR(36) NOT NULL,
  `email_template_id` CHAR(36) NOT NULL,
  `lead_id` CHAR(36) NOT NULL,
  `email_destino` VARCHAR(255) NOT NULL,
  `provedor_msg_id` VARCHAR(255) NULL,       -- id no provedor (Mailchimp/JMail tagging)
  `status` ENUM('fila','enviado','lido','clicado','bounce','spam','erro','cancelado') NOT NULL DEFAULT 'fila',
  `enviado_em` DATETIME NULL,
  `erro` VARCHAR(1000) NULL,

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,

  PRIMARY KEY (`id`),
  KEY `idx_crm_email_envios_camp` (`campanha_id`),
  KEY `idx_crm_email_envios_lead` (`lead_id`),
  KEY `idx_crm_email_envios_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 13) ABERTURAS (pixel 1×1)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_email_opens` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `campanha_id` CHAR(36) NOT NULL,
  `envio_id` INT NOT NULL,
  `opened_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` VARCHAR(45) NULL,
  `ip_proxy` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  KEY `idx_crm_email_opens_envio` (`envio_id`),
  KEY `idx_crm_email_opens_camp` (`campanha_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 14) OPTOUT (descadastro)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_email_optout` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email_hash` CHAR(64) NOT NULL,   -- SHA-256(lower(email))
  `scope` ENUM('global','campanha') NOT NULL DEFAULT 'global',
  `campanha_id` CHAR(36) NULL,
  `reason` VARCHAR(255) NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_crm_email_optout` (`email_hash`,`scope`,`campanha_id`),
  KEY `idx_crm_email_optout_scope` (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 15) ENVIOS DE SMS (Zenvia)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_sms_envios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `campanha_id` CHAR(36) NOT NULL,
  `sms_template_id` CHAR(36) NOT NULL,
  `lead_id` CHAR(36) NOT NULL,
  `telefone_destino` VARCHAR(30) NOT NULL,
  `provider_msg_id` VARCHAR(255) NULL,
  `status` ENUM('fila','enviado','clicado','erro','cancelado') NOT NULL DEFAULT 'fila',
  `enviado_em` DATETIME NULL,
  `erro` VARCHAR(1000) NULL,

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,

  PRIMARY KEY (`id`),
  KEY `idx_crm_sms_envios_camp` (`campanha_id`),
  KEY `idx_crm_sms_envios_lead` (`lead_id`),
  KEY `idx_crm_sms_envios_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 16) INTEGRAÇÕES (tokens/chaves/API params)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_integracoes` (
  `id` CHAR(36) NOT NULL,
  `provider` VARCHAR(50) NOT NULL,     -- google_search, google_images, google_maps, linkedin, instagram, facebook, mailchimp, sugarcrm, shortlink, popimap, zenvia
  `params_json` JSON NOT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_crm_integracoes_provider` (`provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 17) IMPORTAÇÃO — CADASTROS (WEB)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_import_web` (
  `id` CHAR(36) NOT NULL,
  `nome` VARCHAR(150) NOT NULL,
  `origem` ENUM('Google','LinkedIn','Facebook','Instagram') NOT NULL,
  `palavras_chave` VARCHAR(500) NULL,
  `localizacao` VARCHAR(255) NULL,
  `limite_resultados` INT NULL,
  `pasta_imagens` VARCHAR(500) NULL,

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 18) IMPORTAÇÃO — CADASTROS (ARQUIVO)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_import_arquivo` (
  `id` CHAR(36) NOT NULL,
  `nome` VARCHAR(150) NOT NULL,
  `arquivo_path` VARCHAR(500) NOT NULL,

  -- Auditoria padrão
  `state` TINYINT(3) NOT NULL DEFAULT 1,
  `ordering` INT(11) NOT NULL DEFAULT 0,
  `checked_out` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `checked_out_time` DATETIME NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 19) IMPORTAÇÃO — EXECUÇÕES / LOGS
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_import_execucoes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('web','arquivo','users') NOT NULL,
  `referencia_id` CHAR(36) NULL,   -- id em import_web/import_arquivo (quando aplicável)
  `status` ENUM('ok','parcial','falha') NOT NULL DEFAULT 'ok',
  `linhas_total` INT NULL,
  `linhas_sucesso` INT NULL,
  `linhas_falha` INT NULL,
  `log` MEDIUMTEXT NULL,
  `started_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `finished_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_crm_import_exec_tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 20) EXPORTAÇÃO — PROFILES (regras/flags)
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_export_profiles` (
  `id` CHAR(36) NOT NULL,
  `nome` VARCHAR(150) NOT NULL,
  `descricao` VARCHAR(500) NULL,
  `criar_usuario_core` TINYINT(1) NOT NULL DEFAULT 0,
  `usuario_grupo_id` INT NULL,
  `usuario_enviar_reset` TINYINT(1) NOT NULL DEFAULT 1,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) NOT NULL DEFAULT 0,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 21) EXPORTAÇÃO — SCRIPTS (SQL / SugarCRM / Mailchimp / CSV)
-- `ordem` define a sequência de execução no profile.
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_export_scripts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `profile_id` CHAR(36) NOT NULL,
  `nome` VARCHAR(150) NOT NULL,
  `tipo` ENUM('sql','sugarcrm','mailchimp','csv') NOT NULL DEFAULT 'sql',
  `ordem` INT NOT NULL DEFAULT 0,
  `script_sql` MEDIUMTEXT NULL,
  `ativo` TINYINT(1) NOT NULL DEFAULT 1,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_crm_export_scripts_profile` (`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 22) EXPORTAÇÃO — EXECUÇÕES / AUDITORIA
-- ids_json armazena mapa de IDs capturados (aliases) durante a execução.
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_export_runs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `profile_id` CHAR(36) NOT NULL,
  `lead_id` CHAR(36) NOT NULL,
  `status` ENUM('ok','falha') NOT NULL DEFAULT 'ok',
  `started_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `finished_at` DATETIME NULL,
  `log` MEDIUMTEXT NULL,
  `ids_json` JSON NULL,
  PRIMARY KEY (`id`),
  KEY `idx_crm_export_runs_profile` (`profile_id`),
  KEY `idx_crm_export_runs_lead` (`lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 23) (Opcional) JOBS GENÉRICOS / FILA
-- Pode ser usado para crawling, validações e disparos centralizados.
-- ==============================================
CREATE TABLE IF NOT EXISTS `#__crm_jobs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` VARCHAR(50) NOT NULL,                -- ex: email_send, sms_send, crawl_web, validate_email
  `payload` MEDIUMTEXT NULL,                  -- JSON
  `status` ENUM('fila','executando','ok','erro','cancelado') NOT NULL DEFAULT 'fila',
  `tentativas` INT NOT NULL DEFAULT 0,
  `ultima_msg` VARCHAR(1000) NULL,
  `scheduled_at` DATETIME NULL,
  `started_at` DATETIME NULL,
  `finished_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_crm_jobs_tipo` (`tipo`),
  KEY `idx_crm_jobs_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==============================================
-- 24) ÍNDICES RECOMENDADOS (já criados na maioria das tabelas)
-- ==============================================
-- (Nenhuma ação; referência apenas)

-- FIM DO SCRIPT
