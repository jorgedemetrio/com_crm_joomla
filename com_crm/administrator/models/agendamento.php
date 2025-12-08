<?php
defined('_JEXEC') or die;

class CrmModelAgendamento extends JModelAdmin
{
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_crm.agendamento',
            'agendamento',
            ['control' => 'jform', 'load_data' => $loadData]
        );
        if (empty($form)) {
            return false;
        }
        return $form;
    }

    protected function loadFormData()
    {
        $data = JFactory::getApplication()->getUserState('com_crm.edit.agendamento.data', []);
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }
}
