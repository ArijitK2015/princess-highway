<?php
 
class Xigmapro_Jobs_Model_Mysql4_Jobs_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        //parent::__construct();
        $this->_init('Jobs/Jobs');
    }
}