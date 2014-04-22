<?php
class FactoryX_Lookbook_Model_Mysql4_Lookbook_Media extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        $this->_init('lookbook/lookbook_media', 'media_id');
    }

}
