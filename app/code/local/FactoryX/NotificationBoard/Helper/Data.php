<?php
/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why:  
 */ 
class FactoryX_NotificationBoard_Helper_Data extends Mage_Core_Helper_Abstract {
    
    // log file name
    private static $logFileName = 'factoryx_notificationboard.log';
    // stylesheet path
    private static $stylesheetPath = 'css/factoryx/notification/styles.css';
    
    /**
    only get style sheet if there are notifcations
    
    @return string path to stylesheet
    */
    public function getStylesheet() {
        $stylesheet = null;
        $notifications = Mage::getModel('factoryx_notificationboard/notification')->getCollection()->addFieldToFilter('status', 1);
        //Mage::helper('factoryx_notificationboard')->log(sprintf("%s", __METHOD__));
        if ($notifications && count($notifications) > 0) {
            //Mage::helper('factoryx_notificationboard')->log(sprintf("%s->notifications: %d", __METHOD__, count($notifications)));
            $stylesheet = self::$stylesheetPath;
        }
        return $stylesheet;
    }
    
    /**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data)  {
		Mage::log($data, null, self::$logFileName);
	}
    
}