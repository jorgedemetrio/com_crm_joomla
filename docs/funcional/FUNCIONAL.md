# 📑 Especificação Funcional — `com_crm_joomla` (Admin-only)

> Documento dividido em **módulos funcionais**.  
> Regra principal: **Lead válido = (E-mail válido) OU (Telefone válido)**.  
> **Site é opcional**.  
> Todos os CRUDs seguem padrão Joomla 5 Admin: **POST + token CSRF**, ACL por tarefa, auditoria padrão (`state`, `created`, `modified` etc.).

---

## 🔹 Funcional 1 — Grupo de Lead

**Objetivo**  
Agrupar leads para segmentação em campanhas.

**Tabela Principal:** `#__crm_lead_groups`

**Campos**
- `id` (CHAR 36) - UUID, chave primária
- `nome` (VARCHAR 150, obrigatório, único)
- Campos de auditoria: `state`, `ordering`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`
- Campos de rastreamento: `criacao_session_id`, `criacao_tracking_id`, `criacao_ip`, `criacao_ip_proxy`, `alteracao_session_id`, `alteracao_tracking_id`, `alteracao_ip`, `alteracao_ip_proxy`

**Telas**

**Tela de Listagem (`gruposlead`):**
- **Consulta:** `SELECT id, nome, state, created FROM #__crm_lead_groups WHERE state >= 0 ORDER BY ordering, nome`
- **Filtros:** nome, state, datas
- **Ações em massa:** Publicar (state=1), Despublicar (state=0), Excluir lógico (state=-2)
- **Campos exibidos:** Checkbox, Nome, Status, Data de Criação, ID

**Tela de Edição/Novo (`grupolead`):**
- **Consulta (edição):** `SELECT * FROM #__crm_lead_groups WHERE id = ?`
- **Gravação (novo):** `INSERT INTO #__crm_lead_groups (id, nome, created_by, created) VALUES (UUID(), ?, ?, NOW())`
- **Atualização:** `UPDATE #__crm_lead_groups SET nome = ?, modified_by = ?, modified = NOW() WHERE id = ?`
- **Validações:** Nome obrigatório e único
- **Campos do formulário:** Nome, Status

**Relacionamentos**
- N:M com `#__crm_leads` via `#__crm_lead_group_map`
- N:M com `#__crm_campanhas` via `#__crm_campanha_group_map`

**Regras**
- Um lead pode pertencer a vários grupos.
- Campanhas só podem usar grupos existentes.
- UUID gerado automaticamente na criação.

---

## 🔹 Funcional 2 — Campanha

**Objetivo**  
Controlar o ciclo de marketing (e-mails, SMS, links).

**Tabelas:**
- Principal: `#__crm_campanhas`
- Associação de grupos: `#__crm_campanha_group_map`

**Campos**
- `id` (CHAR 36) - UUID, chave primária
- `nome` (VARCHAR 150, obrigatório)
- `status` (ENUM: rascunho, aprovada, em_envio, finalizada, pausada)
- `grupos_lead[]` (N:M via `#__crm_campanha_group_map`)
- Campos de auditoria padrão + rastreamento

**Telas**

**Tela de Listagem (`campanhas`):**
- **Consulta:** `SELECT c.*, COUNT(DISTINCT e.id) as qtd_emails, COUNT(DISTINCT s.id) as qtd_sms, COUNT(DISTINCT l.id) as qtd_links FROM #__crm_campanhas c LEFT JOIN #__crm_campanha_emails e ON c.id=e.campanha_id LEFT JOIN #__crm_campanha_sms s ON c.id=s.campanha_id LEFT JOIN #__crm_campanha_links l ON c.id=l.campanha_id GROUP BY c.id`
- **Filtros:** nome, status, datas
- **Ações em massa:** Publicar, Despublicar, Alterar status
- **Campos exibidos:** Checkbox, Nome, Status, Qtd. E-mails, Qtd. SMS, Qtd. Links, Data Criação

**Tela de Edição/Novo (`campanha`):**
- **Consulta (edição):** `SELECT c.*, GROUP_CONCAT(m.group_id) as grupos FROM #__crm_campanhas c LEFT JOIN #__crm_campanha_group_map m ON c.id=m.campanha_id WHERE c.id = ? GROUP BY c.id`
- **Gravação (novo):** 
  - `INSERT INTO #__crm_campanhas (id, nome, status, created_by) VALUES (UUID(), ?, ?, ?)`
  - Para cada grupo: `INSERT INTO #__crm_campanha_group_map (campanha_id, group_id) VALUES (?, ?)`
- **Atualização:** 
  - `UPDATE #__crm_campanhas SET nome = ?, status = ?, modified_by = ?, modified = NOW() WHERE id = ?`
  - `DELETE FROM #__crm_campanha_group_map WHERE campanha_id = ?`
  - `INSERT INTO #__crm_campanha_group_map ...` (para grupos selecionados)
- **Validações:** Nome obrigatório, pelo menos um grupo selecionado
- **Campos do formulário:** Nome, Status, Grupos (multiselect)
- **Sub-abas:** Templates de E-mail, Templates de SMS, Links

**Relacionamentos**
- N:M com `#__crm_lead_groups` via `#__crm_campanha_group_map`
- 1:N com `#__crm_campanha_emails`, `#__crm_campanha_sms`, `#__crm_campanha_links`, `#__crm_agendamentos`

**Regras**
- Grupos selecionados aqui limitam quais podem ser usados em e-mails e SMS da campanha.
- Status controla o ciclo de vida da campanha.

---

## 🔹 Funcional 3 — Link de Campanha

**Objetivo**  
Gerar links rastreáveis por campanha.

**Tabelas:**
- Principal: `#__crm_campanha_links`
- Log de cliques: `#__crm_campanha_link_clicks`
- Agregação por lead: `#__crm_campanha_link_lead`

**Campos**
- `id` (CHAR 36) - UUID, usado no redirect
- `campanha_id` (CHAR 36) - FK para campanhas
- `nome` (VARCHAR 200), `url_destino` (TEXT)
- `provider` (VARCHAR 50) - Google, interno
- `alias_local` (VARCHAR 64, UNIQUE) - Slug para redirect
- `clicks_total` (INT), `last_click` (DATETIME)
- Campos de auditoria padrão + rastreamento

**Telas**

**Listagem (`linkscampanha`):**
- **Consulta:** `SELECT * FROM #__crm_campanha_links WHERE campanha_id = ? ORDER BY ordering`
## 🔹 Funcional 4 — E-mail Marketing

**Objetivo**  
Criar templates HTML de e-mail vinculados à campanha.

**Tabelas:**
- Principal: `#__crm_campanha_emails`
- Envios: `#__crm_email_envios`
- Aberturas: `#__crm_email_opens`
- Opt-out: `#__crm_email_optout`

**Campos**
- `id` (CHAR 36) - UUID
- `campanha_id` (CHAR 36) - FK para campanhas
- `titulo` (VARCHAR 200) - Assunto do e-mail
- `html` (MEDIUMTEXT) - Corpo HTML com tokens `%CAMPO%` e `%LINK:<UUID>%`
- Campos de auditoria padrão + rastreamento

**Telas**

**Listagem (`campanhaemails`):**
- **Consulta:** `SELECT * FROM #__crm_campanha_emails WHERE campanha_id = ? ORDER BY ordering`
- **Filtros:** titulo, state | **Ações:** Publicar, Despublicar, Excluir
- **Campos:** Checkbox, Título, Status, Data Criação

**Edição/Novo (`campanhaemail`):**
- **Consulta:** `SELECT * FROM #__crm_campanha_emails WHERE id = ?`
- **Gravação:** `INSERT INTO #__crm_campanha_emails (id, campanha_id, titulo, html, created_by) VALUES (UUID(), ?, ?, ?, ?)`
- **Atualização:** `UPDATE #__crm_campanha_emails SET titulo = ?, html = ?, modified_by = ?, modified = NOW() WHERE id = ?`
- **Campos:** Título, Editor HTML (com botões para tokens e links), Preview

**Processo de Envio:**
1. `SELECT l.* FROM #__crm_leads l INNER JOIN #__crm_lead_group_map m ON l.id=m.lead_id WHERE m.group_id IN (grupos_template) AND l.email IS NOT NULL`
2. Para cada lead:
   - Substituir tokens: `%RAZAO_SOCIAL%`, `%EMAIL%`, etc
   - Substituir links: `%LINK:<UUID>%` → URL redirect
   - Incluir pixel: `<img src="index.php?option=com_crm&task=email.open&mid=<ENVIO_ID>" width="1" height="1"/>`
   - `INSERT INTO #__crm_email_envios (campanha_id, email_template_id, lead_id, email_destino, status) VALUES (..., 'fila')`
   - Enviar via JMail/SMTP
   - `UPDATE #__crm_email_envios SET status = 'enviado', enviado_em = NOW() WHERE id = ?`

**Rastreamento de Abertura (Controller `email.open`):**
1. `SELECT * FROM #__crm_email_envios WHERE id = ?`
2. `INSERT INTO #__crm_email_opens (campanha_id, envio_id, ip, user_agent) VALUES (...)`
3. `UPDATE #__crm_email_envios SET status = 'lido' WHERE id = ? AND status NOT IN ('clicado')`
4. Retornar imagem 1x1 transparente

**Validação Opt-out:**
- Antes de enviar: `SELECT * FROM #__crm_email_optout WHERE email_hash = SHA2(LOWER(?), 256) AND (scope = 'global' OR (scope = 'campanha' AND campanha_id = ?))`

**Regras**
- No disparo: substituir `%COLUNA%` por valores do lead, `%LINK:<ID>%` por URL redirect.
- Incluir pixel `<img>` 1×1 para registrar abertura.
- Respeitar opt-outs globais e por campanha.
  `index.php?option=com_crm_joomla&task=link.acesso&id=<UUID>&idlead=<LEAD_ID>`
- Cliques gravam estatísticas por link e por lead.
- Cada clique gera registro em `#__crm_campanha_link_clicks` e atualiza contadores.

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

**Tabelas:**
- Principal: `#__crm_campanha_sms`
- Envios: `#__crm_sms_envios`

**Campos**
- `id` (CHAR 36) - UUID
- `campanha_id` (CHAR 36) - FK para campanhas
- `texto` (VARCHAR 612) - Até ~4 SMS concatenados, tokens `%CAMPO%` e `%LINK:<UUID>%`
- Campos de auditoria padrão + rastreamento

**Telas**

**Listagem (`campanhasmslist`):**
- **Consulta:** `SELECT * FROM #__crm_campanha_sms WHERE campanha_id = ? ORDER BY ordering`
- **Filtros:** state | **Ações:** Publicar, Despublicar, Excluir
- **Campos:** Checkbox, Preview do Texto (50 chars), Status, Data Criação

## 🔹 Funcional 6 — Agendamento de Disparo

**Objetivo**  
Agendar execuções de envio em lote.

**Tabela Principal:** `#__crm_agendamentos`

**Campos**
- `id` (CHAR 36) - UUID
- `tipo` (ENUM: email, sms)
- `campanha_id` (CHAR 36), `template_id` (CHAR 36)
- `inicio_em` (DATETIME) - Data/hora agendada
- `lote_qtde` (INT), `delay_item_ms` (INT), `gap_lote_s` (INT)
- `status_job` (ENUM: agendado, em_execucao, finalizado, pausado, erro)
- `erro_msg` (VARCHAR 1000)

**Telas**

**Listagem (`agendamentos`):**
- **Consulta:** `SELECT a.*, c.nome as campanha_nome FROM #__crm_agendamentos a INNER JOIN #__crm_campanhas c ON a.campanha_id=c.id ORDER BY inicio_em DESC`
- **Filtros:** tipo, status_job, campanha_id, inicio_em | **Ações:** Pausar, Retomar, Cancelar
- **Campos:** Checkbox, Tipo, Campanha, Template, Início Em, Status, Progresso

**Edição/Novo (`agendamento`):**
- **Consulta:** `SELECT * FROM #__crm_agendamentos WHERE id = ?`
- **Gravação:** `INSERT INTO #__crm_agendamentos (id, tipo, campanha_id, template_id, inicio_em, lote_qtde, delay_item_ms, gap_lote_s, status_job, created_by) VALUES (UUID(), ?, ?, ?, ?, ?, ?, ?, 'agendado', ?)`
- **Atualização:** `UPDATE #__crm_agendamentos SET inicio_em = ?, lote_qtde = ?, ... WHERE id = ? AND status_job = 'agendado'`
- **Validações:** Tipo, campanha, template obrigatórios; template deve pertencer à campanha; inicio_em futuro
- **Campos:** Tipo (radio), Campanha (select), Template (select filtrado), Data/Hora, Configurações de Lote, Grupos de Destino

**Processo de Execução (Cron/Job):**
1. `SELECT * FROM #__crm_agendamentos WHERE status_job = 'agendado' AND inicio_em <= NOW()`
2. `UPDATE #__crm_agendamentos SET status_job = 'em_execucao' WHERE id = ?`
3. `SELECT l.* FROM #__crm_leads l INNER JOIN #__crm_lead_group_map m ON l.id=m.lead_id WHERE m.group_id IN (grupos) AND l.email IS NOT NULL`
4. Verificar opt-out, processar em lotes, INSERT em `#__crm_email_envios` ou `#__crm_sms_envios`
5. `UPDATE #__crm_agendamentos SET status_job = 'finalizado' WHERE id = ?`

**Regras**
- Executado por controlador específico de jobs (cron).  
- Seleção de leads = interseção de grupos do template e da campanha.  
- Respeita regras de opt-out (para e-mails).  
- Logs de cada envio armazenados em tabelas de envios.
- Apenas agendamentos 'agendado' podem ser editados.ao e-mail.  
- Links curtos com redirect interno.
- Apenas leads com telefone válido recebem SMS.

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

**Tabelas:**
- Principal: `#__crm_leads`
- Associação: `#__crm_lead_group_map`

**Campos Principais:**
- `id` (CHAR 36) - UUID
- **Dados empresariais:** `cnpj`, `razao_social`, `nome_fantasia`, `situacao`, `porte`, `natureza`, `capital_social`
- **Endereço:** `logradouro`, `numero`, `complemento`, `bairro`, `cep`, `cidade`, `estado`, `pais`
- **Contatos:** `email`, `telefone1`, `tipo_telefone1`, `telefone2`, `tipo_telefone2`
- **Normalizados:** `email_norm` (VIRTUAL), `telefone_norm`
- **Sócios (até 4):** `nome_socio1..4`, `tipo_socio_id1..4`, `telefone1_socio1..4`, `email1_socio1..4`
- **Origem:** `origem` (ENUM), `url_origem`, `site`, `descricao`, `observacoes`
- **Status:** `status` (ENUM: NOVO, VALIDADO, REPROVADO, MIGRADO)

**Telas**

**Listagem (`leads`):**
- **Consulta:** `SELECT l.*, GROUP_CONCAT(g.nome) as grupos FROM #__crm_leads l LEFT JOIN #__crm_lead_group_map m ON l.id=m.lead_id LEFT JOIN #__crm_lead_groups g ON m.group_id=g.id WHERE l.state >= 0 GROUP BY l.id ORDER BY l.created DESC`
- **Filtros:** razao_social/nome_fantasia (LIKE), origem, status, cidade, estado, email IS NOT NULL, telefone1 IS NOT NULL, datas
- **Ações em massa:** Publicar/Despublicar, Associar/Remover grupos, Exportar CSV, Validar e-mails/telefones
- **Campos:** Checkbox, Razão Social/Nome Fantasia, E-mail, Telefone, Cidade/UF, Status, Origem, Grupos

## 🔹 Funcional 8 — Importação via Web

**Tabelas:**
- Principal: `#__crm_import_web`
- Log: `#__crm_import_execucoes`

**Campos:**
- `id` (CHAR 36) - UUID
- `nome` (VARCHAR 150)
- `origem` (ENUM: Google, LinkedIn, Facebook, Instagram)
- `palavras_chave` (VARCHAR 500), `localizacao` (VARCHAR 255), `limite_resultados` (INT), `pasta_imagens` (VARCHAR 500)

**Telas**

**Listagem (`importwebs`):**
- **Consulta:** `SELECT * FROM #__crm_import_web ORDER BY created DESC`
- **Filtros:** nome, origem, created | **Ações:** Executar, Editar, Excluir
- **Campos:** Nome, Origem, Palavras-Chave, Limite, Status, Data

**Edição/Novo (`importweb`):**
- **Consulta:** `SELECT * FROM #__crm_import_web WHERE id = ?`
- **Gravação:** `INSERT INTO #__crm_import_web (id, nome, origem, palavras_chave, ..., created_by) VALUES (UUID(), ?, ?, ?, ..., ?)`
- **Atualização:** `UPDATE #__crm_import_web SET ... WHERE id = ?`
- **Campos:** Nome, Origem (select), Palavras-Chave, Localização, Limite, Pasta de Imagens, Grupos de Destino (multiselect)

**Processo de Execução (Task `importweb.executar`):**
1. `SELECT * FROM #__crm_import_web WHERE id = ?`
2. `SELECT * FROM #__crm_integracoes WHERE provider = ? AND ativo = 1`
3. `INSERT INTO #__crm_import_execucoes (tipo, referencia_id, status, started_at) VALUES ('web', ?, 'ok', NOW())`
4. Crawling/API: extrair dados, validar (e-mail OU telefone), verificar duplicata, INSERT em `#__crm_leads` e `#__crm_lead_group_map`
5. `UPDATE #__crm_import_execucoes SET status = ?, linhas_total = ?, linhas_sucesso = ?, linhas_falha = ?, log = ?, finished_at = NOW() WHERE id = ?`

**Regras**
- Crawling com rate limit
- Extrair e-mail, telefone, site, descrição, endereço
- Validar/deduplicar por `email_norm` e `telefone_norm`
- Associar a grupos destino
- Registrar log detalhadoaticamente (VIRTUAL)
- `telefone_norm` calculado removendo não-numéricos
- Deduplicação verificada antes de inserir
- Grupos associados via tabela N:M

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

**Tabelas:**
- Principal: `#__crm_import_arquivo`
- Log: `#__crm_import_execucoes`

**Campos:**
- `id` (CHAR 36) - UUID
- `nome` (VARCHAR 150)
- `arquivo_path` (VARCHAR 500) - Caminho do CSV

**Telas**

**Listagem (`importarquivos`):**
- **Consulta:** `SELECT * FROM #__crm_import_arquivo ORDER BY created DESC`
- **Filtros:** nome, created | **Ações:** Executar, Editar, Excluir, Download
- **Campos:** Nome, Arquivo, Data Upload, Status

**Edição/Novo (`importarquivo`):**
- **Consulta:** `SELECT * FROM #__crm_import_arquivo WHERE id = ?`
- **Gravação:** Upload CSV + `INSERT INTO #__crm_import_arquivo (id, nome, arquivo_path, created_by) VALUES (UUID(), ?, ?, ?)`
- **Atualização:** Upload novo arquivo + `UPDATE #__crm_import_arquivo SET nome = ?, arquivo_path = ?, ... WHERE id = ?`
- **Campos:** Nome, Upload CSV, Preview (5 linhas), Mapeamento de Colunas, Grupos de Destino

**Processo de Execução:**
1. `SELECT * FROM #__crm_import_arquivo WHERE id = ?`
2. Abrir CSV: `$file = fopen($arquivo_path, 'r')`
3. `INSERT INTO #__crm_import_execucoes (tipo, referencia_id, started_at) VALUES ('arquivo', ?, NOW())`
4. Preview/Mapeamento: detectar colunas (CNPJ, Razão, Email, Telefone)
5. Importação: validar (e-mail OU telefone), normalizar, verificar duplicata, INSERT em `#__crm_leads` e `#__crm_lead_group_map`
6. `UPDATE #__crm_import_execucoes SET status = ?, linhas_total = ?, linhas_sucesso = ?, linhas_falha = ?, log = ?, finished_at = NOW() WHERE id = ?`

**Regras**
- Preview com primeiras 5 linhas
- Mapeamento detectar/ajustar colunas
- Rejeitar sem e-mail E sem telefone
- Deduplicar por `email_norm` e `telefone_norm`
- Associar a grupos destino
- Estatísticas: total, sucesso, falha

---

## 🔹 Funcional 10 — Relatórios de Envio

**Tabelas Consultadas:**
- `#__crm_email_envios` - Instâncias de e-mails
- `#__crm_email_opens` - Aberturas (pixel)
- `#__crm_sms_envios` - Instâncias de SMS
- `#__crm_campanha_link_clicks` - Cliques em links

**Dashboard por Campanha:**
- **Consulta Enviados:** `SELECT COUNT(*) FROM #__crm_email_envios WHERE campanha_id = ? AND status IN ('enviado', 'lido', 'clicado')`
- **Consulta Lidos:** `SELECT COUNT(DISTINCT envio_id) FROM #__crm_email_opens WHERE campanha_id = ?`
- **Consulta Clicados:** `SELECT COUNT(DISTINCT lead_id) FROM #__crm_campanha_link_clicks WHERE campanha_id = ?`
- **Consulta Erros:** `SELECT COUNT(*) FROM #__crm_email_envios WHERE campanha_id = ? AND status IN ('erro', 'bounce')`
- **Métricas:** Enviados, Lidos (pixel), Clicados (links), Erros (SMTP/API), Bounces (POP/IMAP), Status

**Drill-down:**
- **Links Clicados:** `SELECT l.nome, l.url_destino, COUNT(c.id) as total_clicks FROM #__crm_campanha_links l LEFT JOIN #__crm_campanha_link_clicks c ON l.id=c.link_id WHERE l.campanha_id = ? GROUP BY l.id ORDER BY total_clicks DESC`
- **Lista de Envios:** `SELECT e.*, l.razao_social, l.email FROM #__crm_email_envios e INNER JOIN #__crm_leads l ON e.lead_id=l.id WHERE e.campanha_id = ? ORDER BY e.enviado_em DESC`
- **Campos:** Lead, Email/Telefone, Status (enviado, lido, clicado, erro), Data Envio, Erro

**Telas de Relatório:**
- **Listagem de Envios (`emailenvios`, `smsenvios`):** Consulta com filtros por campanha, status, período
- **Dashboard de Campanha:** Cards com métricas agregadas, gráficos de progresso

---

## 🔹 Funcional 11 — Relatório de Registros

**Objetivo**  
Monitorar registros gerados via campanhas/links.

**Tabelas Consultadas:**
- `#__crm_campanha_link_clicks` - Log de cliques
- `#__crm_campanha_links` - Links da campanha
- Tabelas customizadas (conforme queries configuradas)

**Consultas Configuráveis:**
- **Qtde por Campanha:** `SELECT campanha_id, COUNT(*) as total FROM #__crm_campanha_link_clicks GROUP BY campanha_id`
- **Qtde por Link:** `SELECT link_id, l.nome, COUNT(*) as total FROM #__crm_campanha_link_clicks c INNER JOIN #__crm_campanha_links l ON c.link_id=l.id GROUP BY link_id ORDER BY total DESC`
- **Registros Gerados:** Queries SQL customizadas armazenadas na configuração do componente
- **Exemplo:** `SELECT COUNT(*) FROM #__users WHERE id IN (SELECT user_id FROM custom_registrations WHERE campaign_id = ?)`

**Telas:**
- **Dashboard de Registros:** Cards com totais por campanha e por link
- **Filtros:** Período, Campanha, Link específico
- **Exportação:** CSV dos registros encontrados

---

## 🔹 Funcional 12 — Importação Interna (de `#__users`)

**Tabelas:**
- Origem: `#__users` (Joomla core)
- Filtro: `#__user_usergroup_map`
- Destino: `#__crm_leads`, `#__crm_lead_group_map`
- Log: `#__crm_import_execucoes`

**Tela de Importação (Task `importar.users`):**
- **Campos:** Grupo Joomla (select de `#__usergroups`), Opções (criar se não existir, sobrescrever), Grupos de Lead (multiselect)

**Processo:**
1. `SELECT u.* FROM #__users u INNER JOIN #__user_usergroup_map m ON u.id=m.user_id WHERE m.group_id = ?`
2. `INSERT INTO #__crm_import_execucoes (tipo, status, started_at) VALUES ('users', 'ok', NOW())`
3. Para cada usuário:
   - `SELECT id FROM #__crm_leads WHERE email_norm = LOWER(?)`
   - Se não existir: `INSERT INTO #__crm_leads (id, razao_social, nome_fantasia, email, origem, status) VALUES (UUID(), ?, ?, ?, 'Manual', 'NOVO')`
   - Mapear: `name` → `razao_social`, `email` → `email`, `username` → `nome_fantasia`
   - `INSERT INTO #__crm_lead_group_map (lead_id, group_id) VALUES (?, ?)`
4. `UPDATE #__crm_import_execucoes SET status = ?, linhas_total = ?, linhas_sucesso = ?, linhas_falha = ?, log = ?, finished_at = NOW() WHERE id = ?`

**Regras**
- Seleção por grupo Joomla via `#__user_usergroup_map`
- Criar lead se não existir `email_norm`
- Deduplicação por e-mail normalizado
- Processar em lote com logs
- Associar a grupos especificados

---

## 🔹 Funcional 13 — Configuração

**Tabela Principal:** `#__crm_integracoes`

**Campos:**
- `id` (INT AUTO_INCREMENT)
- `provider` (VARCHAR 50, UNIQUE) - Nome do provedor
- `params_json` (JSON) - Configurações (API keys, tokens, URLs)
- `ativo` (TINYINT 1) - Flag ativo/inativo
- `created`, `modified`

**Providers Suportados:**
- `google_search`, `google_images`, `google_maps`, `linkedin`, `instagram`, `facebook`, `mailchimp`, `sugarcrm`, `shortlink`, `popimap`, `zenvia`

**Telas**

**Listagem (`integracoes`):**
- **Consulta:** `SELECT * FROM #__crm_integracoes ORDER BY provider`
- **Filtros:** provider, ativo | **Ações:** Ativar/Desativar, Editar, Testar Conexão
- **Campos:** Provider, Status, Data Modificação

**Edição/Novo (`integracao`):**
- **Consulta:** `SELECT * FROM #__crm_integracoes WHERE provider = ?`
- **Gravação:** `INSERT INTO #__crm_integracoes (provider, params_json, ativo) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE params_json = ?, ativo = ?, modified = NOW()`
- **Campos (dinâmicos por provider):**
  - **google_search:** API Key, CX (Custom Search Engine ID)
  - **linkedin:** App ID, App Secret, Access Token
  - **zenvia:** API URL, API Token
  - **mailchimp:** API Key, Server Prefix
  - **popimap:** Host, Port, Username, Password, SSL
  - **sugarcrm:** URL, Username, Password
  - **shortlink:** API Key

**Uso:**
```php
$db->setQuery("SELECT params_json FROM #__crm_integracoes WHERE provider = 'zenvia' AND ativo = 1");
$params = json_decode($db->loadResult());
```

**Configurações de Envio:**
- Armazenadas em params do componente (Joomla)
- Delay entre e-mails (ms), Qtde por lote
- Delay entre SMS (ms), Qtde por lote SMS

**Consultas:**
- Query de exportação: armazenada em `#__crm_export_scripts`
- Query qtde registros: configurável por relatório

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
