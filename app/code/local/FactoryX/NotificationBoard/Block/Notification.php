<?php
/**
 * Class FactoryX_NotificationBoard_Block_Notification
 */
class FactoryX_NotificationBoard_Block_Notification extends Mage_Core_Block_Template {
    
    private $notifications;

    public function _construct() {
        
        // Getting the one notification
        $this->notifications = Mage::getModel('factoryx_notificationboard/notification')->getCollection()->addFieldToFilter('status', 1);
        $this->setNotifications($this->notifications);
        //Mage::helper('factoryx_notificationboard')->log(sprintf("set %d notifications", count($this->notifications)));

        $this->addData(array(
            'cache_lifetime' => 3600,
            'cache_tags'     => array(
                "notificationboard_notification_cache_tags"
            ),
            'cache_key'      => "notificationboard_notification_cache_key",
        ));

        parent::_construct();
    }

    /**
     * @return mixed
     */
    protected function _prepareLayout()
    {
        /*
        // replaced by layout handle (see notification.xml)
        $notifications = $this->getNotifications();
        if ($notifications && count($notifications) > 0) {            
            $this->getLayout()->getBlock('head')->addItem('skin_css','css/factoryx/notification/styles.css');
            $this->getLayout()->getBlock('head')->addItem('skin_js','js/factoryx/notification.js');
        }
        */
        return parent::_prepareLayout();
    }
}