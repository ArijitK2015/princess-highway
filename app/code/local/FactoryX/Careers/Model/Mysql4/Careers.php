<?php
 
class FactoryX_Careers_Model_Mysql4_Careers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('careers/careers', 'careers_id');
    }
}