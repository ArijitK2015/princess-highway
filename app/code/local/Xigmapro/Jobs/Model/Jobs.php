<?php
 
class Xigmapro_Jobs_Model_Jobs extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('Jobs/Jobs');
    }
}