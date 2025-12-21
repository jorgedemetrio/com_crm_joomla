# Instruções para Jules - CRM Joomla

## Visão Geral do Projeto

### Identificação
- **Projeto**: com_crm_joomla
- **Tipo**: Componente Joomla 3.x para CRM (Customer Relationship Management)
- **Localização**: `com_crm/`
- **Idioma de Comunicação**: Português Brasil

### Stack Tecnológica
- **Framework**: Joomla 3.x (estrutura legada sem namespaces)
- **Backend**: PHP 8.3
- **Banco de Dados**: MySQL 8
- **Frontend**: Bootstrap 5, HTML5, jQuery
- **Ícones**: Bootstrap Icons

## Documentação Essencial

**SEMPRE consulte antes de fazer alterações:**

1. `docs/DEVELOPMENT_GUIDELINES.md` - Padrões técnicos e arquitetura
2. `docs/funcional/FUNCIONAL.md` - Especificações funcionais detalhadas
3. `docs/MER.md` - Modelo de dados e relacionamentos

## Princípios de Desenvolvimento

### Abordagem ao Código
- Atue como desenvolvedor **sênior especializado em Joomla 3**
- **NUNCA refaça arquivos completos** - apenas correções pontuais
- Trabalhe focado em corrigir erros específicos, evitando grandes refatorações
- Ao identificar um erro, **verifique arquivos similares** do mesmo tipo para aplicar a correção em padrão:
  - Controllers → outros controllers
  - Models → outros models
  - Views → outras views
  - Templates → outros templates
  - SQL/Tables → outros arquivos de banco
  - JavaScript → outros arquivos JS
  - CSS/LESS → outros arquivos de estilo

### Estrutura Joomla 3 (Legada)
- **NÃO use namespaces** (feature do Joomla 5+)
- Use `jimport()` para importar classes PHP
- **NUNCA use** declarações `use` (são de Joomla 5)
- Estrutura: controllers/, models/, views/, tables/ em pastas separadas

## Padrões de Banco de Dados

### Chaves Primárias
- **Padrão**: UUID (CHAR 36)
- AUTO_INCREMENT apenas em tabelas auxiliares/log

### Validação de Dados
- **Lead Válido** = possui `email` válido **OU** `telefone` válido
- Campo `site` é **OPCIONAL**
- Toda exclusão é **lógica** (soft delete) via `state = -2`

### Campos de Auditoria (OBRIGATÓRIOS em todas as tabelas)
```sql
-- Status e Ordenação
state TINYINT(3) NOT NULL DEFAULT 1,
ordering INT(11) NOT NULL DEFAULT 0,
checked_out INT(11) UNSIGNED NOT NULL DEFAULT 0,
checked_out_time DATETIME NULL,

-- Auditoria Básica
created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
created_by INT(11) NOT NULL DEFAULT 0,
modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
modified_by INT(11) NULL,

-- Rastreamento de Criação
criacao_session_id VARCHAR(200) NULL,
criacao_tracking_id VARCHAR(36) NULL,
criacao_ip VARCHAR(45) NULL,
criacao_ip_proxy VARCHAR(45) NULL,

-- Rastreamento de Alteração
alteracao_session_id VARCHAR(200) NULL,
alteracao_tracking_id VARCHAR(36) NULL,
alteracao_ip VARCHAR(45) NULL,
alteracao_ip_proxy VARCHAR(45) NULL
```

### Nomenclatura de Tabelas
- Prefixo: `#__crm_*`
- Exemplos: `#__crm_leads`, `#__crm_campanhas`, `#__crm_email_envios`

## Interface do Usuário

### Padrões de Tela

#### Listagens
- **Paginação**: Obrigatória em todas as listas
- **Ordenação**: Permitir clique nos títulos das colunas
- **Filtros**: Nome, status, datas
- **Ações em massa**: Publicar, despublicar, excluir (lógico)

#### Formulários
- **Validação**: Client-side e server-side
- **CSRF**: Sempre usar `JToken` em forms
- **Tracking**: UUID em forms de gravação/alteração

#### Elementos Visuais
- **Ícones**: Usar Bootstrap Icons
- **Modal de Ajuda**: Botão no topo de TODAS as telas explicando a funcionalidade
- **Modal de Suporte**: Em telas com formulários, botão para abrir chamado ao admin

### Internacionalização (i18n)

**CRÍTICO**: Sistema multiidioma obrigatório

#### Idiomas Suportados (8)
1. Português Brasil (pt-BR)
2. Inglês (en-GB)
3. Espanhol (es-ES)
4. Italiano (it-IT)
5. Francês (fr-FR)
6. Alemão (de-DE)
7. Chinês (zh-CN)
8. Japonês (ja-JP)

#### Regras
- **NUNCA remova** traduções existentes
- Ao adicionar nova funcionalidade: tradução em **TODOS os 8 idiomas**
- Use sempre: `JText::_('COM_CRM_KEY')`

## Camada Site (Frontend)

### Segurança e Validação
- **Todos** os links e forms devem passar `Itemid`
- **Todos** os forms devem enviar `JToken` (CSRF)
- Links/botões de remoção devem enviar `JToken`
- Use `JLog` do Joomla para logging

### Mensagens ao Usuário
```php
// Erro
JFactory::getApplication()->enqueueMessage($mensagem, 'error');

// Aviso
JFactory::getApplication()->enqueueMessage($mensagem, 'warning');

// Notificação
JFactory::getApplication()->enqueueMessage($mensagem, 'notice');

// Mensagem padrão
JFactory::getApplication()->enqueueMessage($mensagem, 'message');
```

### Roteamento
- Use `router.php` para mapear rotas
- Use `JRoute::_()` para criar links
- Use `JHtml::_()` para importar recursos visuais

### Metadados SEO (em view.html.php)
Cada view deve definir:
- Title da página
- Breadcrumbs
- Meta descrição
- Keywords
- JSON-LD (Google Structured Data)

### Tracking de Formulários
Formulários que gravam/alteram dados devem registrar:
- ID do usuário logado
- Data e hora
- IP do cliente
- IP do proxy
- UUID de tracking
- Session ID

### Menus no Frontend
Todo item que deve ter link no site precisa de:
- **XML manifesto** na pasta `tmpl/`
- Nome do XML = nome do arquivo PHP da view

## Camada Administrator (Backend)

### Estrutura de Menus
- **Menus principais**: Definidos no `com_crm.xml` (manifesto)
- **Submenus**: Usar `JToolbarHelper`:
  - `preferences` - Configurações
  - `addNew` - Novo registro
  - `editList` - Editar
  - `deleteList` - Excluir
  - `publish` - Publicar
  - `unpublish` - Despublicar
  - `divider` - Separador

### Título de Views
```php
// Em /administrator/views/<view>/view.html.php
JToolbarHelper::title(JText::_('COM_CRM_TITLE'), 'icon');
```

### Controle de Acesso (ACL)
- Configurar e implementar corretamente no `access.xml`
- Verificar permissões por tarefa
- Aplicar em todos os controllers

## Regras Gerais de Modificação

### Arquivo install.sql
- **Evite modificar** sempre que possível
- Se necessário: apenas correções pontuais
- **Priorize adições** (não remova informações existentes)
- Sempre agregativo, nunca destrutivo

### Segurança de Diretórios
- **Todas** as pastas devem ter `index.html` vazio
- Previne listagem de diretórios

### Importação de Recursos PHP
```php
// ✅ CORRETO (Joomla 3)
jimport('joomla.application.component.model');

// ❌ ERRADO (Joomla 5)
use Joomla\CMS\MVC\Model\BaseModel;
```

## Estrutura de Arquivos Principais

### Backend (administrator/)
```
administrator/
├── access.xml              # ACL
├── controller.php          # Controller principal
├── crm.php                # Entry point
├── controllers/           # Controllers específicos
├── models/                # Models de negócio
├── views/                 # Views e templates
├── tables/                # Classes de tabela
├── forms/                 # Definições XML de formulários
├── helpers/               # Classes auxiliares
├── language/              # Traduções (8 idiomas)
└── sql/
    └── install.sql        # Schema do banco
```

### Frontend (site/)
```
site/
├── controller.php         # Controller principal
├── crm.php               # Entry point
├── router.php            # Roteamento SEF
├── controllers/          # Controllers específicos
├── views/                # Views públicas
└── language/             # Traduções frontend
```

## Padrões de Código

### Convenções de Nome
- **Classes**: PascalCase - `CrmModelLead`
- **Métodos**: camelCase - `getLead()`
- **Variáveis**: snake_case ou camelCase - `$lead_id` ou `$leadId`
- **Constantes**: UPPER_SNAKE_CASE - `COM_CRM_VERSION`

### Comentários
```php
/**
 * Descrição do método
 * 
 * @param   string  $param1  Descrição do parâmetro
 * @return  mixed   Descrição do retorno
 * @since   1.0.0
 */
public function metodoExemplo($param1)
{
    // Implementação
}
```

## Fluxo de Trabalho

### Ao Receber uma Tarefa

1. **Leia a documentação** relevante em `docs/`
2. **Analise o código existente** do mesmo tipo
3. **Identifique o padrão** usado no projeto
4. **Aplique a correção pontual** sem refatorar
5. **Verifique arquivos similares** para aplicar a mesma correção
6. **Teste** a alteração (se possível)
7. **Valide traduções** em todos os idiomas (se aplicável)

### Prioridades

1. ✅ Correções pontuais
2. ✅ Manter consistência com código existente
3. ✅ Seguir padrões Joomla 3
4. ✅ Garantir traduções completas
5. ❌ Evitar grandes refatorações
6. ❌ Não alterar estrutura sem necessidade
7. ❌ Nunca remover traduções

## Checklist de Qualidade

Antes de finalizar qualquer alteração, verifique:

- [ ] Código segue padrões Joomla 3 (não usa namespaces)
- [ ] Usa `jimport()` ao invés de `use`
- [ ] Campos de auditoria presentes (se tabela nova)
- [ ] Soft delete implementado (`state = -2`)
- [ ] CSRF token presente em forms
- [ ] Traduções em todos os 8 idiomas
- [ ] Modal de ajuda implementado (se tela nova)
- [ ] Bootstrap Icons usado
- [ ] Paginação e ordenação em listas
- [ ] `index.html` vazio em pastas novas
- [ ] Validações client-side e server-side
- [ ] Tracking UUID em forms de gravação
- [ ] ACL configurado corretamente

## Recursos Úteis

### Documentos do Projeto
- `/docs/DEVELOPMENT_GUIDELINES.md` - Guidelines técnicas
- `/docs/funcional/FUNCIONAL.md` - Especificações funcionais
- `/docs/MER.md` - Modelo de dados

### Estrutura de Tabelas Principais
- `#__crm_leads` - Leads/contatos
- `#__crm_lead_groups` - Grupos de leads
- `#__crm_campanhas` - Campanhas de marketing
- `#__crm_campanha_emails` - Templates de email
- `#__crm_campanha_sms` - Templates SMS
- `#__crm_campanha_links` - Links rastreáveis
- `#__crm_email_envios` - Log de envios
- `#__crm_email_opens` - Log de aberturas
- `#__crm_email_optout` - Descadastros

## Exemplo de Workflow Típico

### Cenário: Corrigir bug em controller

```
1. Identificou bug em controllers/lead.php
2. Leia docs/funcional/FUNCIONAL.md sobre leads
3. Analise outros controllers (campanhas.php, agendamentos.php)
4. Identifique o padrão correto
5. Aplique correção pontual em lead.php
6. Verifique se outros controllers têm o mesmo problema
7. Aplique a mesma correção onde necessário
8. Teste a funcionalidade
```

### Cenário: Adicionar novo campo

```
1. Adicione campo no SQL (install.sql) de forma agregativa
2. Atualize form XML em forms/
3. Atualize model para salvar/validar
4. Atualize view para exibir
5. Adicione traduções em TODOS os 8 idiomas
6. Verifique se tabelas similares precisam do mesmo campo
7. Teste validações
```

## Comandos Úteis do Joomla 3

### JFactory
```php
$app = JFactory::getApplication();
$db = JFactory::getDbo();
$user = JFactory::getUser();
$session = JFactory::getSession();
$config = JFactory::getConfig();
```

### JDatabase
```php
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('*')
      ->from('#__crm_leads')
      ->where('state >= 0');
$db->setQuery($query);
$results = $db->loadObjectList();
```

### JInput
```php
$input = JFactory::getApplication()->input;
$id = $input->get('id', 0, 'INT');
$name = $input->get('name', '', 'STRING');
$data = $input->post->get('jform', array(), 'array');
```

## Observações Finais

- **Sempre priorize estabilidade** sobre novas features
- **Mantenha compatibilidade** com Joomla 3.x
- **Documente** alterações significativas
- **Teste** antes de finalizar
- **Comunique** em Português Brasil
- **Seja consistente** com o código existente

---

**Última Atualização**: Dezembro 2024
**Versão do Documento**: 1.0
**Mantido por**: Equipe de Desenvolvimento CRM
