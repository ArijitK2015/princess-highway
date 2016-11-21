<?php
/**

*/
class FactoryX_ShippedFrom_Helper_Report extends Mage_Core_Helper_Abstract {

    protected $logFileName = 'factoryx_shippedfrom.log';

    const R_WIDTH = 80;
    const SECS_IN_DAY = 86400;


    /**
     * generateReport
     *
     * generate a shipped from report: skus by store
     *
     * @prarm array $range array('from' => utc timestamp, 'to' => utc timestamp)
     * @return array("body" => $body, "subject" => $subject)
     */
    public function generateReport($range) {
        
        //$this->log(sprintf("%s->range: %s", __METHOD__, print_r($range, true)));
        $fromDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $range['from']);
        $toDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $range['to']);
        //$this->log(sprintf("%s->range: %s|%s", __METHOD__, $fromDate, $toDate));

        // get shipments created between collection
        $_shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')->addAttributeToSelect('*');

        $addWhere = "";
        $addWhere .= sprintf("created_at >= '%s' AND created_at <= '%s'", $fromDate, $toDate);
        $_shipmentCollection->getSelect()->where($addWhere);

        $sql = $_shipmentCollection->getSelect();
        Mage::helper('shippedfrom')->log(sprintf("SQL: %s", $sql));
        if (count($_shipmentCollection) == 0) {
            throw new Exception(sprintf("No shipments found between [%s - %s]", date("Y-m-d H:i:s", $range['from']), date("Y-m-d H:i:s", $range['to'])));
        }

        // get store codes
        $storesColl = Mage::getModel('ustorelocator/location')->getCollection()
            ->setOrder('region', 'ASC')
            ->setOrder('store_code', 'ASC');

        $stores = array(0 => "unknown");
        foreach($storesColl as $store) {
            $stores[$store->getId()] = strtoupper($store->getStoreCode());
        }
        $shippedFromStores = array(
            $stores[0] => array()
        );
        
        // iterate shipments
        foreach ( $_shipmentCollection as $s ) {
            // FactoryX_Sales_Model_Order_Shipment
            $shipment = Mage::getModel('sales/order_shipment')->load($s->getId());
            // load the order in
            //$order = Mage::getModel('sales/order')->load($shipment->getOrder()->getId());
            //$orderItems = $order->getItemsCollection();
            //$allOrderItemIds = $orderItems->getAllIds();
            $shippedItemIds = array();
            $shippedItems = $shipment->getItemsCollection();
            /*
             sales/order_shipment_item
            [_data:protected] => Array(
                [entity_id] => 99471
                [parent_id] => 48307
                [row_total] =>
                [price] => 349.0000
                [weight] => 0.2120
                [qty] => 1.0000
                [product_id] => 26178
                [order_item_id] => 184085
                [additional_data] =>
                [description] =>
                [name] => Melt My Ice Cream Heart Dress-Navy-12
                [sku] => agfd501541-Navy-12
            )
            */
            foreach ($shippedItems as $item) {
                $shippedItemIds[] = array(
                    'id'    => $item->getOrderItemId(),
                    'sku'   => $item->getSku(),
                    'name'  => $item->getName(),
                    'qty'   => $item->getQty(),
                    'price' => $item->getPrice()
                );
            }
            $from = 0;
            try {
                $from = $shipment->getData("shipped_from");
            }
            catch(Exception $ex) {
            }
            if (array_key_exists($from, $stores)) {
                if (!array_key_exists($stores[$from], $shippedFromStores)) {
                    $shippedFromStores[$stores[$from]] = array();
                }
            }
            $shippedFromStores[$stores[$from]] = array_merge($shippedFromStores[$stores[$from]], $shippedItemIds);
            /*
            Mage::helper('shippedfrom')->log(sprintf("%s->shippedItemIds[%s|%s]: %s",
                __METHOD__,
                $from, $shipment->getIncrementId(),
                print_r($shippedItemIds, true))
            );
            */
        }
        /**
        Mage::helper('shippedfrom')->log(sprintf("%s",
            print_r($shippedFromStores, true))
        );
        */
        $report = Mage::helper('shippedfrom/report')->formatReport($shippedFromStores);

        Mage::helper('shippedfrom')->log(sprintf("****"));
        Mage::helper('shippedfrom')->log(sprintf("%s", $report));
        Mage::helper('shippedfrom')->log(sprintf("****"));

        $subject = sprintf("%s - Shipped From Stores Report [%s - %s]",
            Mage::app()->getStore()->getName(),
            date("Y-m-d H:i:s", $range['from']),
            date("Y-m-d H:i:s", $range['to'])
        );
        $report = nl2br($report);
        $report = preg_replace('/[ ](?=[^>]*(?:<|$))/', '&nbsp', $report);
        return array("subject" => $subject, "body" => $report);
    }

    /**
     * @param $stores
     * @return string
     */
    public function formatReport($stores) {

        $report = "* Shipped From Stores Report *\n";
        foreach($stores as $store => $items) {
            $total = 0;
            // add header
            if (count($items)) {
                $report .= sprintf("%s\n", str_repeat("=", self::R_WIDTH));
                $report .= sprintf("%s\n", $store);
                $report .= sprintf("%s\n", str_repeat("-", self::R_WIDTH));
            }
            foreach($items as $item) {
                $report .= sprintf("%-25s", substr($item['sku'], 0, 24));
                $report .= sprintf("%-42s", substr($item['name'], 0, 41));
                $report .= sprintf("%4d", $item['qty']);
                $report .= sprintf("%9.2f", $item['price']);
                $report .= sprintf("\n");
                $total += ($item['price'] * $item['qty']);
            }
            if (count($items)) {
                $report .= sprintf("%s\n", str_repeat("-", self::R_WIDTH));
                $report .= sprintf("%80.2f\n", $total);
                $report .= sprintf("%s\n", str_repeat("=", self::R_WIDTH));
            }
        }
        return $report;
    }


    /**
     * @param $report
     * @param $subject
     * @param null $recipientEmail
     * @return null
     */
    public function emailReport($report, $subject, $recipientEmail = null) {

        if ($recipientEmail) {
            Mage::helper('shippedfrom')->log(sprintf("%s->recipientEmail=%s", __METHOD__, $recipientEmail) );
        }

        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('shippedfrom_report');
        // TODO: send as attached csv
        /*
        $emailTemplate->getMail()->createAttachment(
            file_get_contents($csvFileName),
            Zend_Mime::TYPE_OCTETSTREAM,
            Zend_Mime::DISPOSITION_ATTACHMENT,
            Zend_Mime::ENCODING_BASE64,
            basename($csvFileName)
        );
        */
        $sender = array(
            'name'  => Mage::getStoreConfig('trans_email/ident_general/name'),
            'email' => Mage::getStoreConfig('trans_email/ident_general/email')
        );

        // Set variables that can be used in email template
        $vars = array(
            'subject'   => $subject,
            'report'    => $report
        );

        $additionalEmails = Mage::getStoreConfig('shippedfrom/cron_job_store_sales_report/additional_emails');
        if ($additionalEmails) {
            $addr = explode(",", $additionalEmails);
            foreach($addr as $recipientEmail) {
                if (filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
                    Mage::helper('shippedfrom')->log(sprintf("email report to '%s'", $recipientEmail) );
                    $emailTemplate->sendTransactional($templateId = 0, $sender, $recipientEmail, $recipientEmail, $vars);
                    Mage::helper('shippedfrom')->log('sent');
                }
                else {
                    $this->log(sprintf("%s->additional email '%s' invalid!", __METHOD__, $recipientEmail));
                }
            }
        }

        return $recipientEmail;
    }

    /**
     * check current time, if after 9am then process today, otherwise prev day (used for cron)
     *
     * @param $shippingDate
     * @param array $weekDaysToSendOn
     * @param bool $inclusive
     * @return array
     */
    function getDateRange($shippingDate, $weekDaysToSendOn = array(), $inclusive = false) {
        
        if ($shippingDate) {
            $shippingDate = $this->convertDateToTs($shippingDate);
            $tsFrom = mktime(0, 0, 0, date('m', $shippingDate), date('d', $shippingDate), date('Y', $shippingDate));
            $tsTo = mktime(23, 59, 59, date('m', $shippingDate), date('d', $shippingDate), date('Y', $shippingDate));
        }
        else {
            // check time
            $hourOfTheDay = intval(date("G", Mage::getModel('core/date')->timestamp(time())));
            $this->log(sprintf("hourOfTheDay=%s", $hourOfTheDay));
        
            // yesterday
            $tsFrom = Mage::getModel('core/date')->timestamp() - self::SECS_IN_DAY;
            $tsFrom = mktime(0, 0, 0, date('m', $tsFrom), date('d', $tsFrom), date('Y', $tsFrom));
            
            $tsTo = Mage::getModel('core/date')->timestamp() + self::SECS_IN_DAY;
            $tsTo = mktime(0, 0, 0, date('m', $tsTo), date('d', $tsTo), date('Y', $tsTo)) - self::SECS_IN_DAY;
    
            // from today only
            if ($hourOfTheDay > 9 || $inclusive) {
                $tsFrom = Mage::getModel('core/date')->timestamp();
                $tsFrom = mktime(0, 0, 0, date('m', $tsFrom), date('d', $tsFrom), date('Y', $tsFrom));
    
                $tsTo = Mage::getModel('core/date')->timestamp() + self::SECS_IN_DAY;            
                $tsTo = mktime(0, 0, 0, date('m', $tsTo), date('d', $tsTo), date('Y', $tsTo)) - 1;
            }
        }
        $this->log(sprintf("%s->tsFrom=%s[%s],tsTo=%s[%s]", __METHOD__, $tsFrom, date('Y-m-d H:i:s', $tsFrom), $tsTo, date('Y-m-d H:i:s', $tsTo)) );
        //$tsFrom = strtotime($start, time());
        //$tsTo = strtotime("-0 day", time());
        return array('from' => $tsFrom , 'to' => $tsTo);
    }
    
    /**
     *
     */
    public function getTestShipmentDate() {
        $testDate = Mage::getStoreConfig('shippedfrom/cron_job_store_sales_report/test_date');
        $this->log(sprintf("%s->testDate: %s", __METHOD__, $testDate) );
        return $testDate;
    }    

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data) {
        Mage::log($data, null, $this->logFileName);
    }


    /**
     * convertDateToTs
     *
     * convert date string dd/mm/yyyy to UTC timestamp
     *
     * @param string $date e.g. 02/09/14 or 02/09/2014
     * @return long timestamp
     */
    public function convertDateToTs($date) {
        $parts = explode("/", $date);
        // mktime(hour, min, secs, month, day, year, int)
        $ts = mktime(0, 0, 0, $parts[1], $parts[0], $parts[2]);
        return $ts;
    }
}