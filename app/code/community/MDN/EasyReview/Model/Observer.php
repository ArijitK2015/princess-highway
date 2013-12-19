<?php

class MDN_EasyReview_Model_Observer {


    /**
     * Send notifications every nights at 2 o'clock
     */
    public function sendNotification() {
        //if auto update enabled
        if (mage::getStoreConfig('easyreview/general/enable') == 1)
            mage::helper('EasyReview/Notifier')->run();
    }

}

