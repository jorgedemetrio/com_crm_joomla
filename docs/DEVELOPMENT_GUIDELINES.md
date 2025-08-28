# 📘 Documento Técnico — Padrões (Admin-only) para `com_crm_joomla`

> Este documento consolida somente o que usaremos neste projeto (admin-only). **Antes de codar**, alinhe o time com o `DEVELOPMENT_GUIDELINES.md`.

---

## 1) Escopo do componente

- **Joomla 5**  
- **Somente Administrador** (`administrator/`)  
- **Site/** terá apenas controladores mínimos para:
  - `link.acesso` (redirect + tracking de clique)  
  - `tracking.open` (pixel 1×1 de abertura)  
  - `optout.unsubscribe` (descadastro)  

---

## 2) Estrutura de pastas

```
/com_crm_joomla/
├─ administrator/
│  ├─ config/                     # params, esquemas, presets
│  ├─ controllers/                # ex.: LeadsController, CampanhasController
│  ├─ models/                     # Table*, Form*, ModelList, ModelItem
│  ├─ services/                   # integrações (Google, Mailchimp, etc.)
│  │  ├─ import/
│  │  ├─ validate/
│  │  ├─ export/
│  │  └─ shortlinks/
│  ├─ views/                      # MVC Admin (grids, forms)
│  ├─ sql/
│  │  ├─ install.sql              # criação de tabelas
│  │  └─ updates/mysql/           # scripts de migração
│  ├─ helpers/                    # Slug, MetaFetcher, EmailValidator...
│  ├─ language/                   # pt-BR, en-GB
│  ├─ access.xml                  # regras de ACL
│  ├─ config.xml                  # parâmetros do componente
│  └─ com_crm_joomla.php          # entrypoint Admin
├─ site/
│  ├─ controllers/                # link.acesso, tracking.open, optout.unsubscribe
│  └─ router.php                  # rotas dessas ações
├─ media/com_crm_joomla/          # assets Admin (js, css, imgs)
├─ com_crm_joomla.xml             # manifest (instalação)
└─ index.html
```

---

## 3) Convenções de nomes

- **Tabelas**: `#__crm_*` (ex.: `#__crm_leads`, `#__crm_campanhas`)  
- **Models**: `Administrator\Model\LeadsModel`  
- **Tables**: `Administrator\Table\LeadTable` → `tables/lead.php`  
- **Controllers**: `Administrator\Controller\LeadsController`  
- **Views**: `views/leads` (list) e `views/lead` (form)
- **Nomenclatura de Classes (regra específica)**: Para garantir consistência, as classes de Controller e View devem seguir o padrão `NomeDoProjetoControllerNomeDoNegocio` e `NomeDoProjetoViewNomeDoNegocio`. Por exemplo, a classe para um controller de "Leads" no projeto `com_crm_joomla` seria `ComCrmJoomlaControllerLeads`.

> **Nota de Implementação**: Para as classes do núcleo MVC (Controllers, Models, Views, Tables), a convenção de nomenclatura padrão do Joomla 5 (ex: `LeadsController`, `HtmlView`) é mantida para garantir a compatibilidade com o dispatcher do framework. A regra de nomenclatura `NomeDoProjeto...` pode ser aplicada em outras classes de serviço ou helpers.

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
