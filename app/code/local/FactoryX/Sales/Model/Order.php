<?php
/**
 * Order model
 *
 * Supported events:
 *  sales_order_load_after
 *  sales_order_save_before
 *  sales_order_save_after
 *  sales_order_delete_before
 *  sales_order_delete_after
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FactoryX_Sales_Model_Order extends Mage_Sales_Model_Order {

    // Add new statuses
    const STATUS_PROCESSING_PARTIALLY_SHIPPED = 'processing_part_shipped';
    const STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING = 'processing_part_shipped_nt';
    const STATUS_PROCESSING_SHIPPED_NO_TRACKING = 'processing_shipped_nt';    
    const STATUS_PROCESSING_STAGE2 = 'processing_stage2';
    
    /**
     * Whether specified state can be set from outside
     * @param $state
     * @return bool
     */
    public function isStateProtected($state) {
        if (empty($state)) {
            return false;
        }
        return self::STATE_CLOSED == $state;
    }
        
    /**
     check order state before saving
     
     if called via soap ignore the rules
     */
    protected function _checkState() {
        // calling via soap will return soap
        $source = Mage::app()->getFrontController()->getRequest()->getControllerName();
        $userNotification = $this->hasCustomerNoteNotify() ? $this->getCustomerNoteNotify() : null;
        
        $allowOverride = false;
        if (preg_match("/soap/i", $source)) {
            $allowOverride = true;
            $userNotification = false;
        }
        //Mage::helper('fx_sales')->log(sprintf("%s->userNotification=%s", __METHOD__, $userNotification));        
        
        if (!$this->getId()) {
            return $this;
        }
        
        if (preg_match(sprintf("/^%s$/", FactoryX_Sales_Model_Order::STATUS_PROCESSING_STAGE2), $this->getStatus())) {
            //Mage::helper('fx_sales')->log(sprintf("skip check %s", __METHOD__));
            return $this;
        }
                     
        /* *************** START CUSTOM CODE CHANGING ORDER STATUS DEPENDING ON THE SHIPMENT AND TRACKING *************** */
        // If we still can ship, can't invoice and the shipment collection is not empty (which means there were already shipments)        
        
        //Mage::helper('fx_sales')->log(sprintf("%s->state=%s,status=%s", __METHOD__, $this->getState(), $this->getStatus()) );        
        if (!$allowOverride && $this->canShip() && !$this->canInvoice() && $this->hasShipments()) {
            // We set the state to processing partially shipped
            $tracked = true;
            foreach($this->getShipmentsCollection() as $shipment) {
                if (!$shipment->getAllTracks()) {
                    $tracked = false;
                }
            }
            if (!$tracked) {
                $state = self::STATE_PROCESSING;
                $status = self::STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING;
                //Mage::helper('fx_sales')->log(sprintf("status=%s", self::STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING));
                $comment = sprintf("automatically set status to '%s->%s'", $state, $status);
                $this->_setState(
                    $state,
                    $status,
                    $comment,
                    $isCustomerNotified = false
                );
            }
            else {
                $state = self::STATE_PROCESSING;
                $status = self::STATUS_PROCESSING_PARTIALLY_SHIPPED;
                $comment = sprintf("automatically set status to '%s->%s'", $state, $status);
                $this->_setState(
                    $state,
                    $status,
                    $comment,
                    $isCustomerNotified = false
                );
            }
        }

        if (!$this->isCanceled() && !$this->canUnhold() && !$this->canInvoice() && !$this->canShip()) {
            $tracked = true;
            foreach($this->getShipmentsCollection() as $shipment) {
                if (!$shipment->getAllTracks()) {
                    $tracked = false;
                }
            }
            if (!$tracked && !$allowOverride) {
                $state = self::STATE_PROCESSING;
                $status = self::STATUS_PROCESSING_SHIPPED_NO_TRACKING;
                $comment = sprintf("automatically set status to '%s->%s'", $state, $status);
                $this->_setState(
                    $state,
                    $status,
                    $comment,
                    $isCustomerNotified = false                    
                );
            }
            /* *************** END CUSTOM CODE CHANGING ORDER STATUS DEPENDING ON THE SHIPMENT AND TRACKING *************** */
            elseif (0 == $this->getBaseGrandTotal() || $this->canCreditmemo()) {
                if ($this->getState() !== self::STATE_COMPLETE) {
                    //$comment = sprintf("set status to '%s->%s'", self::STATE_COMPLETE, self::STATE_COMPLETE);
                    $this->_setState(
                        self::STATE_COMPLETE,
                        true,
                        $comment = null,
                        $userNotification
                    );
                }
            }
            /**
             * Order can be closed just in case when we have refunded amount.
             * In case of "0" grand total order checking ForcedCanCreditmemo flag
             */
            elseif(floatval($this->getTotalRefunded()) || (!$this->getTotalRefunded() && $this->hasForcedCanCreditmemo())) {
                if ($this->getState() !== self::STATE_CLOSED) {
                    //$comment = sprintf("set status to '%s'", self::STATE_CLOSED);
                    $this->_setState(
                        self::STATE_CLOSED,
                        true,
                        $comment = null,
                        $userNotification
                    );
                }
            }
        }
        if ($this->getState() == self::STATE_NEW && $this->getIsInProcess()) {
            //$comment = sprintf("set status to '%s->%s'", $this->getState(), self::STATE_PROCESSING);
            $this->_setState(
                self::STATE_PROCESSING,
                true,
                $comment = null,
                $userNotification
            );
        }
        return $this;
    }
    
    /*
    addStatusHistoryComment
    */
    public function addStatusHistoryComment($comment, $status = false) {
                    
        Mage::dispatchEvent(
            'add_status_history_comment_before',
            array(
                'order' => $this,
                //'state' => $state,
                'status' => $status,
                'comment' => $comment
                //'isCustomerNotified' => $isCustomerNotified,
                //'shouldProtectState' => $shouldProtectState
            )
        );

        //Mage::helper('fx_sales')->log(sprintf("%s->comment=%s[%s]", __METHOD__, $comment, $status));
        //$data = $this->getRequest()->getPost('history');
        //Mage::helper('fx_sales')->log(sprintf("%s->comment:%s[%s]", __METHOD__, $data['comment'], $data['status']));
                
        if ($this->getStatus() == $status && strlen(trim($comment)) == 0) {
            //Mage::helper('fx_sales')->log("no comment");
            $history = Mage::getModel('sales/order_status_history');
            return $history;
        }

        if (false === $status) {
            $status = $this->getStatus();
        }
        elseif (true === $status) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        }
        else {
            $this->setStatus($status);
        }

        $userName = "unknown";
        Mage::getSingleton('core/session', array('name'=>'adminhtml'));
        if(Mage::getSingleton('admin/session')->isLoggedIn()) {
            $userInfo = Mage::getSingleton('admin/session')->getUser();
            if ($userInfo) {
                $userName = $userInfo->getUsername();
            }
        }
        else {
            if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                $userName = Mage::getSingleton('customer/session')->getCustomer()->getName();
            }
            else {
                $userName = "frontend";
            }
        }
        
        //Mage::helper('fx_sales')->log(sprintf("%s->userName=%s", __METHOD__, $userName));    
        $history = Mage::getModel('sales/order_status_history')
            ->setStatus($status)
            ->setComment($comment)
            ->setTrackUser($userName);
            
        $this->addStatusHistory($history);
        
        // dispatch an event after status has changed
        Mage::dispatchEvent(
            'add_status_history_comment_after',
            array(
                'order' => $this,
                //'state' => $state,
                'status' => $status,
                'comment' => $comment
                //'isCustomerNotified' => $isCustomerNotified,
                //'shouldProtectState' => $shouldProtectState
            )
        );
        
        return $history;
    }
    
    
    /**
     * Send email with order update information
     *
     * no dont send email if processingStage2
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return Mage_Sales_Model_Order
     */
    public function sendOrderUpdateEmail($notifyCustomer = true, $comment = '') {
        $storeId = $this->getStore()->getId();

        if (!Mage::helper('sales')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }
        
        $processingStage2 = false;
        if (preg_match("/processing_stage2/", $this->getStatus())) {
            $processingStage2 = true;
        }
        
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer && !$processingStage2) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($this->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is
        // 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'   => $this,
                'comment' => $comment,
                'billing' => $this->getBillingAddress()
            )
        );
        $mailer->send();

        return $this;
    }    

    /* RETURN CREATED BY */

    public function getCreatedBy(){
        return $this->getData('created_by');
    }

    /* RETURN SHIP FROM */
    public function getShipFrom(){
        $shipments = parent::getShipmentsCollection();
        foreach($shipments as $shipment){
            $shipFrom = Mage::getModel('ustorelocator/location')->load($shipment->getShippedFrom());
            return $shipFrom->title;
        }
    }
}
