-- Adiciona o campo de status à tabela de importação de arquivos
ALTER TABLE `#__crm_import_arquivo` ADD COLUMN `status` ENUM('pendente','processando','processado','falha') NOT NULL DEFAULT 'pendente' AFTER `arquivo_path`;
