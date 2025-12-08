<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_crm
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CrmViewLeads extends JViewLegacy
{
    protected $itemid;

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $menu = $app->getMenu()->getActive();
        $this->itemid = $app->input->getInt('Itemid', $menu ? (int) $menu->id : 0);

        $this->setDocument();

        return parent::display($tpl);
    }

    protected function setDocument()
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $menu = $app->getMenu()->getActive();

        $title = JText::_('COM_CRM_LEADS_TITLE');
        $description = '';
        $keywords = '';

        if ($menu) {
            $params = $menu->getParams();
            $title = $params->get('page_title', $title);
            $description = $params->get('menu-meta_description', '');
            $keywords = $params->get('menu-meta_keywords', '');
        }

        $doc->setTitle($title);

        if ($description) {
            $doc->setDescription($description);
        }

        if ($keywords) {
            $doc->setMetadata('keywords', $keywords);
        }

        $pathway = $app->getPathway();
        $path = $pathway->getPathway();
        if (empty($path) || end($path)['name'] !== $title) {
            $pathway->addItem($title);
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $title,
        );

        $doc->addCustomTag('<script type="application/ld+json">' . json_encode($schema) . '</script>');
    }
}
