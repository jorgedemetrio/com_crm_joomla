# 📘 Documento Técnico — Padrões e Diretrizes para `com_crm_joomla`

> [!IMPORTANT]
> **Este documento é um resumo de alto nível.** Para uma explicação técnica detalhada sobre a estrutura de pastas, namespaces e o fluxo MVC do Joomla 5, **consulte o novo [Guia de Desenvolvimento Joomla 5](./dev/JOOMLA5_DEVELOPMENT_GUIDE.md)**.

---

## 1. Escopo do Componente

- **Joomla 5**
- **Foco no Backend (`administrator/`)**: A maior parte da lógica reside no backend.
- **Frontend Mínimo (`site/`)**: O frontend terá apenas controladores específicos para tarefas de tracking e opt-out, sem views complexas.
  - `link.acesso` (redirecionamento e rastreamento de cliques)
  - `tracking.open` (pixel de abertura de e-mail)
  - `optout.unsubscribe` (descadastro de e-mail)

---

## 2. Estrutura de Pastas (Padrão Joomla 5 com `src`)

A estrutura do projeto segue o padrão moderno do Joomla 5, que é baseado em namespaces e no diretório `src`. A estrutura legada (com pastas `controllers`, `models`, etc., soltas) **não deve ser usada**.

```
/com_crm_joomla/
├── administrator/
│   ├── forms/              # Definições de formulário em XML
│   ├── language/           # Arquivos de idioma (ex: en-GB/en-GB.com_crm_joomla.ini)
│   ├── sql/                # Scripts de banco de dados
│   └── src/                # ---> Código-fonte principal do backend
│       ├── Component/
│       ├── Controller/
│       ├── Model/
│       ├── Table/
│       └── View/
├── site/
│   └── src/                # ---> Código-fonte principal do frontend
└── com_crm_joomla.xml      # Arquivo de manifesto
```

> Para detalhes sobre o que vai em cada pasta do `src`, consulte o guia de desenvolvimento.

---

## 3. Convenções de Nomes (com Namespaces)

- **Tabelas**: `#__crm_*` (ex: `#__crm_leads`).
- **Classes**: Seguem o padrão PSR-4, correspondendo à estrutura de pastas.
  - **Models**: `Joomla\Component\Crm\Administrator\Model\LeadsModel`
  - **Tables**: `Joomla\Component\Crm\Administrator\Table\LeadTable`
  - **Controllers**: `Joomla\Component\Crm\Administrator\Controller\LeadsController`
- **Views**: A nomenclatura de views para listas (plural) e formulários (singular) é mantida.
  - **Lista**: `View/Leads/`
  - **Formulário**: `View/Lead/`

---

## 4) Padrão de colunas (auditoria/observabilidade)

```
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
`alteracao_ip_proxy` VARCHAR(45) NULL
```

- Usar **UUID (CHAR(36))** como PK por padrão.  
- `AUTO_INCREMENT` apenas em tabelas auxiliares ou de log.  
- Criar **índices** para campos de busca (e-mail normalizado, telefone, site).  

---

## 5) Tabelas núcleo do projeto

### 5.1 Leads

- Tabela: `#__crm_leads`
- PK: `id` (UUID)
- **Regra de Validade**: Um lead é considerado válido se possuir um `email` VÁLIDO **OU** um `telefone` VÁLIDO.
- **Campos de Contato Obrigatórios**: `email` ou `telefone1`. O campo `site` é opcional.
- Campos principais: `razao_social`, `nome_fantasia`, `site`, `email`, `descricao` (meta description)
- Campos auxiliares: endereço, telefones, `email_norm`, `telefone_norm`
- Índices: `idx_site`, `idx_email_norm`, `idx_tel_norm`  
- Auditoria: conforme padrão acima  

### 5.2 Campanhas & Disparos

- `#__crm_campanhas` → CRUD de campanhas  
- `#__crm_campanha_emails` → templates HTML  
- `#__crm_email_envios` → instâncias de envio (por lead)  
- `#__crm_email_opens` → registro de aberturas (pixel)  
- `#__crm_email_optout` → controle de opt-out (hash SHA-256 do e-mail)  

### 5.3 Links de Campanha

- `#__crm_campanha_links` → link UUID, short provider, alias local  
- `#__crm_campanha_link_lead` → acessos agregados por lead  
- `#__crm_campanha_link_clicks` → log de cada clique  

### 5.4 Configurações / Integrações / Exportação

- `#__crm_integracoes` → providers e parâmetros (Google, Mailchimp, etc.)  
- `#__crm_export_profiles` → perfis de exportação (flags: criar user, grupo, reset)  
- `#__crm_export_scripts` → scripts SQL/externos por profile  
- `#__crm_export_runs` → auditoria das execuções  
- `#__crm_export_map*` → (opcional) mapeamento campo-a-campo  

---

## 6) Manifesto (`com_crm_joomla.xml`)

- `<administration>` define menus: Leads, Campanhas, Links, Integrações, Exportação, Logs  
- `<files>`: inclui `administrator/*`, `site/*` (controllers mínimos), `media/*`  
- `<languages>`: `pt-BR`, `en-GB`  
- `<install>` / `<update>`: apontar SQL  
- `<config>`: carregar `administrator/config.xml`  

---

## 7) Controllers (Admin / Site)

- **Admin**: importações (CSV/Web/Redes), validações, disparos (Mailchimp), exportação (CSV/SugarCRM/tabelas internas)  
- **Site (mínimos)**:  
  - `link.acesso` → logar clique e redirecionar  
  - `tracking.open` → logar abertura e retornar GIF/PNG 1×1  
  - `optout.unsubscribe` → registrar descadastro  

---

## 8) Segurança, LGPD e Qualidade

- **Admin-only**: ACL por tarefa
- **CSRF** em todos os forms
- **Transações** em exportações SQL
- **Fila/Jobs** para processos longos (crawling, validação, disparos)
- **LGPD**: armazenar hashes (opt-out), evitar PII em logs, retenção configurável
- **CI/CD**: PHPStan, PHPUnit, SonarCloud, Composer PSR-4, code style

---
