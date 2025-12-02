<?php
defined('_JEXEC') or die;

class CrmModelCampanhasms extends JModelAdmin
{
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_crm.campanhasms',
            'campanhasms',
            ['control' => 'jform', 'load_data' => $loadData]
        );
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState('com_crm.edit.campanhasms.data', []);
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }
}
