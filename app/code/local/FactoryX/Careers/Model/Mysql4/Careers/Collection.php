<?php
 
class FactoryX_Careers_Model_Mysql4_Careers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('careers/careers');
    }

}