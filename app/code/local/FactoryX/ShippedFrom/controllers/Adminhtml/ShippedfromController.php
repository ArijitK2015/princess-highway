<?php

/**
 * Class FactoryX_ShippedFrom_Adminhtml_ShippedfromController
 */
class FactoryX_ShippedFrom_Adminhtml_ShippedfromController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/auspost/shipments');
    }

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('factoryx_menu/auspost/shipments');

        return $this;
    }

    /**
     *
     */
    public function indexAction()
    {
        $this->_forward('auspost');
    }

    /**
     *
     */
    public function auspostAction()
    {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('shippedfrom/adminhtml_auspost'));
        $this->renderLayout();
    }

    /**
     *
     */
    public function processPendingAction()
    {
        Mage::getModel('shippedfrom/cron')->processPendingShippingQueue();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shippedfrom')->__('Pending Shipping Queue has been processed'));
        $this->_redirect('*/*/');
    }

    /**
     *
     */
    public function processPendingLabelsAction()
    {
        Mage::getModel('shippedfrom/cron')->processPendingLabels();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shippedfrom')->__('Pending Labels have been processed'));
        $this->_redirect('*/*/');
    }

    /**
     *
     */
    public function processManifestsAction()
    {
        Mage::getModel('shippedfrom/cron')->processManifests();
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('shippedfrom')->__('Manifests have been processed'));
        $this->_redirect('*/*/');
    }

    /**
     *
     */
    public function deleteAction()
    {
        $scheduleId  = (int) $this->getRequest()->getParam('schedule_id');
        if ($scheduleId) {

            try {
                /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $schedule */
                $schedule = Mage::getModel('shippedfrom/shipping_queue');
                $schedule->load($scheduleId);

                /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments $shipmentRepository */
                $shipmentRepository = Mage::getModel('shippedfrom/auspost_shipping_shipments');
                $shipmentRepository->deleteShipment($schedule);

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('shippedfrom')->__('The shipment has been successfully deleted')
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Schedule main view
     */
    public function viewAction()
    {
        $this->_viewAction();
    }

    /**
     * Generic schedule view action
     */
    protected function _viewAction()
    {
        try {
            $scheduleId  = (int) $this->getRequest()->getParam('schedule_id');
            /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $schedule */
            $schedule = Mage::getModel('shippedfrom/shipping_queue');
            $schedule->load($scheduleId);
            Mage::register('current_auspost_schedule', $schedule);
            $this->_title($this->__('Australia Post Schedule'))->_title($this->__('Schedule #%s', $scheduleId));
            $this->_initAction();
            /** @var FactoryX_ShippedFrom_Block_Adminhtml_Auspost_Queue_View $block **/
            $block = $this->getLayout()->createBlock('shippedfrom/adminhtml_auspost_queue_view');
            $this->_addContent($block);
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
        }
        $this->_redirect('*/*/');
    }

    /**
     *
     */
    public function printAction()
    {
        try {
            $scheduleId  = (int) $this->getRequest()->getParam('schedule_id');
            /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $schedule */
            $schedule = Mage::getModel('shippedfrom/shipping_queue');
            $schedule->load($scheduleId);
            $schedule->generateLocalPrintPdf();
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     *
     */
    public function getAction()
    {
        try {
            $scheduleId  = (int) $this->getRequest()->getParam('schedule_id');
            /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $schedule */
            $schedule = Mage::getModel('shippedfrom/shipping_queue');
            $schedule->load($scheduleId);
            $shipmentsFactory = Mage::getModel('shippedfrom/auspost_shipping_shipments');
            $data = $shipmentsFactory->getShipment($schedule);
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($data));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/');
        }
    }

    /**
     *
     */
    public function massUpdateAction()
    {
        $scheduleIds = $this->getRequest()->getParam('auspost');
        $productId  = $this->getRequest()->getParam('product_id');
        if (!is_array($scheduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Please select shipment(s)'));
        } elseif (!$productId) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Please select a new product id'));
        } else {
            try {
                /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
                $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                    ->addFieldToFilter('status', array('nin'    =>  $this->getNotPendingStatuses()))
                    ->addFieldToFilter('schedule_id', array('in' => $scheduleIds));

                $count = 0;

                foreach ($collection as $queueEntry) {
                    /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments $shipmentsFactory */
                    $shipmentsFactory = Mage::getModel('shippedfrom/auspost_shipping_shipments');
                    $shipmentsFactory->updateShipment($queueEntry, $productId);
                    $count++;
                }

                if ($count) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('shippedfrom')->__(
                            'Total of %d shipment(s) were successfully updated', $count
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Nothing was deleted!'));
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     */
    public function massDeleteAction()
    {
        $scheduleIds = $this->getRequest()->getParam('auspost');
        if (!is_array($scheduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Please select shipment(s)'));
        } else {
            try {

                /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
                $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                    ->addFieldToSelect('shipped_from')
                    ->addFieldToFilter('schedule_id', array('in' => $scheduleIds))
                    ->addFieldToFilter('status', array('neq' => FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_INITIALIZED))
                    ->distinct(true);

                $count = 0;

                if ($collection->getSize()) {
                    $shippedFromArray = $collection->getColumnValues('shipped_from');

                    foreach($shippedFromArray as $shippedFrom) {
                        /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $shipmentCollection */
                        $shipmentCollection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                            ->addFieldToSelect(array('schedule_id', 'ap_shipment_id'))
                            ->addFieldToFilter('shipped_from', $shippedFrom)
                            ->addFieldToFilter('schedule_id', array('in' => $scheduleIds));

                        if ($shipmentCollection->getSize()) {
                            $apShipmentIds = $shipmentCollection->getColumnValues('ap_shipment_id');

                            /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments $shipmentRepository */
                            $shipmentRepository = Mage::getModel('shippedfrom/auspost_shipping_shipments');
                            $shipmentRepository->massDeleteShipment($apShipmentIds, $shippedFrom, $shipmentCollection);

                            $count += $shipmentCollection->getSize();
                        }
                    }
                }
                if ($count > 0) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('shippedfrom')->__(
                            'Total of %d shipment(s) were successfully deleted', $count
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Nothing was deleted!'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     */
    public function massOrderAction()
    {
        $scheduleIds = $this->getRequest()->getParam('auspost');
        if (!is_array($scheduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Please select shipment(s)'));
        } else {
            try {

                /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
                $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                    ->addFieldToSelect(array('shipped_from', 'created_at'))
                    ->addFieldToFilter('schedule_id', array('in' => $scheduleIds))
                    ->addFieldToFilter('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_SENT)
                    ->distinct(true);

                $count = 0;

                if ($collection->getSize()) {
                    $shippedFromArray = array();
                    foreach ($collection as $queueEntry) {
                        $shippedFrom = $queueEntry->getShippedFrom();
                        $createdAtDate = substr($queueEntry->getCreatedAt(), 0, 10);
                        if (array_key_exists($shippedFrom, $shippedFromArray)) {
                            if (!array_key_exists($createdAtDate, $shippedFromArray[$shippedFrom])) {
                                $shippedFromArray[$shippedFrom][] = $createdAtDate;
                            }
                        } else {
                            $shippedFromArray[$shippedFrom][] = $createdAtDate;
                        }
                    }

                    $groupedShipments = array();

                    foreach ($shippedFromArray as $shippedFrom => $dates) {
                        foreach ($dates as $date) {
                            $startDate = $date . " 00:00:00";
                            $endDate = $date . " 23:59:59";
                            /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $shipmentCollection */
                            $shipmentCollection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                                ->addFieldToSelect(array('schedule_id', 'ap_shipment_id', 'created_at'))
                                ->addFieldToFilter('shipped_from', $shippedFrom)
                                ->addFieldToFilter('created_at', array('from'   =>  $startDate, 'to'    =>  $endDate))
                                ->addFieldToFilter('schedule_id', array('in' => $scheduleIds))
                                ->addFieldToFilter('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_SENT);

                            if ($shipmentCollection->getSize()) {
                                $apShipmentIds = $shipmentCollection->getColumnValues('ap_shipment_id');

                                foreach ($apShipmentIds as $apShipmentId) {
                                    $groupedShipments[$shippedFrom][] = array('shipment_id'    =>  $apShipmentId);
                                }

                                /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Orders $ordersFactory */
                                $ordersFactory = Mage::getModel('shippedfrom/auspost_shipping_orders');
                                $ordersFactory->createAuspostOrder($groupedShipments);

                                $count += $shipmentCollection->getSize();
                            }
                        }
                    }
                }
                if ($count > 0) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('shippedfrom')->__(
                            'Total of %d shipment(s) were successfully ordered', $count
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Nothing was ordered!'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     */
    public function massNotifyAction()
    {
        $scheduleIds = $this->getRequest()->getParam('auspost');
        if (!is_array($scheduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Please select shipment(s)'));
        } else {
            try {

                foreach ($scheduleIds as $scheduleId) {
                    /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $schedule */
                    $schedule = Mage::getModel('shippedfrom/shipping_queue');
                    $schedule->load($scheduleId);
                    $this->hlp()->notifyStore($schedule);
                }

                if (count($scheduleIds) > 0) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('shippedfrom')->__(
                            'Total of %d shipment(s) were successfully sent', count($scheduleIds)
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Nothing was sent!'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     */
    public function massGetLabelAction()
    {
        $scheduleIds = $this->getRequest()->getParam('auspost');
        if (!is_array($scheduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Please select shipment(s)'));
        } else {
            try {

                /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
                $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                    ->addFieldToFilter('schedule_id', array('in' => $scheduleIds))
                    ->addFieldToFilter('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_LABEL_INITIALIZED);

                /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry */
                foreach ($collection as $queueEntry) {
                    /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Labels $labelsFactory */
                    $labelsFactory = Mage::getModel('shippedfrom/auspost_shipping_labels');
                    $labelsFactory->getAuspostLabel($queueEntry);
                }

                if (count($scheduleIds) > 0) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('shippedfrom')->__(
                            'Total of %d shipment(s) had labels retrieved', count($scheduleIds)
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Nothing was retrieved!'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     */
    public function massInitLabelAction()
    {
        $scheduleIds = $this->getRequest()->getParam('auspost');
        if (!is_array($scheduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Please select shipment(s)'));
        } else {
            try {

                /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
                $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                    ->addFieldToFilter('schedule_id', array('in' => $scheduleIds))
                    ->addFieldToFilter('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_SHIPPED);

                /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry */
                foreach ($collection as $queueEntry) {
                    /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Labels $labelsFactory */
                    $labelsFactory = Mage::getModel('shippedfrom/auspost_shipping_labels');
                    $labelsFactory->initAuspostLabel($queueEntry);
                }

                if (count($scheduleIds)) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('shippedfrom')->__(
                            'Total of %d shipment(s) labels initialised', count($scheduleIds)
                        )
                    );
                } else {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Nothing was initialised!'));
                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     *
     */
    public function massCreateAction()
    {
        $scheduleIds = $this->getRequest()->getParam('auspost');
        if (!is_array($scheduleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('shippedfrom')->__('Please select shipment(s)'));
        }
        else {
            try {
                $errors = 0;
                $trackingNumbers = array();
                /** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $collection */
                $collection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
                    ->addFieldToFilter('schedule_id', array('in' => $scheduleIds))
                    ->addFieldToFilter('status', FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_INITIALIZED);

                /** @var FactoryX_ShippedFrom_Model_Shipping_Queue $queueEntry */
                foreach ($collection as $queueEntry) {
                    /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments $shipmentsFactory */
                    $shipmentsFactory = Mage::getModel('shippedfrom/auspost_shipping_shipments');
                    if ($trackingNumber = $shipmentsFactory->createAuspostShipment($queueEntry)) {
                        $trackingNumbers[] = $trackingNumber;
                    }
                    else {
                        $errors++;
                    }
                    Mage::helper('shippedfrom')->log(sprintf("%->errors: %s", __METHOD__, $errors) );
                }
                Mage::helper('shippedfrom')->log(sprintf("%->trackingNumbers[%d]: %s", __METHOD__, count($trackingNumbers), print_r($trackingNumbers, true)) );
                if (count($trackingNumbers)) {
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('shippedfrom')->__(
                            'Total of %d shipment(s) were successfully shipped', count($trackingNumbers)
                        )
                    );
                }
                if ($errors) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('shippedfrom')->__(
                            'Total of %d shipment(s) failed to ship (see last message)', $errors
                        )
                    );                    
                }
                if (count($scheduleIds) == 0 && $errors == 0) {
                    Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('shippedfrom')->__('Nothing was shipped!'));
                }
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * @return FactoryX_ShippedFrom_Helper_Data
     */
    protected function hlp()
    {
        return Mage::helper('shippedfrom');
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
}