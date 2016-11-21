<?php
 
class FactoryX_Careers_Model_Careers extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('careers/careers');
    }
}