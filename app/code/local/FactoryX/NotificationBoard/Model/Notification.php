<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why: 
 */ 
class FactoryX_NotificationBoard_Model_Notification extends Mage_Core_Model_Abstract
{

    protected function _construct() {
        $this->_init('factoryx_notificationboard/notification');
    }
    
    /**
     * replace time with local start time 00:00:00
     */
    public function getStartDateUTC() {
        //Mage::helper('factoryx_notificationboard')->log(sprintf("%s->%s:%s", __METHOD__, get_class($this), print_r($this->getData(), true)));
        $startDate = $this->getData('start_date');
        // check date
        if ($startDate && preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}/", $startDate)) {
            $startDate = Mage::getModel('core/date')->gmtTimestamp(strtotime($startDate));
            //Mage::helper('factoryx_notificationboard')->log(sprintf("%s->startDate: %s", __METHOD__, date("Y-m-d H:i:s", $startDate)));
        }
        else {
            $startDate = 0;
        }
        //Mage::helper('factoryx_notificationboard')->log(sprintf("%s->startDate: %s", __METHOD__, $startDate));
        return $startDate;
    }

    /**
     * replace time with local end time 23:59:59
     */
    public function getEndDateUTC() {
        //Mage::helper('factoryx_notificationboard')->log(sprintf("%s->%s:%s", __METHOD__, get_class($this), print_r($this->getData(), true)));
        $endDate = $this->getData('end_date');
        if ($endDate && preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}/", $endDate)) {
            $endDate = Mage::getModel('core/date')->gmtTimestamp(strtotime($endDate));
            //Mage::helper('factoryx_notificationboard')->log(sprintf("%s->endDate: %s", __METHOD__, date("Y-m-d H:i:s", $endDate)));
        }
        else {
            $endDate = 0;
        }
        //Mage::helper('factoryx_notificationboard')->log(sprintf("%s->endDate: %s", __METHOD__, $endDate));
        return $endDate;
    }

}