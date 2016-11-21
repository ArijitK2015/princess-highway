<?php
/**
SELECT `main_table`.* FROM `sales_flat_shipment` AS `main_table` WHERE (created_at >= '2014-09-23 14:00:00' AND created_at <= '2014-09-23 13:59:59')
 */

class FactoryX_ShippedFrom_Model_Observer { // extends Mage_Core_Model_Abstract {

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addShipmentNotes(Varien_Event_Observer $observer) {
        if (Mage::helper('shippedfrom')->addShippedFromToNotes()) {
            $storeId = $observer->getShipment()->getShippedFrom();
            $store = Mage::helper('shippedfrom')->getStore($storeId);
            $note = sprintf("Shipped From: %s", $store->getTitle());
            $this->_addToNotes($observer, $note);
        }
        if (Mage::helper('shippedfrom')->addShippedByToNotes()) {
            $note = sprintf("Shipped By: %s", $observer->getShipment()->getShippedBy());
            $this->_addToNotes($observer, $note);
        }
        return $this;
    }


    /**
     * _addToNotes
     *
     * @param  Varien_Event_Observer $observer observer object
     *
     * @param $note
     * @return FireGento_Pdf_Model_Observer
     */
    private function _addToNotes(Varien_Event_Observer $observer, $note) {
        $shipment = $observer->getShipment();
        if (empty($shipment)) {
            return $this;
        }
        //Mage::helper('shippedfrom')->log(sprintf("%s->note=%s", __METHOD__, $note) );
        $result = $observer->getResult();
        $notes = $result->getNotes();
        $notes[] = Mage::helper('shippedfrom')->__($note);
        $result->setNotes($notes);
        return $this;
    }

    /**
     * salesOrderShipmentSaveAfter
     *
     * sends packing slip to assigned store
     *
     * if you combine methods: $shipment->setEmailSent(true) and $shipment->sendEmail()
     * in _after event, it will go in recursive infinite loop sending you the millions of email.
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer) {

        if (Mage::registry('salesOrderShipmentSaveAfterTriggered')) {
            return $this;
        }

        $shipment = $observer->getEvent()->getShipment();
        $request = Mage::app()->getFrontController()->getRequest();

        if ($shipment && $this->_isValidForShipmentEmail($shipment)) {

            //Mage::helper('shippedfrom')->log(sprintf("%s->shipment->getIncrementId()=%s", __METHOD__, $shipment->getIncrementId()) );

            // only send packing slip if email not already sent
            //if (preg_match("/email/", $request->getActionName() && $shipment->getData('email_sent')) {
            //  Mage::helper('shippedfrom')->log(sprintf("email already sent [%s]", $shipment->getData('email_sent')) );
            //}
            $storeId = $shipment->getShippedFrom();
            $store = Mage::helper('shippedfrom')->getStore($storeId);
            $email = Mage::helper('shippedfrom')->getStoreEmail($store);
            Mage::helper('shippedfrom')->log(sprintf("%s->email=%s", __METHOD__, $email) );
            $email = Mage::helper('shippedfrom')->sendPackingSlipToStore($shipment, $email);
            if (is_array($email)) {
                foreach($email as $e) {
                    Mage::getSingleton('core/session')->addSuccess(sprintf("The packing slip has been sent to '%s'.", $e));
                }
            }
            else if (is_string($email)) {
                Mage::getSingleton('core/session')->addSuccess(sprintf("The packing slip has been sent to '%s'.", $email));
            }
            else {
                //Mage::helper('shippedfrom')->log(sprintf("email=%s", get_class($email)) );
                Mage::getSingleton('core/session')->addError(sprintf("Failed to send packing slip!"));
            }
            Mage::register('salesOrderShipmentSaveAfterTriggered', true);
        }
    }

    /**
     * salesOrderShipmentSaveBefore
     *
     * sets shipped from and shipped by
     *
     * If you combine methods: $shipment->setEmailSent(true) and $shipment->sendEmail() in _before event
     * you will get shipment email but without shipment number
     *
     * @param $observer
     * @return $this
     */
    public function salesOrderShipmentSaveBefore(Varien_Event_Observer $observer) {

        //Mage::helper('shippedfrom')->log(sprintf("%s->observer=%s", __METHOD__, get_class($observer)) );
        //Mage::helper('shippedfrom')->log(sprintf("action=%s", $request->getActionName()) );
        //Mage::helper('shippedfrom')->log(sprintf("email_sent=%s", $shipment->getData('email_sent')) );

        if (Mage::registry('salesOrderShipmentSaveBeforeTriggered')) {
            return $this;
        }

        $shipment = $observer->getEvent()->getShipment();
        $request = Mage::app()->getFrontController()->getRequest();

        if ($shipment && $this->_isValidForShipmentEmail($shipment)) {

            $order = $shipment->getOrder();
            $method = $order->getShippingMethod();
            //Mage::helper('shippedfrom')->log(sprintf("%s->method=%s|order=%s", __METHOD__, $method, $order->getEntityId()) );

            // if these are blank then populated with default (e.g. from temando)
            if (!$shipment->getShippedFrom() ) {
                /*
                // Note. this needs to hook into the temando shipment save event
                if (preg_match("/temando/i", $method) ) {
                    $temandoShipment = Mage::getModel('temando/shipment')->getCollection()->loadByOrderId($order->getEntityId());
                    $warehouseId = $temandoShipment->getData('warehouse_id');
                    $origin = Mage::getModel('temando/warehouse')->load($warehouseId);
                    $shippedFrom = $origin->getName();
                    Mage::helper('shippedfrom')->log(sprintf("%s->TEMANDO: shippedFrom=%s", __METHOD__, $shippedFrom) );
                }
                else {
                */
                $shippedFrom = Mage::helper('shippedfrom')->getDefaultShippedFrom();
                //Mage::helper('shippedfrom')->log(sprintf("%s->DEF: shippedFrom=%s", __METHOD__, $shippedFrom) );
                $shipment->setShippedFrom($shippedFrom);
            }

            if (!$shipment->getShippedBy() ) {
                $shippedBy = Mage::helper('shippedfrom')->getDefaultShippedBy();
                //Mage::helper('shippedfrom')->log(sprintf("%s->DEF: shippedBy=%s", __METHOD__, $shippedBy) );
                $shipment->setShippedBy($shippedBy);
            }

            $shipment->setEmailSent(true);
            Mage::register('salesOrderShipmentSaveBeforeTriggered', true);
        }
        return $this;
    }

    /**
     * beforeBlockToHtml
     *
     * shippedfrom_add_column_to_grid
     *
     * @param Varien_Event_Observer $observer
     * @internal param $ (type) (name) about this param
     */
    public function beforeBlockToHtml(Varien_Event_Observer $observer) {
        $grid = $observer->getBlock();

        // Mage_Adminhtml_Block_Sales_Shipment_Grid
        if (
            $grid instanceof Mage_Adminhtml_Block_Sales_Shipment_Grid
            ||
            $grid instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments
        ) {
            $afterClumnCode = 'shipping_name';
            if (Mage::getStoreConfigFlag('shippedfrom/default_values/shipped_from_show_in_gird')) {
                $grid->addColumnAfter(
                    'shipped_from', // column_code
                    array(
                        'header'    => Mage::helper('shippedfrom')->__('Shipped From'),
                        'index'     => 'shipped_from',
                        'filter_index' => 'sfs.shipped_from',
                        'type'      => 'options',
                        'options'   => Mage::helper('shippedfrom')->getStores(false, true),
                        'filter_condition_callback' => array($this, '_filterShippedFromConditionCallback')
                    ),
                    $afterClumnCode
                );
                $afterClumnCode = 'shipped_from';
            }
            if (Mage::getStoreConfigFlag('shippedfrom/default_values/shipped_by_show_in_gird')) {
                $grid->addColumnAfter(
                    'shipped_by',
                    array(
                        'header'    => Mage::helper('shippedfrom')->__('Shipped By'),
                        'index'     => 'shipped_by',
                        'filter_index' => 'sfs.shipped_by',
                        'type'      => 'options',
                        'options'   => Mage::helper('shippedfrom')->getUsers(),
                        'filter_condition_callback' => array($this, '_filterShippedByConditionCallback')
                    ),
                    $afterClumnCode
                );
            }
        }
    }

    /**
     * Callback filter to handle NULL values
     *
     * @param Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this $collection
     */
    public static function _filterShippedFromConditionCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue())
        {
            return;
        }
        if (empty($value) || preg_match("/null/i", $value))
        {
            $collection->getSelect()->where("main_table.shipped_from IS NULL");
        }
        else
        {
            $collection->getSelect()->where(sprintf("main_table.shipped_from = '%s'", $value));
        }
    }

    /**
     * Callback filter to handle NULL values
     *
     * @param Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return $this $collection
     */
    public static function _filterShippedByConditionCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue())
        {
            return;
        }
        if (empty($value) || preg_match("/null/i", $value))
        {
            $collection->getSelect()->where("main_table.shipped_by IS NULL");
        }
        else
        {
            $collection->getSelect()->where(sprintf("main_table.shipped_by = '%s'", $value));
        }
    }

    /**
     * beforeCollectionLoad
     *
     *
     * @param Varien_Event_Observer $observer
     * @internal param $ (type) (name) about this param
     */
    public function beforeCollectionLoad(Varien_Event_Observer $observer) {
        //Mage::helper('shippedfrom')->log(sprintf("%s", __METHOD__) );
        $collection = $observer->getCollection();
        if (!isset($collection)) {
            return;
        }
        //Mage::helper('shippedfrom')->log(sprintf("%s->collection=%s", __METHOD__, get_class($collection)) );
        if ($collection instanceof Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection) {
            $collection->getSelect()->joinLeft(
                array('sfs' => 'sales_flat_shipment'),
                'main_table.entity_id=sfs.entity_id',
                array(
                    'sfs.shipped_from',
                    'sfs.shipped_by'
                )
            );

            // Fix the "Column in where clause is ambiguous" error
            if ($where = $collection->getSelect()->getPart('where'))
            {;
                $ambiguousColumns = array('order_id');
                $where = $this->_fixAmbiguousColumns($where, $ambiguousColumns);
                //Mage::log(sprintf("%s->where=%s\n", __METHOD__, print_r($where,true)));
                $collection->getSelect()->setPart('where', $where);
            }
        }
    }

    /**
     * Fix the "Column in where clause is ambiguous" error
     * @param $where
     * @param array $ambiguousColumns
     * @return
     */
    protected function _fixAmbiguousColumns($where, $ambiguousColumns = array())
    {
        // Avoid sql error: column 'COLUMN_NAME' in where clause is ambiguous
        foreach ($where as $key => $condition)
        {
            //Mage::log(sprintf("%s->key=%s,condition=%s\n", __METHOD__, $key, $condition));
            foreach ($ambiguousColumns as $column)
            {
                $old = "";
                $new = "";
                // test with backticks (only used in MySQL) = quoted identifiers
                if (preg_match(sprintf("/\(`%s`/", $column), $condition))
                {
                    $old = sprintf("`%s`", $column);
                    $new = sprintf("`main_table`.`%s`", $column);
                }
                elseif (preg_match(sprintf("/\(%s/", $column), $condition))
                {
                    $old = sprintf("%s", $column);
                    $new = sprintf("main_table.%s", $column);
                }
                if (strlen($old) && strlen($new))
                {
                    $new_condition = str_replace($old, $new, $condition);
                    $where[$key] = $new_condition;
                }
            }
        }
        return $where;
    }

    /**
     * _isValidForShipmentEmail
     *
     * send shipment email only when carrier tracking info is added
     * @param $shipment
     * @return bool
     */
    protected function _isValidForShipmentEmail($shipment) {
        $trackingNumbers = array();
        foreach ($shipment->getAllTracks() as $track) {
            $trackingNumbers[] = $track->getNumber();
        }
        if (count($trackingNumbers) > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
    check the report has been sent

    magento cron skips 5 minutes intervals
    this checks the last run time every
     */
    public function storeSalesReportCheck($observer = null, $test = 0) {
        Mage::helper('shippedfrom')->log(sprintf("%s->check", __METHOD__) );



    }

    /**
     * report
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @param int $test
     * @throws Exception
     * @return FactoryX_ShippedFrom_Model_Observer
     */
    public function storeSalesReport(Mage_Cron_Model_Schedule $schedule = null, $test = 0) {

        if (!Mage::getStoreConfigFlag('shippedfrom/cron_job_store_sales_report/enabled') && !$test) {
            $msg = sprintf("%s->cron disabled!", __METHOD__);
            Mage::helper('shippedfrom')->log($msg);
            return false;
        }

        if ($test) {
            $currModule = Mage::app()->getRequest()->getModuleName();
            $msg = sprintf("Running %s report cron...", $currModule);
            Mage::helper('shippedfrom')->log($msg);
            if (Mage::getSingleton('adminhtml/session')) {
                Mage::getSingleton('adminhtml/session')->addSuccess($msg);
            }
        }


        // get days of the week to run
        $sendOn = Mage::getStoreConfig('shippedfrom/cron_job_store_sales_report/send_on');
        $weekDaysToSendOn = array();
        if ($sendOn) {
            $weekDaysToSendOn = explode(",", $sendOn);
        }

        // validate weekDaysToSendOn
        if (!$weekDaysToSendOn || !is_array($weekDaysToSendOn) || !count($weekDaysToSendOn) || !array_product(array_map('is_numeric', $weekDaysToSendOn)) ) {
            $msg = sprintf("No days to run set");
            Mage::getSingleton('adminhtml/session')->addError($msg);
            Mage::helper('shippedfrom')->log($msg);
            return false;
        }

        // run today
        $today = date('N'); // 1 (for Monday) through 7 (for Sunday)
        if (!in_array($today, $weekDaysToSendOn)) {
            $msg = sprintf("Wont run on %d, cron set to send on %s", $today, print_r($weekDaysToSendOn, true));
            Mage::getSingleton('adminhtml/session')->addError($msg);
            Mage::helper('shippedfrom')->log($msg);
            return false;
        }

        // shipment date
        $shipmentDate = null;

        // get date if one exists
        // getCronJobShipmentDate was never written ???
        //$shipmentDate = Mage::helper('shippedfrom/report')->getCronJobShipmentDate();
        $shipmentDate = date("d/m/Y", time());
        if ($test) {
            $shipmentDate = Mage::helper('shippedfrom/report')->getTestShipmentDate();
        }

        if ($shipmentDate) {
            Mage::helper('shippedfrom/report')->log(sprintf("use date: %s", $shipmentDate));
            $range = Mage::helper('shippedfrom/report')->getDateRange($shipmentDate);
        }
        else {
            // get date ranges
            $range = Mage::helper('shippedfrom/report')->getNextDateRange();
        }

        $tsFrom = $range['from'];
        $tsTo = $range['to'];

        $fromDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $tsFrom);
        $toDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $tsTo);
        Mage::helper('shippedfrom')->log(sprintf("%s->from: %s, %s", __METHOD__, $fromDate, date("Y-m-d H:i:s", $tsFrom)) );
        Mage::helper('shippedfrom')->log(sprintf("%s->to  : %s, %s", __METHOD__, $toDate, date("Y-m-d H:i:s", $tsTo)) );

        //$report = Mage::helper('shippedfrom/report')->generateReport($fromDate, $toDate);
        $report = Mage::helper('shippedfrom/report')->generateReport($range);

        $msg = sprintf("Subject: %s", $report["subject"]);
        Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        Mage::helper('shippedfrom')->log($msg);

        Mage::helper('shippedfrom/report')->emailReport($report["body"], $report["subject"], null);
        return true;
    }

}