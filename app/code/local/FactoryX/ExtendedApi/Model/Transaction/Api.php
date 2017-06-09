<?php
/**
 * Transaction api
 *
 * @package    FactoryX_ExtendedApi
 * @author     Factory X Team <developers@factoryx.com.au>
 */
 
 class FactoryX_ExtendedApi_Model_Transaction_Api extends Mage_Api_Model_Resource_Abstract {

    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Retrieve transaction info
     *
     * @param int|string $transactionId
     * @return array
     */
    public function info($transactionId) {
    	//Mage::helper('extended_api')->log(sprintf("%s->%s", __METHOD__, $transactionId));
    	
        //$transaction = Mage::getModel('sales/order_payment_transaction')->loadByTxnId($transactionId);
        /*
        $transaction = Mage::getModel('sales/order_payment_transaction')->load($transactionId);
        Mage::helper('extended_api')->log(sprintf("%s->%s", __METHOD__, print_r($transaction, true)));
        */

		$query = sprintf("select * from sales_payment_transaction where txn_id = '%s'", $transactionId);
		//Mage::helper('extended_api')->log(sprintf("%s->query=%s", __METHOD__, $query));
		
    	$transaction = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($query); 
        //Mage::helper('extended_api')->log(sprintf("%s->%s", __METHOD__, print_r($transaction, true)));
        
        //if (!$transaction->getId()) {
        $retVal = false;
        //if (!array_key_exists('transaction_id', $transaction) || !$transaction['transaction_id']) {        	
        if ($transaction) {
        	//Mage::helper('extended_api')->log(sprintf("%s->%s", __METHOD__, print_r($transaction, true)));
        	$retVal = $transaction[0];
        }
        else {
			$this->_fault('not_exists');
		}

        //return $transaction->toArray();
        return $transaction;
    }

    /**
     * Retrieve list of transactions with basic info (id, ...)
     *
     * @param null|object|array $filters
     * @param string|int $store
     * @return array
     */
    public function items($filters = null, $store = null) {
        $transactions = array();
        Mage::helper('extended_api')->log(sprintf("%s->filters: %s", __METHOD__, print_r($filters, true)) );
        
        if (!is_array($filters)) {
            $filters = array();
        }
            
        if (array_key_exists("from", $filters)) {
            $fromDate = $filters['from'];
        }
        else {
            $filters['from'] = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $fromDate = Mage::getModel('core/date')->gmtDate(self::DATE_FORMAT, $filters['from']);
        }
        
        if (array_key_exists("to", $filters)) {
            $toDate = $filters['to'];
        }
        else {
            $filters['to'] = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
            $toDate = Mage::getModel('core/date')->gmtDate(self::DATE_FORMAT, $filters['to']);
        }
        
        Mage::helper('extended_api')->log(sprintf("%s->UTC: from=%s, to=%s", __METHOD__, $fromDate, $toDate));
    	
    	//$transactions = Mage::getResourceModel('sales/order_payment_transaction_collection')
    	$transactions = Mage::getModel('sales/order_payment_transaction')->getCollection()
    	    ->addPaymentInformation(array('method'))
            //->addAttributeToFilter('txn_type', array('eq' => 'capture'))
            ->addAttributeToFilter('created_at', array(
                'from'  => $fromDate,
                'to'    => $toDate,
                'date'  => true,
            )
        );
        /*
        $transactions = Mage::getResourceModel('sales/order_payment_transaction_collection')
            ->addPaymentInformation(array('method'))
            ->addAttributeToFilter('txn_type', array('eq' => 'capture'))
            ->addAttributeToFilter('created_at', array(
                'from'  => $fromDate,
                'to'    => $toDate,
                'date'  => true,
            )
        );
        */        
        
    	$results = array();
        if (count($transactions) > 0) {
            Mage::helper('extended_api')->log(sprintf("%s->transactions: %d", __METHOD__, count($transactions)) );    
            foreach($transactions as $tran) {
                Mage::helper('extended_api')->log(sprintf("%s->data: %s", __METHOD__, print_r($tran->getData(), true)) );
                $results[] = array(
                    "id"        => $tran->getData("transaction_id"),
                    "number"    => $tran->getData("txn_id"),
                    "date"      => $tran->getData("created_at"),
                    "type"      => $tran->getData("txn_type"),
                    "order_id"  => $tran->getData("order_id"),
                    "is_closed" => $tran->getData("is_closed"),
                    "method"    => $tran->getData("method"),
                    // this gets unpacked to info->raw_details_info
                    "info"      => $tran->getData("additional_information")
                    // is this required unserialize additional_information
                    //"infou"     => unserialize($tran->getData("additional_information"))
                );
            }
        }
        else {
			$this->_fault('no_records', sprintf("No transaction exist between (%s - %s)", $fromDate, $toDate));
		}
		//Mage::helper('extended_api')->log(sprintf("%s->results: %s", __METHOD__, print_r($results, true)) );
        return $results;
    }

}