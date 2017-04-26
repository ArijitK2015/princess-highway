<?php
/**
 * Class FactoryX_Sales_Model_Observer
 * sales_email_stage2
 * sales_email/stage2/template
 * References: http://stackoverflow.com/questions/3046530/magento-order-status-change-events
 */

class FactoryX_Sales_Model_Observer {

	/**
	 * Constants declaration
	 */
	const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/stage2/template';
	const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/stage2/guest_template';
	const XML_PATH_EMAIL_IDENTITY               = 'sales_email/stage2/identity';
	const XML_PATH_EMAIL_COPY_TO                = 'sales_email/stage2/copy_to';
	const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/stage2/copy_method';
	const XML_PATH_EMAIL_ENABLED                = 'sales_email/stage2/enabled';

	/**
	 * Notification flag
	 * @var bool
	 */
	private $_notify = false;
    private $_notifyStage2 = false;

	/**
	 * Change the order status after a shipment has been saved
	 * @param Varien_Event_Observer $observer
	 */
	public function changeOrderStatus(Varien_Event_Observer $observer)
	{
		// Get the shipment
		$observedShipment = $observer->getEvent()->getShipment();
		// Check if tracked
		$tracked = true;
		foreach($observedShipment->getOrder()->getShipmentsCollection() as $shipment)
		{
			if (!$shipment->getAllTracks())
			{
				$tracked = false;
			}
		}

		// If tracked and the status is partially shipped no tracking
		if ($tracked
			&& ($observedShipment->getOrder()->getStatus() ==  FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING)) {
			$state = FactoryX_Sales_Model_Order::STATE_PROCESSING;
			$status = FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED;
			$comment = sprintf("automatically set status to '%s->%s'", $state, $status);
			// We change it to partially shipped
			$observedShipment->getOrder()->setState($state, $status, $comment, false);
			// Save attribute instead of the entire object
			$observedShipment->getOrder()->getResource()->saveAttribute($observedShipment->getOrder(),'state');
			$observedShipment->getOrder()->getResource()->saveAttribute($observedShipment->getOrder(),'status');
		} elseif ($tracked
			&& ($observedShipment->getOrder()->getStatus() ==  FactoryX_Sales_Model_Order::STATUS_PROCESSING_SHIPPED_NO_TRACKING)) {
			// If tracked and the status is shipped no tracking
			// We ensure the order can be completed
			if (!$observedShipment->getOrder()->isCanceled()
				&& !$observedShipment->getOrder()->canUnhold()
				&& !$observedShipment->getOrder()->canInvoice()
				&& !$observedShipment->getOrder()->canShip()
				&& (0 == $observedShipment->getOrder()->getBaseGrandTotal() || $observedShipment->getOrder()->canCreditmemo())) {
				// We ensure the order is not already complete
				if ($observedShipment->getOrder()->getState() !== 'complete') {
					// And we complete it
					$observedShipment->getOrder()->setState('complete', true, '');
					$observedShipment->getOrder()->getResource()->saveAttribute($observedShipment->getOrder(),'state');
					$observedShipment->getOrder()->getResource()->saveAttribute($observedShipment->getOrder(),'status');
				}
			}
		}
		// handle status_preorder
		elseif (
		    $tracked
		    &&
	        $observedShipment->getOrder()->getState() == 'complete'
	        &&
	        $observedShipment->getOrder()->getData('status_preorder') == 'processing'
		) {
			// We ensure the order can be completed
			if (!$observedShipment->getOrder()->isCanceled()
				&& !$observedShipment->getOrder()->canUnhold()
				&& !$observedShipment->getOrder()->canInvoice()
				&& !$observedShipment->getOrder()->canShip()
				&& (0 == $observedShipment->getOrder()->getBaseGrandTotal() || $observedShipment->getOrder()->canCreditmemo())) {
				// We ensure the order is not already complete
				Mage::log(sprintf("%s->order[state]=%s", __METHOD__, $observedShipment->getOrder()->getState()) );
				$observedShipment->getOrder()->setState('complete', true, '');
				$observedShipment->getOrder()->setStatusPreorder(Mage_Sales_Model_Order::STATE_COMPLETE, true);
				$observedShipment->getOrder()->getResource()->saveAttribute($observedShipment->getOrder(),'state');
				$observedShipment->getOrder()->getResource()->saveAttribute($observedShipment->getOrder(),'status');
				$observedShipment->getOrder()->getResource()->saveAttribute($observedShipment->getOrder(),'status_preorder');
		    }
		}
	}

	/**
	 * Call before Mage_Sales_Model_Order->addStatusHistoryComment
	 *
	 * @TODO: override OrderController $notify
	 * @TODO: implement autoNotify override notify
	 * @TODO: add comment to the email???
	 * @param $observer
	 * @return $this|bool
	 * @throws Mage_Core_Exception
	 */
	public function statusHistoryCommentBefore(Varien_Event_Observer $observer)
	{
		// Is stage 2 enabled ?
		$enabled = Mage::getStoreConfig(self::XML_PATH_EMAIL_ENABLED, Mage::app()->getStore()->getStoreId());
		// Get the order
		$order = $observer->getEvent()->getOrder();
		//Mage::helper('fx_sales')->log(sprintf("%s->%s", __METHOD__, get_class($order)) );

        // check if the request is from the api, then get data from event
        if (Mage::getSingleton('api/server')->getAdapter() != null) {
            $history = array(
                'status' => $observer->getEvent()->getStatus(),
                'comment' => $observer->getEvent()->getComment()
            );
            // is this event set? (see sales_order.addComment)
            if ($observer->getEvent()->getIsCustomerNotified()) {
                Mage::helper('fx_sales')->log(sprintf("%s->is_customer_notified: YES", __METHOD__) );
                $history['is_customer_notified'] = $observer->getEvent()->getIsCustomerNotified();
            }
            $this->_notify = isset($history['is_customer_notified']) ? $history['is_customer_notified'] : false;
            $this->_notifyStage2 = true;
        }
        else {
            // Get the post data
            $history = Mage::app()->getFrontController()->getRequest()->getPost('history');
            // Set the notify flag
            $this->_notify = isset($history['is_customer_notified']) ? $history['is_customer_notified'] : false;
            $this->_notifyStage2 = $this->_notify;
            // always send it!
            $this->_notifyStage2 = true;
        }

        //Mage::helper('fx_sales')->log(sprintf("%s->history: %s", __METHOD__, print_r($history, true)) );

		// Get the order state
		//$state = $order->getState();
		// Get the status change
		//$nextStatus = $data['status'];
		// Get the old status
		//$prevStatus = $order->getStatus();
		//Mage::helper('fx_sales')->log(sprintf("%s->state=%s,prevStatus=%s,nextStatus=%s", __METHOD__, $state, $prevStatus, $nextStatus));

		// If stage 2 is not enabled nor the new status is stage 2 we stop here
		if (!$enabled || !preg_match(sprintf("/^%s$/", FactoryX_Sales_Model_Order::STATUS_PROCESSING_STAGE2), $history['status'])) {
			//Mage::helper('fx_sales')->log("do nothing");
			return $this;
		}

		//Mage::helper('fx_sales')->log(sprintf("itemList=%s", $itemList));
		// Get the list of unshipped items
		$itemList = $this->_getItemsNotShipped($order);
		// If empty list
		if (strlen(trim($itemList)) == 0) {
			// Do nothing
			$eventMsg = sprintf("This order has already been 'shipped' OR not invoiced completely!");
			Mage::throwException(sprintf("WARNING: %s", $eventMsg));
		}
		// Send stage 2 email
        Mage::helper('fx_sales')->log(sprintf("%s->_sendStage2Email: %s", __METHOD__, $order->getCustomerEmail()) );
		$sentTo = $this->_sendStage2Email($order, $itemList, $history['comment']);

		// Log
		$eventMsg = sprintf("stage2 processing email was sent to '%s'", $sentTo);
		Mage::helper('fx_sales')->log($eventMsg);
		Mage::getSingleton('adminhtml/session')->addSuccess($eventMsg);
		return true;
	}

	/**
	 * Call after Mage_Sales_Model_Order->addStatusHistoryComment
	 *
	 * @todo finish tracking user that edits the code (beware, paypal express does trigger this event)
	 * @param $observer
	 */
	public function statusHistoryCommentAfter(Varien_Event_Observer $observer)
	{
		// Get the history
		$history = $observer->getEvent()->getHistory();

		// Add track user
		$history->setTrackUser($this->_getAdminUser())->save();
	}

	/**
	 * Call after sales order save
	 *
	 * @param $observer
	 */
	public function salesOrderAfter(Varien_Event_Observer $observer)
	{
		// Get the order
		$order = $observer->getOrder();

		/* *************** START CUSTOM CODE CHANGING ORDER STATUS DEPENDING ON THE SHIPMENT AND TRACKING *************** */
		// Skip if stage 2
		if (preg_match(sprintf("/^%s$/", FactoryX_Sales_Model_Order::STATUS_PROCESSING_STAGE2), $order->getStatus())) {
			//Mage::helper('fx_sales')->log(sprintf("skip check %s", __METHOD__));
			return;
		}

		// If we still can ship, can't invoice and the shipment collection is not empty (which means there were already shipments)

		//Mage::helper('fx_sales')->log(sprintf("%s->state=%s,status=%s", __METHOD__, $order->getState(), $order->getStatus()) );
		if ($order->canShip() && !$order->canInvoice() && $order->hasShipments()) {
			// We set the state to processing partially shipped
			$tracked = true;
			foreach($order->getShipmentsCollection() as $shipment) {
				if (!$shipment->getAllTracks()) {
					$tracked = false;
				}
			}
			// Partially shipped no tracking
			if (!$tracked) {
				$state = FactoryX_Sales_Model_Order::STATE_PROCESSING;
				$status = FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING;
				//Mage::helper('fx_sales')->log(sprintf("status=%s", self::STATUS_PROCESSING_PARTIALLY_SHIPPED_NO_TRACKING));
				$comment = sprintf("automatically set status to '%s->%s'", $state, $status);
				$order->setState(
					$state,
					$status,
					$comment,
					$isCustomerNotified = false
				);
				$order->getResource()->saveAttribute($order,'state');
				$order->getResource()->saveAttribute($order,'status');
			} else {
				// Partially shipped
				$state = FactoryX_Sales_Model_Order::STATE_PROCESSING;
				$status = FactoryX_Sales_Model_Order::STATUS_PROCESSING_PARTIALLY_SHIPPED;
				$comment = sprintf("automatically set status to '%s->%s'", $state, $status);
				$order->setState(
					$state,
					$status,
					$comment,
					$isCustomerNotified = false
				);
				$order->getResource()->saveAttribute($order,'state');
				$order->getResource()->saveAttribute($order,'status');
			}
		}

		// If we can't ship anymore
		if (!$order->isCanceled() && !$order->canUnhold() && !$order->canInvoice() && !$order->canShip()) {
			// We check if every shipment is tracked
			$tracked = true;
			foreach($order->getShipmentsCollection() as $shipment) {
				if (!$shipment->getAllTracks()) {
					$tracked = false;
				}
			}
			// Shipped no tracking
			if ($tracked) {
			    // fix status_preorder
			    if (
        	        $order->getState() == 'complete'
        	        &&
        	        $order->getStatus() == 'complete'
        	        &&
        	        $order->getData('status_preorder') == 'processing'
                ) {
    				$order->setStatusPreorder(Mage_Sales_Model_Order::STATE_COMPLETE, true)->save();
			    }
			}
			else {
				$state = FactoryX_Sales_Model_Order::STATE_PROCESSING;
				$status = FactoryX_Sales_Model_Order::STATUS_PROCESSING_SHIPPED_NO_TRACKING;
				$comment = sprintf("automatically set status to '%s->%s'", $state, $status);
				$order->setState(
					$state,
					$status,
					$comment,
					$isCustomerNotified = false
				);
				$order->getResource()->saveAttribute($order,'state');
				$order->getResource()->saveAttribute($order,'status');
			}
		}
	}

	/**
	 * Get admin user commenting the order
	 * @return string
	 */
	protected function _getAdminUser()
	{
		$userName = "unknown";
		Mage::getSingleton('core/session', array('name'=>'adminhtml'));
		if(Mage::getSingleton('admin/session')->isLoggedIn()) {
			$userInfo = Mage::getSingleton('admin/session')->getUser();
			if ($userInfo) {
				$userName = $userInfo->getUsername();
			}
		} else {
			if(Mage::getSingleton('customer/session')->isLoggedIn()) {
				$userName = Mage::getSingleton('customer/session')->getCustomer()->getName();
			} else {
				$userName = "frontend";
			}
		}
		//Mage::helper('fx_sales')->log(sprintf("%s->userName=%s", __METHOD__, $userName));
		return $userName;
	}

	/**
	 * Get email addresses
	 * @param $configPath
	 * @return array|bool
	 */
	protected function _getEmails($configPath)
	{
		$data = Mage::getStoreConfig($configPath, Mage::app()->getStore()->getStoreId());
		if (!empty($data)) {
			return explode(',', $data);
		}
		return false;
	}

	/**
	 * Send stage 2 email
	 * @param $order
	 * @param $itemList
	 * @param $comment
	 * @return string
	 */
	protected function _sendStage2Email($order, $itemList, $comment)
	{
		// Get the store ID
		$storeId = Mage::app()->getStore()->getStoreId();
		$storeName = Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore());

		$copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
		$copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
		//Mage::helper('fx_sales')->log(sprintf("%s->copyTo:%s,copyMethod:%s", __METHOD__, $copyTo, $copyMethod));

		// Retrieve corresponding email template id and customer name
		$custFirstName = "Valued Customer";
		if ($order->getCustomerIsGuest()) {
			$templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
			$customerName = $order->getBillingAddress()->getName();
		} else {
			$templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
			$customerName = $order->getCustomerName();
		}
		// get the first part of the name
		if (preg_match('/\s/', $customerName)) {
			$name = preg_split('/\s/', $customerName);
			$custFirstName = $name[0];
		}

		if (strlen(trim($comment))) {
			$comment = sprintf("Additional Comments:\n%s\n", $comment);
		} else {
			$comment = "";
		}

		$sendTo = "";
		$mailer = Mage::getModel('core/email_template_mailer');
		$emailInfo = Mage::getModel('core/email_info');

		// add customer
        Mage::helper('fx_sales')->log(sprintf("%s->_notify: %d|%d", __METHOD__, $this->_notify, $this->_notifyStage2));
		if ($this->_notifyStage2) {
			$custEmail = $order->getCustomerEmail();
			$emailInfo->addTo($custEmail, $customerName);
			$sendTo .= (strlen($sendTo))?",".$custEmail:$custEmail;
		}

		if ($copyTo && $copyMethod == 'bcc') {
			// Add bcc to customer email
			foreach ($copyTo as $email) {
				$emailInfo->addBcc($email);
				$sendTo .= (strlen($sendTo))?",".$email:$email;
			}
		}
		$mailer->addEmailInfo($emailInfo);

		// Email copies are sent as separated emails if their copy method is 'copy'
		if ($copyTo && ($copyMethod == 'copy' || !$this->_notifyStage2)) {
			foreach ($copyTo as $email) {
				$emailInfo = Mage::getModel('core/email_info');
				$emailInfo->addTo($email);
				$mailer->addEmailInfo($emailInfo);
				$sendTo .= (strlen($sendTo))?",".$email:$email;
			}
		}

		// Set all required params and send emails
		$sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId);
		$mailer->setSender($sender);
		$mailer->setStoreId($storeId);
		// sales_email_stage2_template
		//Mage::helper('fx_sales')->log(sprintf("templateId=%s", $templateId));
		$mailer->setTemplateId($templateId);
		$mailer->setTemplateParams(array(
				'storeName' 	=> $storeName,
				'orderId'		=> $order->getIncrementId(),
				'custFirstName' => $custFirstName,
				'itemList'		=> $itemList,
				'comment'		=> $comment,
			)
		);
		$mailer->send();
		return $sendTo;
	}

	/**
	 * Get items that haven't been shipped yet
	 *
	 * STATUS_PENDING        = 1; // No items shipped, invoiced, canceled, refunded nor backordered
	 * STATUS_SHIPPED        = 2; // When qty ordered - [qty canceled + qty returned] = qty shipped
	 * STATUS_INVOICED       = 9; // When qty ordered - [qty canceled + qty returned] = qty invoiced
	 * STATUS_BACKORDERED    = 3; // When qty ordered - [qty canceled + qty returned] = qty backordered
	 * STATUS_CANCELED       = 5; // When qty ordered = qty canceled
	 * STATUS_PARTIAL        = 6; // If [qty shipped or(max of two) qty invoiced + qty canceled + qty returned]
	 *                            // < qty ordered
	 * STATUS_MIXED          = 7; // All other combinations
	 * STATUS_REFUNDED       = 8; // When qty ordered = qty refunded
	 * STATUS_RETURNED       = 4; // When qty ordered = qty returned // not used at the moment
	 * @param Mage_Sales_Model_Order $order
	 * @return string
	 */
	protected function _getItemsNotShipped($order)
	{
		$itemList = "";
		$itemsToShip = 0;
		$items = $order->getAllItems();
		// Loop through every item
		foreach($items as $itemId => $item) {
			//Mage::helper('fx_sales')->log(sprintf("%s->%s [%d,%d]", __METHOD__, $item->getName(), $item->getStatusId(), $item->getQtyToShip()));
			if ($item->getStatusId() == Mage_Sales_Model_Order_Item::STATUS_INVOICED && $item->getQtyToShip() > 0) {
				$itemsToShip++;
				$itemList .= sprintf("%s x %d [%s]\n",
					$item->getName(),
					$item->getQtyToShip(),
					$item->getStatus()
				);
			}
		}
		//Mage::helper('fx_sales')->log(sprintf("%s->itemList=%s", __METHOD__, $itemList));
		return $itemList;
	}

	/**
	 * @param Varien_Event_Observer $observer
	 */
	public function enableZeroTotalRefund(Varien_Event_Observer $observer)
	{
		$creditmemo = $observer->getEvent()->getCreditmemo();
		$canCreditmemoZeroTotalOrders = Mage::helper('fx_sales')->canRefundZeroTotalOrders();
		if ($canCreditmemoZeroTotalOrders) {
			$creditmemo->setAllowZeroGrandTotal(true);
		}
	}
}
