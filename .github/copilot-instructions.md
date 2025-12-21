**Projeto CRM Joomla**
# Pré-requisitos e Padrões de Desenvolvimento – Joomla 3

## Contexto do Projeto
- **Nome**: com_crm_joomla
- **Componente Principal**: `com_crm` localizado em `com_crm/`
- **Versão Joomla**: Joomla 3.x (estrutura legada, sem namespaces)
- **Stack Tecnológica**: PHP 8.3, MySQL 8, Bootstrap 5, HTML5, jQuery
- **Idioma**: Fale comigo em **Português Brasil**

## Documentação Obrigatória
- **SEMPRE** leia a documentação em `docs/` antes de fazer alterações
- Arquivos importantes:
  - `docs/DEVELOPMENT_GUIDELINES.md` - Padrões técnicos e estrutura
  - `docs/funcional/FUNCIONAL.md` - Especificações funcionais completas
  - `docs/MER.md` - Modelo de dados e relacionamentos
## Princípios de Trabalho
- Você é um **desenvolvedor sênior Joomla 3** especializado
- Trabalhe **pontualmente em erros**, evitando refazer telas e grandes trabalhos
- **NÃO refaça arquivos** - sempre altere ou corrija pontualmente
- Quando detectar um erro, **analise arquivos similares** para corrigir o mesmo padrão:
  - **Controllers**: Verifique outros controllers
  - **Templates**: Verifique outros templates
  - **Views**: Verifique outras views
  - **Models**: Verifique outros models
  - **Tables**: Verifique outros arquivos SQL
  - **JavaScript**: Verifique outros arquivos JS
  - **CSS/LESS**: Verifique outros arquivos CSS/LESS

## Estrutura de Dados e Validações
- **Regra de Lead Válido**: Um lead é válido se possuir `email` válido **OU** `telefone` válido
- **Campo `site` é OPCIONAL**
- **Chave Primária**: Usar **UUID (CHAR 36)** como PK padrão
- **Remoção de Dados**: Toda remoção é **lógica (soft delete)** via campo `state = -2`
- **Campos de Auditoria Obrigatórios**:
  ```sql
  state TINYINT(3) NOT NULL DEFAULT 1,
  ordering INT(11) NOT NULL DEFAULT 0,
  checked_out INT(11) UNSIGNED NOT NULL DEFAULT 0,
  checked_out_time DATETIME NULL,
  created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_by INT(11) NOT NULL DEFAULT 0,
  modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  modified_by INT(11) NULL,
  criacao_session_id VARCHAR(200) NULL,
  criacao_tracking_id VARCHAR(36) NULL,
  criacao_ip VARCHAR(45) NULL,
  criacao_ip_proxy VARCHAR(45) NULL,
  alteracao_session_id VARCHAR(200) NULL,
  alteracao_tracking_id VARCHAR(36) NULL,
  alteracao_ip VARCHAR(45) NULL,
  alteracao_ip_proxy VARCHAR(45) NULL
  ```

## Interface e Usabilidade
- **Listas**: Todas devem ter paginação e permitir reordenação clicando nos títulos das colunas
- **Botões**: Usar **Bootstrap Icons**
- **Modal de Ajuda**: Todas as telas devem ter botão no topo que abre modal explicativo
- **Modal de Suporte**: Telas com formulários devem ter botão para abrir chamado ao admin
## Internacionalização (i18n)
- O sistema **DEVE** estar disponível em **8 idiomas**:
  - Português Brasil (pt-BR)
  - Inglês (en-GB)
  - Espanhol (es-ES)
  - Italiano (it-IT)
  - Francês (fr-FR)
  - Alemão (de-DE)
  - Chinês (zh-CN)
  - Japonês (ja-JP)
- **NUNCA** remova traduções existentes
- Ao adicionar funcionalidade ou tela nova, garanta tradução em **TODOS os idiomas**
- Use sempre `JText::_('COM_CRM_KEY')` para exibir textos traduzidos

## Regras da Camada Site (Frontend)

- Todos os `links` e `forms` devem passar `Itemid`.
- Todos os `forms` devem enviar o `JToken` (CSRF).
- Sempre que necessário use o `JLog` do Joomla.
- Todos os `links` ou `botões` que geram remoção devem enviar o `JToken` (CSRF).
- Para disponibilizar mensagens no front, utilize:
  - Erro: `JFactory::getApplication()->enqueueMessage(<MENSAGEM>, 'error');`
  - Aviso: `JFactory::getApplication()->enqueueMessage(<MENSAGEM>, 'warning');`
  - Notificação: `JFactory::getApplication()->enqueueMessage(<MENSAGEM>, 'notice');`
  - Mensagem padrão: `JFactory::getApplication()->enqueueMessage(<MENSAGEM>, 'message');`
- Use `router.php` para mapear as rotas e `JRoute::_` para criar os links.
- Para importar recursos visuais, use `JHtml::_`.
- Para exibir textos traduzidos, use `JText::_`.
- O arquivo `view.html.php` deve ser usado para definir:
  - Title da página  
  - Breadcrumbs  
  - Meta descrição  
  - Keywords  
  - JSON-LD (Google Data Structure)
- Todos os botões devem utilizar **Bootstrap Icons**.
- Todas as telas devem ter um **botão no topo** que abre um **Modal de Ajuda** explicando sobre a tela.
- Todas as telas com formulários devem ter um botão ao lado do botão de ajuda para abrir um **modal com formulário para abrir chamado** destinado ao administrador do sistema.
- Todos os forms das views que têm como objetivo **gravar ou alterar dados** no banco devem enviar um **UUID** chamado `tracking` (corrigido de `trackind`).
  - ID do usuário que solicitou  
  - Data de atualização  
  - IP  
  - IP Proxy  
  - Tracking  
  - Session ID
- Sempre que um novo registro for gravado ou um existente alterado, salvar:
  - ID do usuário logado  
  - Data  
  - IP  
  - IP Proxy  
  - Tracking  
  - Session ID
- Todo item que necessite ter link na área site deve ter um **XML manifesto** na mesma pasta do `tmpl`, com o mesmo nome do arquivo PHP que virará menu no frontend.


## Regras da Camada Administrator

- Use o **manifesto XML** de instalação (ex: `com_crm.xml`) do componente para os menus principais.
- Use **JToolbarHelper** para criar submenus. Exemplos de métodos:
  - `preferences`
  - `addNew`
  - `editList`
  - `deleteList`
  - `publish`
  - `unpublish`
  - `divider`
- No arquivo `/administrator/views/<nome da view>/view.html.php`, use `JToolbarHelper::title` para atribuir um título.
- Configure e implemente corretamente o **ACL** no arquivo `access.xml`.

## Diretrizes Gerais

- Evite mexer em `install.sql`; caso seja necessário, corrija apenas pontualmente e prioritariamente de forma agregativa apenas para adicionar itens não remova informação.
- Não refaça arquivos **sempre altere ou corrija pontualmente**.
- Não remova traduções. Se adicionar funcionalidade ou tela nova, garanta tradução em **todos os idiomas**.
- Por segurança, todas as pastas devem ter um arquivo `index.html` com HTML em branco para evitar listagem de diretórios.
- Na versão Joomla 3, para importar recursos PHP, use `jimport` o `use` se trata de outra versão do Joomla não use ele.