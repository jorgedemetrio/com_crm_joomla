# Guia de Desenvolvimento Joomla 5 para o Componente CRM

> Este documento serve como um guia detalhado para o desenvolvimento e manutenção do `com_crm_joomla`, seguindo as melhores práticas e padrões do Joomla 5.

---

## 1. Estrutura de Arquivos e Pastas

A estrutura de um componente Joomla 5 é organizada para separar claramente as diferentes responsabilidades da aplicação (MVC). A seguir está a estrutura principal do nosso componente no backend (`administrator/`).

```
/administrator
├── controllers/        # Controladores: Orquestram as requisições.
│   ├── lead.php        # Controlador para um único item (Formulário).
│   └── leads.php       # Controlador para a lista de itens.
├── forms/              # Definições de formulário em XML.
│   └── lead.xml
├── language/           # Arquivos de idioma.
│   ├── en-GB/
│   └── pt-BR/
├── models/             # Models: Lógica de negócio e acesso a dados.
│   ├── lead.php        # Model para um único item.
│   └── leads.php       # Model para a lista de itens.
│   └── forms/          # Definições XML para os filtros das listas.
│       └── filter_leads.xml
├── sql/                # Scripts de instalação e atualização do banco de dados.
│   └── install.sql
├── tables/             # Classes Table: Mapeamento direto para as tabelas do BD.
│   └── lead.php
└── views/              # Views: Camada de apresentação.
    ├── lead/           # View para um único item.
    │   ├── tmpl/
    │   │   └── edit.php # Template do formulário.
    │   └── view.html.php # Classe da View.
    └── leads/          # View para a lista de itens.
        ├── tmpl/
        │   └── default.php # Template da lista.
        └── view.html.php   # Classe da View.
```

### Convenção de Nomenclatura Singular vs. Plural

O Joomla utiliza uma convenção clara para diferenciar a visualização de uma lista de itens da visualização de um único item:

*   **Plural (ex: `leads`)**: Usado para a tela que exibe a **lista** de todos os itens. Envolve `LeadsController`, `LeadsModel`, e a pasta `views/leads/`.
*   **Singular (ex: `lead`)**: Usado para a tela que exibe o **formulário** de criação ou edição de um único item. Envolve `LeadController`, `LeadModel`, e a pasta `views/lead/`.

---

## 2. A Camada MVC em Detalhes

### 2.1. A Classe `Table`

A classe `Table` é a camada de mais baixo nível para manipulação de dados. Cada classe `Table` mapeia diretamente para uma tabela no banco de dados e fornece métodos básicos de CRUD (Create, Read, Update, Delete) para um único registro.

**Localização:** `administrator/tables/`

**Exemplo (`leadgroup.php`):**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class LeadgroupTable extends Table
{
    /**
     * Construtor para ligar a classe à tabela do banco de dados.
     *
     * @param   DatabaseDriver  $db  O objeto conector do banco de dados.
     */
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__crm_lead_groups', 'id', $db);
    }
}
```

**Pontos Chave:**
*   **Herança:** A classe deve herdar de `Joomla\CMS\Table\Table`.
*   **Construtor:** O construtor chama `parent::__construct()` passando três argumentos:
    1.  O nome da tabela no banco de dados (com o prefixo `#_`).
    2.  O nome da coluna da chave primária.
    3.  O objeto de conexão com o banco de dados.
*   **Campos:** Os campos da tabela (ex: `id`, `nome`, `state`) são automaticamente mapeados como propriedades públicas da classe.

### 2.2. O Formulário XML

O Joomla utiliza arquivos XML para definir a estrutura e os campos de um formulário. Isso separa a definição dos dados da sua renderização no HTML.

**Localização:** `administrator/forms/`

**Exemplo (`leadgroup.xml`):**
```xml
<?xml version="1.0" encoding="utf-8"?>
<form>
    <fieldset>
        <field
            name="id"
            type="text"
            label="JGLOBAL_FIELD_ID_LABEL"
            readonly="true"
        />
        <field
            name="nome"
            type="text"
            label="COM_CRM_LEADGROUP_FIELD_NOME_LABEL"
            required="true"
        />
        <field
            name="state"
            type="list"
            label="JSTATUS"
            default="1"
            >
            <option value="1">JPUBLISHED</option>
            <option value="0">JUNPUBLISHED</option>
        </field>
    </fieldset>
</form>
```
*Note: O exemplo acima é uma versão simplificada do arquivo real para clareza.*

**Pontos Chave:**
*   **Estrutura:** Um formulário é definido pela tag `<form>`, que contém um ou mais `<fieldset>`. Cada `<fieldset>` agrupa um conjunto de campos (`<field>`).
*   **Atributos do Campo:**
    *   `name`: Corresponde ao nome da coluna na tabela do banco de dados e à propriedade na classe `Table`.
    *   `type`: O tipo de campo (ex: `text`, `list`, `editor`, `sql`, `calendar`). O Joomla oferece dezenas de tipos de campo padrão.
    *   `label`: A chave de idioma para o rótulo do campo.
    *   `required="true"`: Marca o campo como obrigatório.
    *   `readonly="true"`: Torna o campo somente leitura.

### 2.3. As Classes `Model`

O Model é o coração da lógica de negócio. Ele é responsável por buscar dados do banco de dados (geralmente usando a classe `Table`), manipular esses dados e prepará-los para a View. Existem dois tipos principais de Model no backend:

#### `AdminModel` (para um único item)

Este model gerencia um único registro. Suas principais responsabilidades são carregar o formulário XML e os dados do item para edição.

**Localização:** `administrator/models/` (nome no singular, ex: `leadgroup.php`)

**Exemplo (`leadgroup.php`):**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Factory;

class LeadgroupModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_crm.leadgroup', // Contexto
            'leadgroup',         // Nome do XML
            ['control' => 'jform', 'load_data' => $loadData]
        );

        return empty($form) ? false : $form;
    }

    protected function loadFormData()
    {
        $data = Factory::getApplication()->getUserState('com_crm.edit.leadgroup.data', null);
        return empty($data) ? $this->getItem() : $data;
    }
}
```

**Pontos Chave:**
*   `getForm()`: Carrega o arquivo XML correspondente de `administrator/forms/`.
*   `loadFormData()`: Busca os dados para preencher o formulário. Ele primeiro verifica a sessão do usuário (caso um envio anterior tenha falhado na validação) e, se não encontrar, busca o item do banco de dados usando `$this->getItem()`.
*   `save()`: O `AdminModel` base já possui um método `save()` que funciona para formulários simples. Para relacionamentos complexos (como muitos-para-muitos), este método precisa ser sobrescrito, como fizemos no `LeadModel`.

#### `ListModel` (para uma lista de itens)

Este model gerencia uma lista de registros. Sua principal responsabilidade é construir a consulta SQL para buscar os dados da lista, incluindo filtros e ordenação.

**Localização:** `administrator/models/` (nome no plural, ex: `leadgroups.php`)

**Exemplo (`leadgroups.php`):**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Model;

use Joomla\CMS\MVC\Model\ListModel;

class LeadgroupsModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'a.id',
                'nome', 'a.nome',
                'state', 'a.state',
            ];
        }
        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db    = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('a.id, a.nome, a.state')
              ->from($db->quoteName('#__crm_lead_groups', 'a'));

        // Adiciona filtros de busca e ordenação...

        return $query;
    }
}
```

**Pontos Chave:**
*   `__construct()`: Define os `filter_fields`, uma lista de campos que podem ser usados para filtrar e ordenar a lista.
*   `getListQuery()`: O método mais importante. Ele deve retornar um objeto de consulta (`Query`) que o Joomla usará para buscar os dados. É aqui que se implementa a lógica para os filtros de busca, estado, etc.

### 2.4. A Classe `View`

A View é responsável por apresentar os dados ao usuário. No Joomla, a classe da view (`view.html.php`) busca os dados do Model e os atribui a propriedades da classe. O template (`tmpl/default.php` ou `tmpl/edit.php`) então usa essas propriedades para renderizar o HTML.

**Localização:** `administrator/views/<nome_da_view>/`

**Exemplo (`views/leadgroups/view.html.php`):**
```php
<?php
namespace Joomla\Component\Crm\Administrator\View\Leadgroups;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $pagination;
    protected $state;

    public function display($tpl = null)
    {
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        ToolbarHelper::title('Grupos de Lead');
        ToolbarHelper::addNew('leadgroup.add');
        // ... outros botões da barra de ferramentas
    }
}
```

**Pontos Chave:**
*   **Nome da Classe:** A classe da view é sempre `HtmlView`. O Joomla a localiza com base no namespace.
*   `display()`: O método principal. Ele chama métodos `get()` (que são passados para o Model correspondente) para buscar os dados (`Items`, `Pagination`, etc.) e os armazena em propriedades da classe.
*   `addToolbar()`: Um método auxiliar para configurar os botões da barra de ferramentas (Novo, Editar, Excluir, etc.).

### 2.5. A Classe `Controller`

O Controller atua como o ponto de entrada para as requisições do usuário. Ele recebe a tarefa (task) da URL, executa ações (como salvar ou deletar dados, geralmente chamando métodos do Model) e redireciona o usuário.

**Localização:** `administrator/controllers/`

**Exemplo (`controllers/leadgroup.php` - Singular):**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;

class LeadgroupController extends FormController
{
    // Normalmente, herdar de FormController já fornece
    // as tarefas padrão (save, apply, cancel, edit).
}
```

**Exemplo (`controllers/leadgroups.php` - Plural):**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Controller;

use Joomla\CMS\MVC\Controller\AdminController;

class LeadgroupsController extends AdminController
{
    // AdminController fornece tarefas em lote (publish, unpublish, delete).
    // O método getModel é frequentemente sobrescrito para apontar
    // para o model no singular ao executar uma tarefa em um item.
    public function getModel($name = 'Leadgroup', $prefix = 'Administrator', $config = [])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
```

**Pontos Chave:**
*   **Herança:** Controllers de formulário herdam de `FormController`, e controllers de lista herdam de `AdminController`. Essas classes base já fornecem a maior parte da lógica necessária.
*   **Tarefas (Tasks):** As tarefas são métodos públicos no controller. Uma URL como `index.php?option=com_crm&task=leadgroup.edit&id=1` irá chamar o método `edit()` da classe `LeadgroupController`.

---

## 3. Desenvolvimento de Módulos e Plugins

Embora o foco principal seja o componente, módulos e plugins são essenciais para estender a funcionalidade do Joomla.

### 3.1. Módulos do Administrador

Um módulo de administrador pode ser usado para exibir informações resumidas no painel de controle.

**Exemplo Conceitual: Módulo `mod_crm_latest_leads`**

*   **Objetivo:** Mostrar os 5 leads mais recentes no painel.
*   **`mod_crm_latest_leads.php` (Ponto de Entrada):** Arquivo principal que busca os dados.
*   **`helper.php`:** Conteria uma classe `ModCrmLatestLeadsHelper` com um método `getLeads()`. Este método faria uma consulta SQL na tabela `#__crm_leads` para buscar os últimos registros.
*   **`tmpl/default.php`:** O template que recebe os dados do helper e os renderiza como uma lista HTML.
*   **`mod_crm_latest_leads.xml`:** O manifesto do módulo, que o registra no Joomla.

### 3.2. Plugins

Plugins são usados para responder a eventos que ocorrem no Joomla (ex: `onUserAfterSave`, `onContentPrepare`).

**Exemplo Conceitual: Plugin `plg_user_crm`**

*   **Objetivo:** Criar um novo Lead em nosso CRM sempre que um novo usuário se registra no Joomla.
*   **`plg_user_crm.php`:** O arquivo principal do plugin.
*   **Classe `PlgUserCrm`:** Herdaria de `Joomla\CMS\Plugin\CMSPlugin`.
*   **Método `onUserAfterSave($user, $isnew, $success, $msg)`:** Este método seria acionado após um usuário ser salvo. O código dentro dele verificaria se `$isnew` é `true`. Se for, ele pegaria os dados do objeto `$user` (nome, e-mail) e os inseriria na tabela `#__crm_leads`, possivelmente usando a `LeadTable` que já criamos para garantir consistência.
*   **`plg_user_crm.xml`:** O manifesto do plugin, que o registra e especifica que ele pertence ao grupo `user`.
