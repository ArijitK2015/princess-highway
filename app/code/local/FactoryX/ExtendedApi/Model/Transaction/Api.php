<?php
/**
$transaction = Mage::getModel('factoryx/transaction_api');

desc sales_payment_transaction
select * from sales_payment_transaction where txn_id = "VKMR4EC63EA7";
*/
class FactoryX_ExtendedApi_Model_Transaction_Api extends Mage_Api_Model_Resource_Abstract {

    public function info($transactionId) {
    	//Mage::helper('transaction')->log(sprintf("%s->%s", __METHOD__, $transactionId));
    	
        //$transaction = Mage::getModel('sales/order_payment_transaction')->loadByTxnId($transactionId);
        /*
        $transaction = Mage::getModel('sales/order_payment_transaction')->load($transactionId);
        Mage::helper('transaction')->log(sprintf("%s->%s", __METHOD__, print_r($transaction, true)));
        */

		$query = sprintf("select * from sales_payment_transaction where txn_id = '%s'", $transactionId);
		//Mage::helper('transaction')->log(sprintf("%s->query=%s", __METHOD__, $query));
		
    	$transaction = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query); 
        //Mage::helper('transaction')->log(sprintf("%s->%s", __METHOD__, print_r($transaction, true)));
        
        //if (!$transaction->getId()) {
        $retVal = false;
        //if (!array_key_exists('transaction_id', $transaction) || !$transaction['transaction_id']) {        	
        if ($transaction) {
        	//Mage::helper('transaction')->log(sprintf("%s->%s", __METHOD__, print_r($transaction, true)));
        	$retVal = $transaction[0];
        }
        else {
			$this->_fault('not_exists');
		}

        //return $transaction->toArray();
        return $transaction;
    }

}