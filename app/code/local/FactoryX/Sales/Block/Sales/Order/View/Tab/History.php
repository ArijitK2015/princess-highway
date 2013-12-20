<?php
/**

app\design\adminhtml\default\default\template\sales\order\view\history.phtml
app\design\adminhtml\default\default\template\sales\order\view\tab\history.phtml
*/

/**
 * Order history tab
 */
class FactoryX_Sales_Block_Sales_Order_View_Tab_History extends Mage_Adminhtml_Block_Sales_Order_View_Tab_History {
// Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Compose and get order full history.
     * Consists of the status history comments as well as of invoices, shipments and creditmemos creations
     * @return array
     */
    public function getFullHistory() {
		$order = $this->getOrder();
    	//Mage::helper('fx_sales')->log(sprintf("%s=%s", __METHOD__, $order->getIncrementId()));
		$history = array();
		// iterate Mage_Sales_Model_Order_Status_History		
	    foreach ($order->getAllStatusHistory() as $orderComment){
	    	$createdAt = $orderComment->getCreatedAt();
	    	// avoid timestamp clashes
	    	$ts = strtotime($createdAt);
	    	while(array_key_exists($ts, $history)) {
	    		$ts++;
	    	}
	        $history[$ts] = $this->_prepareHistoryItem(
	            $orderComment->getStatusLabel(),
	            $orderComment->getIsCustomerNotified(),
	            $orderComment->getCreatedAtDate(),
	            $orderComment->getComment(),
	            $orderComment->getTrackUser(),
	            $orderComment->getTrackUserName()
	        );
	    }
	
	    foreach ($order->getCreditmemosCollection() as $_memo){
	    	$createdAt = $_memo->getCreatedAt();
	    	$ts = strtotime($createdAt);
	    	while(array_key_exists($ts, $history)) {
	    		$ts++;
	    	}
	        $history[$ts] = $this->_prepareHistoryItem(
				$this->__('Credit Memo #%s created',
				$_memo->getIncrementId()),
				$_memo->getEmailSent(),
				$_memo->getCreatedAtDate()
			);
	        foreach ($_memo->getCommentsCollection() as $_comment){
	        	$createdAt = $_comment->getCreatedAt();
	        	// $_comment->getEntityId()
		    	$ts = strtotime($createdAt);
		    	while(array_key_exists($ts, $history)) {
		    		$ts++;
		    	}	        	
	            $history[$ts] = $this->_prepareHistoryItem(
	            	$this->__('Credit Memo #%s comment added',
	            	$_memo->getIncrementId()),
					$_comment->getIsCustomerNotified(),
					$_comment->getCreatedAtDate(),
					$_comment->getComment(),
					$_comment->getTrackUser(),
					$_comment->getTrackUserName()
				);
	        }
	    }
	
	    foreach ($order->getShipmentsCollection() as $_shipment){
	    	$createdAt = $_shipment->getCreatedAt();
	    	$ts = strtotime($createdAt);
	    	while(array_key_exists($ts, $history)) {
	    		$ts++;
	    	}	    	
	        $history[$ts] = $this->_prepareHistoryItem(
	        	$this->__('Shipment #%s created',
        		$_shipment->getIncrementId()),
                $_shipment->getEmailSent(),
                $_shipment->getCreatedAtDate()
			);
	        foreach ($_shipment->getCommentsCollection() as $_comment) {
	        	$createdAt = $_comment->getCreatedAt();
		    	$ts = strtotime($createdAt);
		    	while(array_key_exists($ts, $history)) {
		    		$ts++;
		    	}
	            $history[$ts] = $this->_prepareHistoryItem(
	            	$this->__('Shipment #%s comment added',
	            	$_shipment->getIncrementId()),
					$_comment->getIsCustomerNotified(),
					$_comment->getCreatedAtDate(),
					$_comment->getComment(),
					$_comment->getTrackUser(),
					$_comment->getTrackUserName()
				);
	        }
	    }
	
	    foreach ($order->getInvoiceCollection() as $_invoice) {
	    	$createdAt = $_invoice->getCreatedAt();
	    	$ts = strtotime($createdAt);
	    	while(array_key_exists($ts, $history)) {
	    		$ts++;
	    	}
	    	$history[$ts] = $this->_prepareHistoryItem(
	        	$this->__('Invoice #%s created',
	        	$_invoice->getIncrementId()),
				$_invoice->getEmailSent(),
				$_invoice->getCreatedAtDate()
			);
	        foreach ($_invoice->getCommentsCollection() as $_comment) {
	        	$createdAt = $_comment->getCreatedAt();
		    	$ts = strtotime($createdAt);
		    	while(array_key_exists($ts, $history)) {
		    		$ts++;
		    	}
	            $history[$ts] = $this->_prepareHistoryItem($this->__('Invoice #%s comment added', $_invoice->getIncrementId()),
					$_comment->getIsCustomerNotified(),
	                $_comment->getCreatedAtDate(),
	                $_comment->getComment(),
	                $_comment->getTrackUser(),
	                $_comment->getTrackUserName()
				);
	        }
	    }
	
	    foreach ($order->getTracksCollection() as $_track) {
	    	$createdAt = $_track->getCreatedAt();
	    	$ts = strtotime($createdAt);
	    	while(array_key_exists($ts, $history)) {
	    		$ts++;
	    	}	    	
	    	$_shipment = Mage::getModel('sales/order_shipment')->load($_track->getParentId());	    	
	        $history[$ts] = $this->_prepareHistoryItem(
	        	$this->__("Shipment #%s created, assigned tracking number '%s' for carrier '%s'", $_shipment->getIncrementId(), $_track->getNumber(), $_track->getTitle()),
				false,
				$_track->getCreatedAtDate()
			);
	    }

		//Mage::helper('fx_sales')->log(sprintf("count:%d", count($history)));
		// sort by date latest to earliest (decending)
	    uksort($history, array("FactoryX_Sales_Block_Sales_Order_View_Tab_History", "cmp"));
	    return $history;
	}

	static function cmp($a, $b) {
	    if ($a == $b) {
	        return 0;
	    }
	    return ($a > $b) ? -1 : 1;
	}

	/**
	deprecated as getCreatedAtDate() returns 10/%m/%Y 11:00:00 PM
	use getCreatedAt() instead! (returns timestamp)
	*/
	public function _strtotime($time) {
		//Mage::helper('fx_sales')->log("_strtotime=$time");
		$arr = strptime($time, "%d/%m/%Y %H:%M:%S");
		//Mage::helper('fx_sales')->log("arr=" . print_r($arr, true));
		$year = $arr['tm_year'] + 1900;
		$hour = $arr['tm_hour'];
		/*
		if ($arr['unparsed'] == " PM") {
			$hour += 12;
		}
		*/
		$time = mktime($hour,$arr['tm_min'],$arr['tm_sec'],$arr['tm_mon'],$arr['tm_mday'],$year);
		//Mage::helper('fx_sales')->log("_strtotime=$time");
		return $time;
	}

	protected function _prepareHistoryItem($label, $notified, $created, $comment = '' , $trackUser = '' , $trackUserName ='') {
        return array(
            'title'      => $label,
            'notified'   => $notified,
            'track_user' => $trackUser,
            'track_user_name' => $trackUserName,
            'comment'    => $comment,
            'created_at' => $created            
        );
    }

	/**
     * Status history date/datetime getter
     * @param array $item
     * @return string
     */
    public function getItemCreatedAt(array $item, $dateType = 'date', $format = 'medium')
    {
        if (!isset($item['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($item['created_at'], $format);
        }
        return $this->helper('core')->formatTime($item['created_at'], $format);
    }
   
}
?>

