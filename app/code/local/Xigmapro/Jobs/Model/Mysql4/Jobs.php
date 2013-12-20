<?php
 
class Xigmapro_Jobs_Model_Mysql4_Jobs extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {   
        $this->_init('Jobs/Jobs', 'Jobs_id');
    }
}