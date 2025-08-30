# 🗄️ MER — Modelo Entidade-Relacionamento do `com_crm_joomla`

> Documento técnico descrevendo as entidades, relacionamentos e chaves principais do componente **Admin-only** `com_crm_joomla`.

---

## 1) Entidades Principais

- **Leads** (`#__crm_leads`)  
  - Armazena leads capturados/importados.  
  - PK: `id` (UUID).  
  - Campos obrigatórios: **`email`** ou **`telefone`**.
  - Campo `descricao`: meta description da homepage.  
  - Inclui todos os campos vindos do CSV de importação.
  - **Campos (do CSV + adicionais de CRM):**
  - **Identificação da empresa**
    - `cnpj`
    - `razao_social`
    - `nome_fantasia`
    - `matriz_filial` (ex.: Matriz / Filial)
    - `situacao` (Ativa/Inativa)
    - `natureza` (código)
    - `natureza_desc`
    - `qualificacao_responsavel_id`
    - `qualificacao_responsavel_desc`
    - `porte`
    - `capital_social`
  
  - **Endereço**
    - `logradouro`
    - `numero`
    - `complemento`
    - `bairro`
    - `cep`
    - `cidade`
    - `estado`
    - `pais` (default Brasil)
  
  - **Contato (empresa)**
    - `email` (obrigatório, normalizado em `email_norm`)
    - `telefone1` / `tipo_telefone1`
    - `telefone2` / `tipo_telefone2`
    - (expandível para N contatos em tabelas auxiliares no futuro)
  
  - **Sócios (estrutura repetida até Sócio4 no CSV)**
    - `nome_socio1`, `tipo_socio_id1`, `tipo_socio_desc1`, `telefone1_socio1`, `email1_socio1`, `email2_socio1`
    - `nome_socio2`, `tipo_socio_id2`, `tipo_socio_desc2`, `telefone1_socio2`, `email1_socio2`, `email2_socio2`
    - `nome_socio3`, `tipo_socio_id3`, `tipo_socio_desc3`, `telefone1_socio3`, `email1_socio3`, `email2_socio3`
    - `nome_socio4`, `tipo_socio_id4`, `tipo_socio_desc4`, `telefone1_socio4`, `email1_socio4`, `email2_socio4`
  
  - **Origem e rastreio**
    - `site` (opcional, origem do lead)
    - `url_origem` (quando veio do Google/LinkedIn/etc)
    - `origem` (enum: CSV, Google, LinkedIn, Instagram, Facebook, Manual)
    - `descricao` (meta description da homepage)
    - `observacoes`
  
  - **Normalizações (para deduplicação)**
    - `email_norm` (LOWER(email))
    - `telefone_norm` (somente dígitos)
    - `ddd_telefone_norm` (somente dígitos, máximo 4)
  
  - **Status**
    - `status` (NOVO, VALIDADO, REPROVADO, MIGRADO)
  
  - **Campos de auditoria** (padrão do componente)
    - `state`
    - `ordering`
    - `checked_out`, `checked_out_time`
    - `created`, `created_by`
    - `modified`, `modified_by`
    - `criacao_session_id`, `criacao_tracking_id`, `criacao_ip`, `criacao_ip_proxy`
    - `alteracao_session_id`, `alteracao_tracking_id`, `alteracao_ip`, `alteracao_ip_proxy`


- **Campanhas** (`#__crm_campanhas`)  
  - PK: `id` (UUID).  
  - Representa uma campanha de marketing.  
  - Relaciona-se com e-mails, links e envios.  

- **Campanha E-mails** (`#__crm_campanha_emails`)  
  - Templates HTML vinculados a campanhas.  

- **Envios de E-mail** (`#__crm_email_envios`)  
  - Disparos de e-mail por lead.  
  - Relacionado a `leads` e `campanhas`.  

- **Aberturas de E-mail** (`#__crm_email_opens`)  
  - Registro de leitura (pixel 1px).  
  - FK para `envios`.  

- **Opt-out** (`#__crm_email_optout`)  
  - Controle de descadastro global ou por campanha.  

- **Campanha Links** (`#__crm_campanha_links`)  
  - Links usados em campanhas, com shortlink provider.  
  - Relacionados a leads via tabela associativa.  

- **Associação Link × Lead** (`#__crm_campanha_link_lead`)  
  - Quantidade de acessos de um lead a um link.  

- **Cliques em Links** (`#__crm_campanha_link_clicks`)  
  - Log detalhado de cada clique.  

- **Integrações** (`#__crm_integracoes`)  
  - Configurações de provedores externos (Google, Mailchimp, etc).  

- **Exportação** (`#__crm_export_profiles`, `#__crm_export_scripts`, `#__crm_export_runs`)  
  - Regras de exportação de leads qualificados.  
  - Suporte a SQL, SugarCRM, Mailchimp, CSV.  

---

## 2) Diagrama ER (ASCII simplificado)

"""
[LEADS] --------------------< [EMAIL_ENVIO] >-------------------- [CAMPANHAS]
   |                               |                                  |
   |                               v                                  v
   |                          [EMAIL_OPENS]                  [CAMPANHA_EMAILS]
   |
   |---< [CAMPANHA_LINK_LEAD ] >--- [CAMPANHA_LINKS] ---< [CAMPANHA_LINK_CLICKS]
   |
   v
[EMAIL_OPTOUT]

[INTEGRACOES] (config)
[EXPORT_PROFILES] --< [EXPORT_SCRIPTS] --< [EXPORT_RUNS]
"""

---

## 3) Relacionamentos

- **Lead 1:N Envios**  
  - Um lead pode receber vários envios de e-mail.  

- **Envio 1:N Aberturas**  
  - Um envio pode ter várias aberturas (tracking pixel).  

- **Lead N:M Links (via associativa)**  
  - Um lead pode clicar em vários links de campanha.  
  - Cada link pode ser clicado por vários leads.  

- **Campanha 1:N E-mails**  
  - Uma campanha pode ter múltiplos templates.  

- **Campanha 1:N Links**  
  - Uma campanha pode conter vários links rastreáveis.  

- **Exportação**  
  - Um `profile` pode ter vários `scripts`.  
  - Cada execução (`runs`) referencia um profile + lead.  

---

## 4) Chaves e Índices

- **Leads**  
  - PK: `id` (UUID)  
  - Índices: `idx_site`, `idx_email_norm`, `idx_tel_norm`  
  - `email` ou `telefone1` são **chaves obrigatórias** para persistir um lead. O campo `site` é opcional.
  - CSV trouxe múltiplos sócios, por ora mantidos como campos repetidos no lead; em fase posterior pode-se normalizar em `#__crm_lead_socios`.  
  - **Deduplicação** na importação:  
  - Se e-mail já existir (`email_norm`), não salva.  
  - Se e-mail não existir mas telefone já existir (`telefone_norm`), não salva.  
  - `descricao` vem do **meta description** da homepage do `site`.  

- **Envios**  
  - FK: `campanha_id` → `#__crm_campanhas.id`  
  - FK: `lead_id` → `#__crm_leads.id`  

- **Aberturas**  
  - FK: `envio_id` → `#__crm_email_envios.id`  

- **Links**  
  - PK: `id` (UUID)  
  - FK: `campanha_id` → `#__crm_campanhas.id`  

- **Associativa Link × Lead**  
  - UNIQUE (`link_id`, `lead_id`)  

- **Cliques**  
  - FK: `link_id` → `#__crm_campanha_links.id`  

- **Opt-out**  
  - UNIQUE (`email_hash`, `scope`, `campanha_id`)  

---

## 5) Observações

- Todas as tabelas seguem padrão de **auditoria** (`state`, `ordering`, `created`, `created_by`, `modified`, etc).  
- Preferência por **UUID** em entidades principais; `AUTO_INCREMENT` apenas em tabelas auxiliares.  
- **Dados pessoais sensíveis** (e-mail, telefone) normalizados para deduplicação.  
- **Opt-out** armazena apenas `email_hash` (SHA-256) por LGPD.  
- Logs de clique/abertura armazenam IP/UA apenas se permitido.  

---
