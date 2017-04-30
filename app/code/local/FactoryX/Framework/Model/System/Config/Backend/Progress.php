<?php

class FactoryX_Framework_Model_System_Config_Backend_Progress extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        Mage::getConfig()->saveConfig('framework/options/progress_checkout_enable', ($this->getValue() == 'none')?0:1, $this->getScope(), $this->getScopeId());
        return parent::save();
    }
}