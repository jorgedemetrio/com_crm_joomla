<?php
defined('_JEXEC') or die;

class CrmModelCampanhaemail extends JModelAdmin
{
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_crm.campanhaemail',
            'campanhaemail',
            ['control' => 'jform', 'load_data' => $loadData]
        );
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState('com_crm.edit.campanhaemail.data', []);
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }
}
