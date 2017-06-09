<?php

/**
 * Class FactoryX_CustomReports_Block_Customreport
 */
class FactoryX_CustomReports_Block_Customreport extends Mage_Adminhtml_Block_Template
{
    protected $_sideNote = null;

    /**
     * @param $nb
     */
    public function setSideNote($nb)
    {
        $this->_sideNote = $nb;
    }

    /**
     * @return null
     */
    public function getSideNote()
    {
        return $this->_sideNote;
    }
}