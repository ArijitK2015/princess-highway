<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why:  
 */ 
class FactoryX_NotificationBoard_Model_Resource_Notification_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('factoryx_notificationboard/notification');
    }

}