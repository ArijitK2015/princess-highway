<?php

/**
 * Class FactoryX_ShippedFrom_Model_Cron
 */
class FactoryX_ShippedFrom_Model_Cron
{
    const XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE     = 'shippedfrom/auspost_log/error_email_template';
    const XML_PATH_EMAIL_LOG_CLEAN_IDENTITY     = 'shippedfrom/auspost_log/error_email_identity';
    const XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT    = 'shippedfrom/auspost_log/error_email';
    const XML_PATH_LOG_CLEAN_ENABLED            = 'shippedfrom/auspost_log/enabled';

    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors = array();

    /**
     * Cron logger
     *
     * @var FactoryX_ShippedFrom_Model_Cron_Log
     */
    protected $_cronLogger;

    /**
     * @param Mage_Cron_Model_Schedule|null $schedule
     * @return $this
     */
    public function processManifests(Mage_Cron_Model_Schedule $schedule = null)
    {
        if (!$this->hlpAuspost()->isEnabled()) {
            return $this;
        }

        $this->initCronLogger($schedule);
        $this->getCronLogger()->addMessage($this->hlp()->__('Starting process manifests cron'));

        /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
            ->addFieldToFilter('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_SENT);

        $groupedShipments = array();

        $this->getCronLogger()
            ->addMessage(
                $this->hlp()->__('%s queue entries have been found', $collection->getSize())
            );

        /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry */
        foreach ($collection as $queueEntry) {
            $shippedFrom = $queueEntry->getShippedFrom();
            $createdAtDate = substr($queueEntry->getCreatedAt(), 0, 10);
            if (array_key_exists($shippedFrom, $shippedFromArray)) {
                if (!array_key_exists($createdAtDate, $shippedFromArray[$shippedFrom])) {
                    $shippedFromArray[$shippedFrom][$createdAtDate][] = $queueEntry->getApShipmentId();
                }
            } else {
                $shippedFromArray[$shippedFrom][$createdAtDate][] = $queueEntry->getApShipmentId();
            }
        }

        /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Orders $ordersFactory */
        $ordersFactory = Mage::getModel('shippedfrom/auspost_shipping_orders');

        foreach ($shippedFromArray as $shippedFrom => $dates) {
            foreach ($dates as $date => $shipments) {
                $groupedShipments = array();
                foreach ($shipments as $shipmentId) {
                    $groupedShipments[$shippedFrom][] = array(
                        'shipment_id'   => $shipmentId
                    );
                }

                $ordersFactory->createAuspostOrder($groupedShipments, $this->getCronLogger());
            }
        }

        $this->getCronLogger()->addMessage($this->hlp()->__('End process manifests cron'));
        $this->getCronLogger()->save();

        return $this;
    }

    /**
     * @param Mage_Cron_Model_Schedule|null $schedule
     * @return $this
     */
    public function processPendingLabels(Mage_Cron_Model_Schedule $schedule = null)
    {
        if (!$this->hlpAuspost()->isEnabled()) {
            return $this;
        }

        $this->initCronLogger($schedule);
        $this->getCronLogger()->addMessage($this->hlp()->__('Starting process pending labels cron'));

        /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
            ->addFieldToFilter('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_INITIALIZED);

        $this->getCronLogger()
            ->addMessage(
                $this->hlp()->__('%s queue entries have been found', $collection->getSize())
            );

        /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry */
        foreach ($collection as $queueEntry) {
            /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Labels $labelsFactory */
            $labelsFactory = Mage::getModel('shippedfrom/auspost_shipping_labels');
            $labelsFactory->getAuspostLabel($queueEntry, $this->getCronLogger());
        }

        $this->getCronLogger()->addMessage($this->hlp()->__('End process pending labels cron'));
        $this->getCronLogger()->save();

        return $this;
    }

    /**
     * @param Mage_Cron_Model_Schedule|null $schedule
     * @return $this
     */
    public function processPendingShippingQueue(Mage_Cron_Model_Schedule $schedule = null)
    {
        if (!$this->hlpAuspost()->isEnabled()) {
            return $this;
        }

        $this->initCronLogger($schedule);
        $this->getCronLogger()->addMessage($this->hlp()->__('Starting process pending shipping queue cron'));

        /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
        $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
            ->addFieldToFilter('status', array('nin'    =>  $this->getNotPendingStatuses()));

        $this->getCronLogger()
            ->addMessage(
                $this->hlp()->__('%s queue entries have been found', $collection->getSize())
            );

        /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry */
        foreach ($collection as $queueEntry) {
            $this->getCronLogger()
                ->addMessage(
                    $this->hlp()->__('Start processing queue entry %s', $queueEntry->getEntityId())
                );
            $this->getCronLogger()
                ->addMessage(
                    $this->hlp()->__('Status found: %s', $queueEntry->getStatus())
                );
            if ($queueEntry->getStatus() == FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_INITIALIZED) {
                /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments $shipmentsFactory */
                $shipmentsFactory = Mage::getModel('shippedfrom/auspost_shipping_shipments');
                $shipmentsFactory->createAuspostShipment($queueEntry, $this->getCronLogger());
            }

            if ($queueEntry->getStatus() == FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_SHIPPED) {
                /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Labels $labelsFactory */
                $labelsFactory = Mage::getModel('shippedfrom/auspost_shipping_labels');
                $labelsFactory->initAuspostLabel($queueEntry, $this->getCronLogger());
            }

            if ($queueEntry->getStatus() == FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_CREATED) {
                $this->hlp()->notifyStore($queueEntry);
            }
        }

        $this->getCronLogger()->addMessage($this->hlp()->__('End process pending shipping queue cron'));
        $this->getCronLogger()->save();

        return $this;
    }

    /**
     * Clean logs
     *
     * @return FactoryX_ShippedFrom_Model_Cron
     */
    public function logClean()
    {
        if (!Mage::getStoreConfigFlag(self::XML_PATH_LOG_CLEAN_ENABLED)) {
            return $this;
        }

        $this->_errors = array();

        try {
            Mage::getModel('shippedfrom/cron_log')->clean();
        }
        catch (Exception $e) {
            $this->_errors[] = $e->getMessage();
            $this->_errors[] = $e->getTrace();
        }

        $this->_sendLogCleanEmail();

        return $this;
    }

    /**
     * Send Log Clean Warnings
     *
     * @return Mage_Log_Model_Cron
     */
    protected function _sendLogCleanEmail()
    {
        if (!$this->_errors) {
            return $this;
        }
        if (!Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT)) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $emailTemplate = Mage::getModel('core/email_template');
        /* @var $emailTemplate Mage_Core_Model_Email_Template */
        $emailTemplate->setDesignConfig(array('area' => 'backend'))
            ->sendTransactional(
                Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_TEMPLATE),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_IDENTITY),
                Mage::getStoreConfig(self::XML_PATH_EMAIL_LOG_CLEAN_RECIPIENT),
                null,
                array('warnings' => join("\n", $this->_errors))
            );

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * @param Mage_Cron_Model_Schedule|null $schedule
     */
    protected function initCronLogger(Mage_Cron_Model_Schedule $schedule = null)
    {
        $this->_cronLogger = Mage::getModel('shippedfrom/cron_log')
            ->setCronName(($schedule === null) ? "button triggerred manually" : $schedule->getJobCode())
            ->setCreatedAt(($schedule === null) ? strftime('%Y-%m-%d %H:%M:00', time()) : $schedule->getExecutedAt());
    }

    /**
     *
     */
    protected function getCronLogger()
    {
        return $this->_cronLogger;
    }

    /**
     * @return array
     */
    protected function getNotPendingStatuses()
    {
        return array(
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_COMPLETE,
            FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_INITIALIZED
        );
    }

    /**
     * @return FactoryX_ShippedFrom_Helper_Data
     */
    protected function hlp()
    {
        return Mage::helper('shippedfrom');
    }

    /**
     * @return FactoryX_ShippedFrom_Helper_Auspost
     */
    protected function hlpAuspost()
    {
        return Mage::helper('shippedfrom/auspost');
    }
}