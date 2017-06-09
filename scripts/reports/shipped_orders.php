<?php
/*
http://connect20.magentocommerce.com/community/Order_Status
./mage install http://connect20.magentocommerce.com/community Order_Status
./mage list-files http://connect20.magentocommerce.com/community Order_Status

SQL
sales_flat_order_status_history;

update sales_flat_order_status_history set status = 'processing_shipped_nt' where status = 'processing_shipped_no_tracking';
update sales_flat_order_status_history set status = 'processing_shipped_nt' where status = 'processing_shipped_no_tracking';

select state, status from sales_flat_order;
update sales_flat_order set status = 'processing_shipped_nt' where status = 'processing_shipped_no_tracking';
update sales_flat_order set status = 'processing' where status = '';


const STATE_NEW             = 'new';
const STATE_PENDING_PAYMENT = 'pending_payment';
const STATE_PROCESSING      = 'processing';
const STATE_COMPLETE        = 'complete';
const STATE_CLOSED          = 'closed';
const STATE_CANCELED        = 'canceled';
const STATE_HOLDED          = 'holded';
const STATE_PAYMENT_REVIEW  = 'payment_review';

Sales/Model/Order/Status.php
*/

error_reporting(E_ALL | E_STRICT);

define('MAGENTO', realpath(dirname(__FILE__)));
require_once MAGENTO . '/../../app/Mage.php';

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$storeId = Mage::app()->getStore()->getId();
$websiteId = Mage::app()->getStore()->getWebsiteId();

Varien_Profiler::enable();
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
Mage::app('default');

///////////////////////////////////////////////////////////////////////////////

$orders = Mage::getModel('sales/order')->getCollection()
	->addAttributeToSelect("*")
	->addAttributeToFilter('state', array('processing', 'complete')); 

$test = 0;
$isCustomerNotified = false;
$messages = array();
$comment = sprintf("set via %s on %s", $_SERVER['SCRIPT_NAME'], date(DATE_RFC822));
echo "comment=" . $comment . "\n";

$statuses_processing = array(
	"processing_shipped_nt",
	"processing_part_shipped",
	"processing_part_shipped_nt"
);

foreach($orders as $o) {
	$order_increment_id = $o->getIncrementId();
	//echo("order_increment_id=$order_increment_id\n");
	$order = Mage::getModel('sales/order')->load($o->getId());
	$shipments = $order->getShipmentsCollection();
	$saveOrder = false;
	$messages = array();
	echo sprintf("--->checking order #%s - state: %s, status: %s\n", $order_increment_id, $order->getState(), $order->getStatus());
	if ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
		// partially shipped	
		if (count($shipments) > 0) {
			//echo("order #$order_increment_id is partially shipped\n");
			$status = "processing_part_shipped";
			foreach($shipments as $shipment) {
				//echo("shipment #" . $shipment->getIncrementId() . "\n");
				if ($shipment->getAllTracks()) {
					foreach ($shipment->getAllTracks() as $track) {
						//echo("tracking=" . get_class($track) . "\n");
						$details = $track->getNumberDetail();
						$messages[] = "order is partially shipped #" . $shipment->getIncrementId() . ", tracking[" . $details['title'] . "=" . $details['number'] . "]";
					}
				}
				else {
					$messages[] = "order is partially shipped shipped #" . $shipment->getIncrementId() . ", but has no tracking info!";
					$status = "processing_part_shipped_nt";
				}
			}
			$saveOrder = true;
		}
		else {
			// do nothing
		}
	}
	elseif ($order->getState() == Mage_Sales_Model_Order::STATE_COMPLETE) {
		// if it is complete, but has no tracking
		if (count($shipments) > 0) {
			//echo "shipments=" . count($shipments) . "\n";
			foreach($shipments as $shipment) {
				if (!$shipment->getAllTracks()) {
					$messages[] = sprintf("order is complete, but shipment #" . $shipment->getIncrementId() . " has no tracking info!");
				}
			}
			if (count($messages) > 0) {
				$status = "processing_shipped_nt";
				$saveOrder = true;
			}
		}
		else {
			// do nothing
		}
	}
	if ($saveOrder) {
		$msg = implode("\n", $messages);
		$msg .= "\n" . "new status: " . $status;
		echo "$msg\n";
		Mage::log($msg);
		$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, $status, $comment . ", " . $msg, $isCustomerNotified);
		if (!$test) {
			Mage::log("update order #$order_increment_id");
			$order->save();
		}
	}
}

?>