# 📑 Especificação Funcional — `com_crm_joomla` (Admin-only)

> Documento dividido em **módulos funcionais**.  
> Regra principal: **Lead válido = (E-mail válido) OU (Telefone válido)**.  
> **Site é opcional**.  
> Todos os CRUDs seguem padrão Joomla 5 Admin: **POST + token CSRF**, ACL por tarefa, auditoria padrão (`state`, `created`, `modified` etc.).

---

## 🔹 Funcional 1 — Grupo de Lead

**Objetivo**  
Agrupar leads para segmentação em campanhas.

**Campos**
- `nome` (obrigatório, único)

**Telas**
- Listar grupos (filtro por nome)
- Criar/Editar grupo (campo nome)
- Ações em massa: publicar, despublicar, excluir lógico

**Regras**
- Um lead pode pertencer a vários grupos.
- Campanhas só podem usar grupos existentes.

---

## 🔹 Funcional 2 — Campanha

**Objetivo**  
Controlar o ciclo de marketing (e-mails, SMS, links).

**Campos**
- `nome` (obrigatório)
- `grupos_lead[]` (multiselect; ao menos um grupo)
- `status` (rascunho, aprovada, em_envio, finalizada, pausada)

**Telas**
- Listar campanhas (filtros: nome, status)
- Criar/Editar campanha: selecionar grupos, nome, status
- Ações: publicar, despublicar, excluir lógico

**Regras**
- Grupos selecionados aqui limitam quais podem ser usados em e-mails e SMS da campanha.

---

## 🔹 Funcional 3 — Link de Campanha

**Objetivo**  
Gerar links rastreáveis por campanha.

**Campos**
- `nome`
- `url_destino`
- `campanha_id`
- `short_url` (via API Google Short Link, se configurado; fallback = redirect interno)
- `alias_local` (UUID/slug único usado no redirect público)

**Telas**
- Listar links por campanha
- Criar/Editar link

**Regras**
- URL no disparo sempre usa redirect interno:  
  `index.php?option=com_crm_joomla&task=link.acesso&id=<UUID>&idlead=<LEAD_ID>`
- Cliques gravam estatísticas por link e por lead.

---

## 🔹 Funcional 4 — E-mail Marketing

**Objetivo**  
Criar templates HTML de e-mail vinculados à campanha.

**Campos**
- `titulo`
- `html` (editor, aceita tokens `%COLUNA%` e `%LINK:<ID>%`)
- `campanha_id`
- `links_usados[]`
- `grupos_disparo[]` (subconjunto dos grupos da campanha)

**Telas**
- Listar templates de e-mail
- Criar/Editar template (preview com substituição de tokens)

**Regras**
- No disparo: substituir `%COLUNA%` por valores do lead, `%LINK:<ID>%` por URL redirect.
- Incluir pixel `<img>` 1×1 para registrar abertura.

---

## 🔹 Funcional 5 — SMS Marketing

**Objetivo**  
Criar templates de SMS vinculados à campanha.

**Campos**
- `texto` (com tokens `%COLUNA%` e `%LINK:<ID>%`)
- `campanha_id`
- `links_usados[]`
- `grupos_disparo[]` (subconjunto da campanha)

**Telas**
- Listar templates de SMS
- Criar/Editar template (contador de caracteres, validação)

**Regras**
- Disparo via **Zenvia**.  
- Substituições de tokens iguais ao e-mail.  
- Links curtos com redirect interno.

---

## 🔹 Funcional 6 — Agendamento de Disparo

**Objetivo**  
Agendar execuções de envio em lote.

**Campos**
- `tipo` (email | sms)
- `campanha_id`
- `template_id`
- `inicio_em` (datetime)
- `lote_qtde`, `delay_item_ms`, `gap_lote_s`
- `status_job` (agendado, em_execucao, finalizado, pausado, erro)

**Regras**
- Executado por controlador específico de jobs.  
- Seleção de leads = interseção de grupos do template e da campanha.  
- Respeita regras de opt-out.  
- Logs de cada envio armazenados.

---

## 🔹 Funcional 7 — Lead

**Objetivo**  
Gerenciar leads (CRUD).

**Filtros**
- nome/razão social
- origem (internet, csv, manual)
- status (validado/reprovado)
- cidade/UF
- possui e-mail / possui telefone
- datas

**Cadastro/Edição**
- Regra mínima: **e-mail OU telefone** válido
- Campos do CSV (CNPJ, Razão, Fantasia, Situação, Sócios, Endereço, etc)
- Campo `site` opcional
- Grupos de lead (multisseleção)

**Ações em massa**
- Associar/remover em grupo
- Exportar CSV
- Solicitar validação de e-mail
- Validar telefones

---

## 🔹 Funcional 8 — Importação via Web

**Tela “Nova Importação Web”**
- `nome`
- `origem`: Google | LinkedIn | Facebook | Instagram
- `palavras_chave`, `localizacao`, `limite_resultados`
- `pasta_imagens` (logo/fotos)
- `grupos_destino[]`

**Processo**
- Crawling com rate limit
- Extrair e-mail, telefone, site, descrição, endereço
- Validar/deduplicar
- Associar a grupos destino

---

## 🔹 Funcional 9 — Importação de Arquivo

**Cadastro**
- `nome`
- `arquivo` (CSV)
- `grupos_destino[]`

**Execução**
- Preview, mapeamento de colunas
- Regras: rejeitar sem contato, deduplicar, enriquecer descrição
- Associar a grupos

---

## 🔹 Funcional 10 — Relatórios de Envio

**Dashboard por campanha**
- Enviados
- Lidos (pixel)
- Clicados (links)
- Erros (SMTP/API)
- Bounces (POP/IMAP)
- Status

**Drill-down**
- Links clicados (total por link)
- Lista de envios com status (enviado, lido, clicado, erro)

---

## 🔹 Funcional 11 — Relatório de Registros

**Objetivo**  
Monitorar registros gerados via campanhas/links.

**Consultas configuráveis**
- Qtde por campanha
- Qtde por link

---

## 🔹 Funcional 12 — Importação Interna (de `#__users`)

**Tela**
- Seleção por grupo Joomla
- Opções:
  - Criar lead se não existir `email_norm`
  - Associar a grupos de lead

**Processo**
- Lote, com logs
- Deduplicação aplicada

---

## 🔹 Funcional 13 — Configuração

**Envio**
- Delay entre e-mails
- Qtde por lote
- Delay entre SMS
- Qtde por lote SMS

**Integrações**
- Google Search, Google Short Link
- LinkedIn
- POP/IMAP (validar bounces)
- SMS (Zenvia)
- Mailchimp
- SugarCRM

**Consultas**
- Query de exportação (inserts com `%CAMPO%`)
- Query qtde registros por campanha
- Query qtde registros por link

---

## 🔹 Funcional 14 — Disparo de E-mail

**Regras**
- Lotes + intervalos da Config
- Usar JMail (SMTP se configurado no Joomla)
- Tokens: `%COLUNA%`, `%LINK:<ID>%`
- Pixel de abertura incluído
- Try/catch por item; registrar sucesso/erro

---

## 🔹 Funcional 15 — Disparo de SMS (Zenvia)

**Regras**
- Lotes + intervalos da Config
- Usar API Zenvia
- Tokens: `%COLUNA%`, `%LINK:<ID>%`
- Try/catch por item; registrar sucesso/erro
- Opt-out respeitado

---
