<?php

/**
 * Class FactoryX_ShippedFrom_Model_Observer
 */
class FactoryX_ShippedFrom_Model_Observer
{

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addShipmentNotes(Varien_Event_Observer $observer)
    {
        if ($this->_hlp()->addShippedFromToNotes()) {
            $storeId = $observer->getShipment()->getShippedFrom();
            $store = $this->_hlp()->getStore($storeId);
            $note = sprintf("Shipped From: %s", $store->getTitle());
            $this->_addToNotes($observer, $note);
        }

        if ($this->_hlp()->addShippedByToNotes()) {
            $note = sprintf("Shipped By: %s", $observer->getShipment()->getShippedBy());
            $this->_addToNotes($observer, $note);
        }

        return $this;
    }


    /**
     * @param  Varien_Event_Observer $observer observer object
     * @param $note
     * @return $this
     */
    protected function _addToNotes(Varien_Event_Observer $observer, $note)
    {
        $shipment = $observer->getShipment();
        if (empty($shipment)) {
            return $this;
        }

        $result = $observer->getResult();
        $notes = $result->getNotes();
        $notes[] = $this->_hlp()->__($note);
        $result->setNotes($notes);
        return $this;
    }

    /**
     * sends packing slip to assigned store
     *
     * if you combine methods: $shipment->setEmailSent(true) and $shipment->sendEmail()
     * in _after event, it will go in recursive infinite loop sending you the millions of email.
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function salesOrderShipmentSaveAfter(Varien_Event_Observer $observer)
    {
        if (Mage::registry('salesOrderShipmentSaveAfterTriggered')) {
            Mage::helper('shippedfrom')->log(sprintf("%s->salesOrderShipmentSaveAfterTriggered already", __METHOD__));
            return $this;
        }

        $shipment = $observer->getEvent()->getShipment();

        $productIds = $this->_hlpAuspost()->getProductIdFromShippingMethod($shipment);
        Mage::helper('shippedfrom')->log(sprintf("%s->productIds=%s", __METHOD__, print_r($productIds, true)));
        if (empty($productIds)) {
            $shippedFrom = "Unknown Location";
            if ($storeId = $shipment->getShippedFrom()) {
                $shippedFrom = Mage::getModel('ustorelocator/location')->load($storeId)->getTitle();
            }

            Mage::throwException(
                Mage::helper('shippedfrom')->__(
                    "No Australia Post products found! Please check the configuration for '%s'.",
                    $shippedFrom
                )
            );
        }

        if ($productIds && !$this->queueEntryAlreadyExists($shipment)) {
            /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry */
            $queueEntry = Mage::getModel('shippedfrom/shipping_queue');
            $queueEntry->initQueue($shipment);
        }

        /*
        sales_order_shipment_save_after is also triggered when "Send Tracking Information" is clicked, so avoid this
        */
        if (preg_match("/\/sales_order_shipment\/email\/shipment_id\//i", Mage::helper('core/url')->getCurrentUrl())) {
            Mage::helper('shippedfrom')->log(sprintf("%s->DONT SEND!!!", __METHOD__));
        } else {
            if ($shipment && $this->_isValidForShipmentEmail($shipment)) {
                $storeId = $shipment->getShippedFrom();
                $store = $this->_hlp()->getStore($storeId);
                $email = $this->_hlp()->getStoreEmail($store);
                $email = $this->_hlp()->sendPackingSlipToStore($shipment, $email);
                if (is_array($email)) {
                    foreach ($email as $e) {
                        Mage::getSingleton('core/session')->addSuccess(
                            sprintf("The packing slip has been sent to '%s'.", $e)
                        );
                    }
                } elseif (is_string($email)) {
                    Mage::getSingleton('core/session')->addSuccess(
                        sprintf("The packing slip has been sent to '%s'.", $email)
                    );
                } else {
                    Mage::getSingleton('core/session')->addError(sprintf("Failed to send packing slip!"));
                }
            }
        }

        Mage::register('salesOrderShipmentSaveAfterTriggered', true);
        return $this;
    }

    /**
     * sets shipped from and shipped by
     *
     * If you combine methods: $shipment->setEmailSent(true) and $shipment->sendEmail() in _before event
     * you will get shipment email but without shipment number
     *
     * @param $observer
     * @return $this
     */
    public function salesOrderShipmentSaveBefore(Varien_Event_Observer $observer)
    {
        if (Mage::registry('salesOrderShipmentSaveBeforeTriggered')) {
            return $this;
        }

        $shipment = $observer->getEvent()->getShipment();
                
        if ($shipment && $this->_isValidForShipmentEmail($shipment)) {
            // if these are blank then populated with default (e.g. from temando)
            if (!$shipment->getShippedFrom()) {
                $shippedFrom = $this->_hlp()->getDefaultShippedFrom();
                $shipment->setShippedFrom($shippedFrom);
            }

            if (!$shipment->getShippedBy()) {
                $shippedBy = $this->_hlp()->getDefaultShippedBy();
                $shipment->setShippedBy($shippedBy);
            }

            $shipment->setEmailSent(true);
            Mage::register('salesOrderShipmentSaveBeforeTriggered', true);
        }

        $productIds = $this->_hlpAuspost()->getProductIdFromShippingMethod($shipment);
        Mage::helper('shippedfrom')->log(sprintf("%s->productIds=%s", __METHOD__, print_r($productIds, true)));

        if (is_array($productIds)) {
            Mage::throwException(
                Mage::helper('shippedfrom')->__(
                    "More than one AusPost products '%s' have been found matching this store location and this shipping method",
                    implode(",", $productIds)
                )
            );
        } elseif ($productIds === 0) {
            Mage::throwException(
                Mage::helper('shippedfrom')->__(
                    'No AusPost product have been found matching this store location and this shipping method'
                )
            );
        } else {
            /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry */
            $potentialQueueEntry = Mage::getModel('shippedfrom/shipping_queue');
            $potentialQueueEntry = $potentialQueueEntry->initQueue($shipment, false);

            /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments $auspostShipment */
            $auspostShipment = Mage::getModel('shippedfrom/auspost_shipping_shipments');
            $shipmentData = $auspostShipment->generateAuspostShipmentData($shipment);

            /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Address $addressValidator */
            $addressValidator = Mage::getModel('shippedfrom/auspost_shipping_address');

            foreach ($shipmentData['shipments'] as $si => $aShipment) {
                $addressValidator->validateShipmentAddressData($potentialQueueEntry, $aShipment);
            }
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function beforeBlockToHtml(Varien_Event_Observer $observer)
    {
        $grid = $observer->getBlock();
        // Mage_Adminhtml_Block_Sales_Shipment_Grid
        if ($grid instanceof Mage_Adminhtml_Block_Sales_Shipment_Grid
            || $grid instanceof Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments) {
            $afterClumnCode = 'shipping_name';
            if (Mage::getStoreConfigFlag('shippedfrom/default_values/shipped_from_show_in_gird')) {
                $grid->addColumnAfter(
                    'shipped_from', // column_code
                    array(
                        'header'    => $this->_hlp()->__('Shipped From'),
                        'index'     => 'shipped_from',
                        'filter_index' => 'sfs.shipped_from',
                        'type'      => 'options',
                        'options'   => $this->_hlp()->getStores(false, true),
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
                        'header'    => $this->_hlp()->__('Shipped By'),
                        'index'     => 'shipped_by',
                        'filter_index' => 'sfs.shipped_by',
                        'type'      => 'options',
                        'options'   => $this->_hlp()->getUsers(),
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
     */
    public static function _filterShippedFromConditionCallback($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            if (empty($value) || preg_match("/null/i", $value)) {
                $collection->getSelect()->where("main_table.shipped_from IS NULL");
            } else {
                $collection->getSelect()->where(sprintf("main_table.shipped_from = '%s'", $value));
            }
        }
    }

    /**
     * Callback filter to handle NULL values
     *
     * @param Mage_Sales_Model_Resource_Order_Shipment_Grid_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     */
    public static function _filterShippedByConditionCallback($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            if (empty($value) || preg_match("/null/i", $value)) {
                $collection->getSelect()->where("main_table.shipped_by IS NULL");
            } else {
                $collection->getSelect()->where(sprintf("main_table.shipped_by = '%s'", $value));
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function beforeCollectionLoad(Varien_Event_Observer $observer)
    {
        $collection = $observer->getCollection();
        if (!isset($collection)) {
            return;
        }

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
            if ($where = $collection->getSelect()->getPart('where')) {
                $ambiguousColumns = array('order_id');
                $where = $this->_fixAmbiguousColumns($where, $ambiguousColumns);
                $collection->getSelect()->setPart('where', $where);
            }
        }
    }

    /**
     * Fix the "Column in where clause is ambiguous" error
     * @param $where
     * @param array $ambiguousColumns
     * @return mixed
     */
    protected function _fixAmbiguousColumns($where, $ambiguousColumns = array())
    {
        // Avoid sql error: column 'COLUMN_NAME' in where clause is ambiguous
        foreach ($where as $key => $condition) {
            foreach ($ambiguousColumns as $column) {
                $old = "";
                $new = "";
                // test with backticks (only used in MySQL) = quoted identifiers
                if (preg_match(sprintf("/\(`%s`/", $column), $condition)) {
                    $old = sprintf("`%s`", $column);
                    $new = sprintf("`main_table`.`%s`", $column);
                } elseif (preg_match(sprintf("/\(%s/", $column), $condition)) {
                    $old = sprintf("%s", $column);
                    $new = sprintf("main_table.%s", $column);
                }

                if ($old !== "" && $new !== "") {
                    $newCondition = str_replace($old, $new, $condition);
                    $where[$key] = $newCondition;
                }
            }
        }

        return $where;
    }

    /**
     * send shipment email only when carrier tracking info is added
     * @param $shipment
     * @return bool
     */
    protected function _isValidForShipmentEmail($shipment)
    {
        $trackingNumbers = array();
        foreach ($shipment->getAllTracks() as $track) {
            $trackingNumbers[] = $track->getNumber();
        }

        return (count($trackingNumbers) ? true : false);
    }

    /**
     * @param Mage_Cron_Model_Schedule $schedule
     * @param int $test
     * @throws Exception
     * @return boolean
     */
    public function storeSalesReport(Mage_Cron_Model_Schedule $schedule = null, $test = 0)
    {
        if (!Mage::getStoreConfigFlag('shippedfrom/cron_job_store_sales_report/enabled') && !$test) {
            $msg = sprintf("%s->cron disabled!", __METHOD__);
            Mage::getSingleton('adminhtml/session')->addError($msg);
            return false;
        }

        if ($test) {
            $currModule = Mage::app()->getRequest()->getModuleName();
            $msg = sprintf("Running %s report cron...", $currModule);
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
        if (!$weekDaysToSendOn
            || !is_array($weekDaysToSendOn)
            || !empty($weekDaysToSendOn)
            || !array_product(array_map('is_numeric', $weekDaysToSendOn))) {
            $msg = sprintf("No days to run set");
            Mage::getSingleton('adminhtml/session')->addError($msg);
            return false;
        }

        // run today 1 (for Monday) through 7 (for Sunday)
        $today = date('N');
        if (!in_array($today, $weekDaysToSendOn)) {
            $msg = sprintf("Wont run on %d, cron set to send on %s", $today, print_r($weekDaysToSendOn, true));
            Mage::getSingleton('adminhtml/session')->addError($msg);
            return false;
        }

        // shipment date
        $shipmentDate = null;

        // get date if one exists
        // getCronJobShipmentDate was never written ???
        //$shipmentDate = Mage::helper('shippedfrom/report')->getCronJobShipmentDate();
        $shipmentDate = ($test ? Mage::helper('shippedfrom/report')->getTestShipmentDate() : date("d/m/Y", time()));
        $range = ($shipmentDate
            ? Mage::helper('shippedfrom/report')->getDateRange($shipmentDate)
            : Mage::helper('shippedfrom/report')->getNextDateRange()
        );

        $tsFrom = $range['from'];
        $tsTo = $range['to'];

        $fromDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $tsFrom);
        $toDate = Mage::getModel('core/date')->gmtDate('Y-m-d H:i:s', $tsTo);

        //$report = Mage::helper('shippedfrom/report')->generateReport($fromDate, $toDate);
        $report = Mage::helper('shippedfrom/report')->generateReport($range);

        $msg = sprintf("Subject: %s", $report["subject"]);
        Mage::getSingleton('adminhtml/session')->addSuccess($msg);

        Mage::helper('shippedfrom/report')->emailReport($report["body"], $report["subject"], null);
        return true;
    }

    /**
     * @return FactoryX_ShippedFrom_Helper_Data
     */
    protected function _hlp()
    {
        return Mage::helper('shippedfrom');
    }

    /**
     * @return FactoryX_ShippedFrom_Helper_Auspost
     */
    protected function _hlpAuspost()
    {
        return Mage::helper('shippedfrom/auspost');
    }

    /**
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return int
     */
    protected function queueEntryAlreadyExists(Mage_Sales_Model_Order_Shipment $shipment)
    {
        /** @var Mage_Sales_Model_Resource_Order_Shipment_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
            ->addFieldToFilter('shipment_id', $shipment->getData('next_shipment_id'));

        // try id
        if ($collection->getSize() == 0) {
            $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                ->addFieldToFilter('shipment_id', $shipment->getId());
        }

        // try increment_id
        if ($collection->getSize() == 0) {
            $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                ->addFieldToFilter('shipment_id', $shipment->getIncrementId());
        }

        return $collection->getSize();
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function addExtraTrackingInfo(Varien_Event_Observer $observer)
    {
        $trackingInfo = $observer->getEvent()->getResult();
        $trackings = $trackingInfo->getAllTrackings();
        $trackingInfo->reset();
        /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Track $trackRepository */
        $trackRepository = Mage::getModel('shippedfrom/auspost_shipping_track');
        foreach($trackings as $tracking) {
            $trackingId = $tracking->getTracking();
            $extraInfo = $trackRepository->track($trackingId);
            if (is_array($extraInfo)) {
                if (array_key_exists('errors', $extraInfo['tracking_results'][0])) {
                    $status = $extraInfo['tracking_results'][0]['errors'][0]['message'];
                } else {
                    $status = array_key_exists('status', $extraInfo['tracking_results'][0]) ? $extraInfo['tracking_results'][0]['status'] : "Unknown";
                    if (array_key_exists('trackable_items', $extraInfo['tracking_results'][0])) {
                        $progressDetails = array();
                        foreach ($extraInfo['tracking_results'][0]['trackable_items'][0]['events'] as $trackEvent) {
                            $progressDetails[] = array(
                                'activity'          => array_key_exists('description', $trackEvent) ? $trackEvent['description'] : "",
                                'deliverylocation'  => array_key_exists('location', $trackEvent) ? $trackEvent['location'] : "",
                                'deliverydate'      => substr($trackEvent['date'], 0, 10),
                                'deliverytime'      => substr($trackEvent['date'], 10, 8)
                            );
                        }
                        $tracking->setProgressdetail($progressDetails);
                    }
                }
                $tracking->setStatus($status);
            }
            $trackingInfo->append($tracking);
        }
    }

}