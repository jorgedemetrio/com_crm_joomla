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
    protected $tracking;

    public function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $menu = $app->getMenu()->getActive();
        $this->itemid = $app->input->getInt('Itemid', $menu ? (int) $menu->id : 0);
        $this->tracking = $this->generateTrackingId();

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

        $lastItem = !empty($path) ? end($path) : null;
        $lastName = '';

        if (is_array($lastItem) && isset($lastItem['name'])) {
            $lastName = $lastItem['name'];
        } elseif (is_object($lastItem) && isset($lastItem->name)) {
            $lastName = $lastItem->name;
        }

        if (empty($path) || $lastName !== $title) {
            $pathway->addItem($title);
        }

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $title,
        );

        $doc->addCustomTag('<script type="application/ld+json">' . json_encode($schema) . '</script>');
    }

    /**
     * Retorna um UUID para rastreamento do formulário.
     *
     * @return string
     */
    protected function generateTrackingId()
    {
        $session = JFactory::getSession();
        $tracking = $session->get('com_crm.tracking');

        if (!$tracking) {
            $tracking = $this->createUuid();
            $session->set('com_crm.tracking', $tracking);
        }

        return $tracking;
    }

    /**
     * Gera um UUID v4 simples para atender o requisito de tracking.
     *
     * @return string
     */
    private function createUuid()
    {
        $data = random_bytes(16);

        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
