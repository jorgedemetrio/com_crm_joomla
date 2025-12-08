# 📖 Guia Definitivo de Desenvolvimento de Componentes em Joomla 5

Este guia detalha o processo de criação de componentes para o Joomla 5, focando nas práticas modernas, na estrutura de namespaces e no novo fluxo de trabalho MVC. Ele serve como o padrão ouro para o desenvolvimento dentro do projeto `com_crm_joomla`.

---

## 1. A Mudança de Paradigma: Do Legado ao Moderno

O Joomla 4 e 5 introduziram uma arquitetura de componentes moderna, alinhada com as melhores práticas do PHP contemporâneo. A mudança mais significativa foi a adoção do **carregamento de classes via namespaces (PSR-4)** e uma estrutura de diretórios organizada.

- **Adeus, Nomenclatura Legada:** Não usamos mais nomes de classe como `JControllerLegacy` ou `MyCrmControllerLeads`.
- **Olá, Namespaces:** As classes agora vivem em namespaces, como `Joomla\Component\Crm\Administrator\Controller\LeadsController`.
- **Estrutura Centralizada em `src`:** Quase todo o código PHP do seu componente agora reside em um único diretório `src`, que espelha a estrutura do namespace.

---

## 2. A Nova Estrutura de Pastas (Padrão `com_crm_joomla`)

A estrutura de arquivos de um componente moderno é mais limpa e lógica.

### 2.1. Estrutura Raiz do Componente

```
/com_crm_joomla/
├── administrator/
│   ├── forms/              # Definições de formulário em XML (ex: lead.xml)
│   ├── language/           # Arquivos de idioma (ex: en-GB/en-GB.com_crm_joomla.ini)
│   ├── sql/                # Scripts de instalação e atualização do banco de dados
│   │   ├── install.sql
│   │   └── updates/mysql/
│   │       └── 1.0.1.sql
│   └── src/                # <--- O CORAÇÃO DO COMPONENTE
│       ├── Component/
│       ├── Controller/
│       ├── Model/
│       ├── Table/
│       └── View/
├── media/                  # Arquivos de mídia (CSS, JS, Imagens). Copiados para /media/
├── site/
│   └── src/                # Estrutura similar ao 'administrator/src/' para o frontend
└── com_crm_joomla.xml      # O arquivo de manifesto
```

### 2.2. O Diretório `src`

Este é o diretório mais importante. Sua estrutura interna mapeia diretamente o namespace `Joomla\Component\Crm\Administrator`.

- `src/Component/` -> `Joomla\Component\Crm\Administrator\Component`
- `src/Controller/` -> `Joomla\Component\Crm\Administrator\Controller`
- `src/Model/` -> `Joomla\Component\Crm\Administrator\Model`
- etc.

---

## 3. O Manifesto (`com_crm_joomla.xml`)

O manifesto é a porta de entrada. Para a nova estrutura, ele precisa de duas tags cruciais:

```xml
<?xml version="1.0" encoding="utf-8"?>
<extension type="component" version="4.0" method="upgrade">

    <!-- 1. O Namespace Principal -->
    <namespace>Joomla\Component\Crm</namespace>

    <!-- ... outros metadados ... -->

    <administration>
        <!-- 2. Apontar para as pastas, não para arquivos individuais -->
        <files folder="administrator">
            <folder>forms</folder>
            <folder>language</folder>
            <folder>sql</folder>
            <folder>src</folder>
        </files>
        <!-- ... -->
    </administration>

</extension>
```

---

## 4. O Fluxo MVC no Joomla 5

### 4.1. Ponto de Entrada: `src/Component/Component.php`

Este arquivo substitui o antigo `component.php` na raiz. Ele atua como um despachante (dispatcher) principal.

**Exemplo: `administrator/src/Component/Component.php`**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Component;

use Joomla\CMS\MVC\Component\MVCComponent;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

class Component extends MVCComponent
{
    public function __construct(MVCFactoryInterface $factory)
    {
        parent::__construct($factory);
    }
}
```

### 4.2. Controladores (`src/Controller/`)

Os controladores orquestram as ações. Eles não contêm lógica de negócio; eles delegam para os Models.

**Exemplo: `administrator/src/Controller/ImportArquivoController.php`**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController; // Para um item
use Joomla\CMS\MVC\Controller\BaseController;  // Para uma lista

class ImportArquivoController extends FormController
{
    // O FormController já fornece as tarefas padrão:
    // add, edit, save, apply, cancel, delete, etc.

    // Você pode sobrescrever tarefas se precisar de lógica customizada,
    // como redirecionar para uma view de processamento.
    public function save($key = null, $urlVar = null)
    {
        // Lógica customizada aqui...
        parent::save($key, $urlVar);
    }
}
```

### 4.3. Models (`src/Model/`)

Os Models contêm a lógica de negócio e o acesso aos dados.

- **AdminModel (`FormModel`):** Para um único item (formulário).
- **ListModel:** Para uma lista de itens.

**Exemplo: `administrator/src/Model/ImportArquivoModel.php` (FormModel)**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;

class ImportArquivoModel extends AdminModel
{
    // Carrega o XML do formulário
    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm(
            'com_crm.importarquivo',
            'importarquivo', // Nome do XML em /administrator/forms/
            ['control' => 'jform', 'load_data' => $loadData]
        );
    }

    // Carrega os dados para o formulário
    protected function loadFormData()
    {
        // ... lógica para carregar dados do banco ou da sessão ...
        return $this->getItem();
    }

    // Salva os dados
    public function save($data)
    {
        // ... lógica de validação e de negócio ...
        return parent::save($data);
    }
}
```

### 4.4. Tables (`src/Table/`)

A classe `Table` é um mapeamento direto para uma tabela do banco de dados. Ela lida com as operações de CRUD de baixo nível.

**Exemplo: `administrator/src/Table/LeadTable.php`**
```php
<?php
namespace Joomla\Component\Crm\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class LeadTable extends Table
{
    public function __construct(DatabaseDriver &$db)
    {
        // Mapeia esta classe para a tabela #__crm_leads com a chave 'id'
        parent::__construct('#__crm_leads', 'id', $db);
    }

    // Sobrescreva métodos como check() ou save() se precisar de lógica customizada
    // Ex: Gerar um UUID para novos registros
    public function save($src, $orderingFilter = '', $ignore = '')
    {
        if (empty($this->id)) {
            $this->id = \Joomla\CMS\Uid\Uid::create();
        }
        return parent::save($src, $orderingFilter, $ignore);
    }
}
```

### 4.5. Views e Layouts (`src/View/` e `tmpl/`)

A classe `View` prepara os dados para a exibição. O arquivo `tmpl` renderiza o HTML.

- **`src/View/Leads/HtmlView.php`:** A classe da View.
- **`src/View/Leads/tmpl/default.php`:** O layout da lista.

**Exemplo: `administrator/src/View/Leads/HtmlView.php`**
```php
<?php
namespace Joomla\Component\Crm\Administrator\View\Leads;

use Joomla\CMS\MVC\View\ListView; // Para listas
use Joomla\CMS\MVC\View\AdminView; // Para formulários

class HtmlView extends ListView
{
    protected $items;
    protected $pagination;
    protected $state;

    public function display($tpl = null)
    {
        // Delega a busca de dados para o Model
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state      = $this->get('State');

        // Adiciona a barra de ferramentas (botões "Novo", "Excluir", etc.)
        $this->addToolbar();

        // Renderiza o layout
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        \Joomla\CMS\Toolbar\ToolbarHelper::title('Leads');
        \Joomla\CMS\Toolbar\ToolbarHelper::addNew('lead.add');
        // ... outros botões ...
    }
}
```

**Exemplo: `administrator/src/View/Leads/tmpl/default.php` (Layout)**
```php
<?php
// ... imports ...
?>
<form action="..." method="post" name="adminForm" id="adminForm">
    <table class="table">
        <thead>
            <tr>
                <th><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.checkall'); ?></th>
                <th>Nome</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr>
                    <td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?></td>
                    <td>
                        <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_crm&task=lead.edit&id=' . (int) $item->id); ?>">
                            <?php echo $this->escape($item->nome); ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- ... campos ocultos e token ... -->
</form>
```

---

## 5. Internacionalização

Os arquivos de idioma agora seguem um padrão de nomenclatura mais explícito, incluindo o nome do componente.

- **Localização:** `administrator/language/en-GB/`
- **Nome do Arquivo Principal:** `en-GB.com_crm_joomla.ini`
- **Nome do Arquivo de Sistema:** `en-GB.com_crm_joomla.sys.ini` (para o instalador)

O uso no código permanece o mesmo: `Text::_('COM_CRM_MINHA_STRING');`

---

Este guia cobre a estrutura fundamental. Módulos e Plugins seguem uma lógica similar de `src` e namespaces, que será detalhada em seus próprios guias quando necessário.
