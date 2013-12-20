<?php

class FactoryX_Sales_Model_Order_Shipment extends Mage_Sales_Model_Order_Shipment
{

	/**
     * After object save manipulations
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    protected function _afterSave()
    {
        if (null !== $this->_items) {
            foreach ($this->_items as $item) {
                $item->save();
            }
        }

        if (null !== $this->_tracks) {
            foreach($this->_tracks as $track) {
                $track->save();
            }
        }

        if (null !== $this->_comments) {
            foreach($this->_comments as $comment) {
                $comment->save();
            }
        }
		
		/* *************** START CUSTOM CODE CHANGING ORDER STATUS DEPENDING ON THE SHIPMENT AND TRACKING *************** */
		$tracked = true;
		foreach($this->getOrder()->getShipmentsCollection() as $shipment) 
		{
			if (!$shipment->getAllTracks()) 
			{
				$tracked = false;
			}
		}
		
		if ($tracked && ($this->getOrder()->getStatus() ==  FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING))
		{
			$this->getOrder()->setState(FactoryX_Sales_Model_Order::STATE_PROCESSING, FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED, '');
			$this->getOrder()->save();
		}
		elseif ($tracked && ($this->getOrder()->getStatus() ==  FactoryX_Sales_Model_Order::STATUS_PROCESSING_SHIPPED_NO_TRACKING)) { 
			if (!$this->getOrder()->isCanceled()
				&& !$this->getOrder()->canUnhold()
				&& !$this->getOrder()->canInvoice()
				&& !$this->getOrder()->canShip()
				&& (0 == $this->getOrder()->getBaseGrandTotal() || $this->getOrder()->canCreditmemo())) {
					if ($this->getOrder()->getState() !== 'complete') {
						$this->getOrder()->setState('complete', true, '');
						$this->getOrder()->save();
					}
				}
		}
		/* *************** END CUSTOM CODE CHANGING ORDER STATUS DEPENDING ON THE SHIPMENT AND TRACKING *************** */
        return parent::_afterSave();
    }
	
	/**
     * Send email with shipment data
     *
     * @param boolean $notifyCustomer
     * @param string $comment
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
            // Mage_Payment_Model_Info
            // Mage_Sales_Model_Order_Payment
            $payment = $order->getPayment();
            $paymentBlock = Mage::helper('payment')->getInfoBlock($payment)->setIsSecureMode(true);
            $paymentBlock->getMethod()->setStore($storeId);
            $paymentBlockHtml = $paymentBlock->toHtml();
            //Mage::helper('fx_sales')->log(sprintf("%s->paymentBlockHtml=%s", __METHOD__, $paymentBlockHtml));
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
       
	    // Add extra template params if the shipment is partial
	    $partial = false;
		if ($order->getStatus() == FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING ||
		    $order->getStatus() == FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED) 
		{
            $partial = true;
		}
		$emailTemplateVariables = array(
            'shipped'	   => $this->getTotalQty(),
            'total'		   => $this->countOrderItems($order),
            'partial'      => $partial,
    		'order'        => $order,
    		'shipment'     => $this,
    		'comment'      => $comment,
    		'billing'      => $order->getBillingAddress(),
    		'payment_html' => $paymentBlockHtml
        );

        $mailer->setTemplateParams($emailTemplateVariables);
		
        $mailer->send();

        return $this;
    }
	
	public function countOrderItems($order)
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