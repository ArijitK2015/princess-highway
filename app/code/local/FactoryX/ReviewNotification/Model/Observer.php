<?php

class FactoryX_ReviewNotification_Model_Observer {


    /**
     * Send notifications every nights at 2 o'clock
     */
    public function sendNotification(Mage_Cron_Model_Schedule $schedule = null) {
        //if auto update enabled
        if (mage::getStoreConfig('reviewnotification/general/enable') == 1)
            mage::helper('reviewnotification/notifier')->run();
    }

}

