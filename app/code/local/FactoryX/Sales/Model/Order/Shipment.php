<?php
/**
 * Add extra email variables to the original template to handle partial shipment
 */
class FactoryX_Sales_Model_Order_Shipment extends Mage_Sales_Model_Order_Shipment
{
    /**
     * Send email with shipment data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @throws Exception
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function sendEmail($notifyCustomer = true, $comment = '')
    {
        $order = $this->getOrder();
        $storeId = $order->getStore()->getId();

        if (!Mage::helper('sales')->canSendNewShipmentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Start store emulation process
        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {
            // Retrieve specified view block from appropriate design package (depends on emulated store)
            $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                ->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();;
        }
        catch (Exception $exception) {
            // Stop store emulation process
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $exception;
        }

        // Stop store emulation process
        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        // Retrieve corresponding email template id and customer name
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $order->getCustomerName();
        }

        $mailer = Mage::getModel('core/email_template_mailer');
        if ($notifyCustomer) {
            $emailInfo = Mage::getModel('core/email_info');
            $emailInfo->addTo($order->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);

        /* ============= CUSTOM CODE START ============= */
	    // Add extra template params if the shipment is partial
        $orderStatus = $order->getStatus();
	    $partial = ($orderStatus == FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING || $orderStatus == FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED) ? true : false;
        // Added shipped, total and partial
		$emailTemplateVariables = array(
            'shipped'      => round($this->getTotalQty(),0),
            'total'		   => $this->_countOrderItems($order),
            'partial'      => $partial,
    		'order'        => $order,
    		'shipment'     => $this,
    		'comment'      => $comment,
    		'billing'      => $order->getBillingAddress(),
    		'payment_html' => $paymentBlockHtml
        );
        /* ============= CUSTOM CODE END ============= */

        $mailer->setTemplateParams($emailTemplateVariables);
        $mailer->send();

        return $this;
    }

    /**
     * Count the real number of items (configurable vs simple)
     * @param $order
     * @return int
     */
    protected function _countOrderItems($order)
	{
		$totalItems = 0;
		$items = $order->getAllItems();
	
		foreach($items as $itemId => $_item) {
		    if ($_item->getParentItem()) {
		        continue;
		    }
			$totalItems += $_item->getQtyOrdered();
		}
		return $totalItems;
	}
}