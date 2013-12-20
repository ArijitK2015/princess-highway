<?php
/**

sales_email_stage2
sales_email/stage2/template

References
http://stackoverflow.com/questions/3046530/magento-order-status-change-events
*/

class FactoryX_Sales_Model_Observer {
		
    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/stage2/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/stage2/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/stage2/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/stage2/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/stage2/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/stage2/enabled';

	private $debug = false;
	private $notify = false;
	private $alwaysNotify = false;
	private $visible = false; // not used

	public function __construct() {
    }
    
    /**
    call before Mage_Sales_Model_Order->addStatusHistoryComment

    TODO:
    - override OrderController $notify
	- implement autoNotify override notify
	- add comment to the email???
    */
    public function statusHistoryCommentBefore($observer) {
		
		$enabled = Mage::getStoreConfig(self::XML_PATH_EMAIL_ENABLED, Mage::app()->getStore()->getStoreId());
		
		$event = $observer->getEvent(); //Fetches the current event
		Mage::helper('fx_sales')->log(sprintf("%s->%s=%s", __METHOD__, 'event', $event->getName()));
		
		$order = $event->getOrder();
		//Mage::helper('fx_sales')->log(sprintf("%s", get_class($order)));

		// get the post data
		$data = Mage::app()->getFrontController()->getRequest()->getPost('history');	
        $this->notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
        $this->visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;
		//Mage::helper('fx_sales')->log(sprintf("historyComment=%s", $data['comment']));
		//Mage::helper('fx_sales')->log(sprintf("status=%s", $data['status']));
		//Mage::helper('fx_sales')->log(sprintf("request=%s", print_r($request, true)));	
		
		$state = $order->getState();
		$nextStatus = $data['status'];
		$prevStatus = $order->getStatus();
				
		Mage::helper('fx_sales')->log(sprintf("%s->state=%s,prevStatus=%s,nextStatus=%s", __METHOD__, $state, $prevStatus, $nextStatus));
		if (!$enabled || !preg_match(sprintf("/^%s$/", FactoryX_Sales_Model_Order::STATUS_PROCESSING_STAGE2), $data['status'])) {
			// Mage::helper('fx_sales')->log("do nothing");
			return $this;
		}

		// email already sent? (check history)
		/*
		$cnt = 0;
		foreach ($order->getAllStatusHistory() as $comment) {
			$createdAt = $comment->getCreatedAtDate();
			$ts = strtotime($createdAt);
			$notified = $comment->getIsCustomerNotified();
			$statusLabel = $comment->getStatusLabel();
			//Mage::helper('fx_sales')->log(sprintf("%d - %s", $cnt++, $statusLabel));
			if (preg_match("/stage 2/i", $statusLabel)) {
				$this->notify = false;
				$eventMsg = sprintf("this order has previously been in '%s' and the customer notified!", $prevStatus);
				Mage::helper('fx_sales')->log($eventMsg);
				Mage::getSingleton('adminhtml/session')->addWarning($eventMsg); 
				break;
			}
		}
		*/
		
        // Check if at least one recepient is found
        //if (!$this->notify && !$copyTo) {
        //    return $this;
        //}

		//Mage::helper('fx_sales')->log(sprintf("itemList=%s", $itemList));
		$itemList = $this->_getItemsNotShipped($order);
		if (strlen(trim($itemList)) == 0) {
			// do nothing
			$eventMsg = sprintf("This order has already been 'shipped' OR not invoiced completely!");
			Mage::throwException(sprintf("WARNING: %s", $eventMsg));
		}
        $sentTo = $this->_sendStage2Email($order, $itemList, $data['comment']);
		$eventMsg = sprintf("stage2 processing email was sent to '%s'", $sentTo);
		Mage::helper('fx_sales')->log($eventMsg);
		Mage::getSingleton('adminhtml/session')->addSuccess($eventMsg); 		
	}
    
    /*
    	if (preg_match("/^processing$/", $nextStatus) && preg_match(sprintf("/^%s$/", FactoryX_Sales_Model_Order::STATUS_PROCESSING_STAGE2), $prevStatus)) {
			$eventMsg = "cannot change back from 'Processing Stage 2' to 'Processing'!";
			//Mage::helper('fx_sales')->log($eventMsg);
			Mage::throwException(sprintf("WARNING: %s", $eventMsg));
		}
	*/
    
    /**
    */
	public function statusHistoryCommentAfter($observer) {
		$event = $observer->getEvent(); //Fetches the current event
		//Mage::helper('fx_sales')->log(sprintf("***%s->%s=%s", __METHOD__, 'event', $event->getName()));
		//$order = $event->getOrder();
	}

    /**
    get email addresses
    */	
    protected function _getEmails($configPath) {
        $data = Mage::getStoreConfig($configPath, Mage::app()->getStore()->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }
   
    /**
    send email
    */
    protected function _sendStage2Email($order, $itemList, $comment) {
		$storeId = Mage::app()->getStore()->getStoreId();
		$storeName = Mage::getStoreConfig('general/store_information/name', Mage::app()->getStore());

        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);
		Mage::helper('fx_sales')->log(sprintf("%s->copyTo:%s,copyMethod:%s", __METHOD__, $copyTo, $copyMethod));
		
        // Retrieve corresponding email template id and customer name
        $custFirstName = "Valued Customer";
        if ($order->getCustomerIsGuest()) {
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $order->getBillingAddress()->getName();
        }
        else {
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
		}
		else {
			$comment = "";
		}
		    		
		$sendTo = "";
		$mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        
        // add customer
        if ($this->notify) {
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
        if ($copyTo && ($copyMethod == 'copy' || !$this->notify)) {
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

	/*
	get items that haven't been shipped yet
	
	STATUS_PENDING        = 1; // No items shipped, invoiced, canceled, refunded nor backordered
	STATUS_SHIPPED        = 2; // When qty ordered - [qty canceled + qty returned] = qty shipped
	STATUS_INVOICED       = 9; // When qty ordered - [qty canceled + qty returned] = qty invoiced
	STATUS_BACKORDERED    = 3; // When qty ordered - [qty canceled + qty returned] = qty backordered
	STATUS_CANCELED       = 5; // When qty ordered = qty canceled
	STATUS_PARTIAL        = 6; // If [qty shipped or(max of two) qty invoiced + qty canceled + qty returned]
	                           // < qty ordered
	STATUS_MIXED          = 7; // All other combinations
	STATUS_REFUNDED       = 8; // When qty ordered = qty refunded
	
	STATUS_RETURNED       = 4; // When qty ordered = qty returned // not used at the moment
	*/
    protected function _getItemsNotShipped($order) {
		// get the items that have NOT been shipped
		$itemList = "";
		$itemsToShip = 0;
		$items = $order->getAllItems();
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
}