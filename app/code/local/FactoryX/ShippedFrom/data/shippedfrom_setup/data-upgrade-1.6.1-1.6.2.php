<?php

$installer = $this;
$installer->startSetup();

/** @var FactoryX_ShippedFrom_Model_Resource_Shipping_Queue_Collection $queueCollection */
$queueCollection = Mage::getResourceModel('shippedfrom/shipping_queue_collection')
    ->addFieldToFilter(
        'status',
        array(
            'neq'    =>  FactoryX_ShippedFrom_Model_Shipping_Queue::STATUS_INITIALIZED
        )
    );

foreach ($queueCollection as $queueEntry) {
    /** @var FactoryX_ShippedFrom_Model_Auspost_Shipping_Shipments $auspostShipmentFactory */
    $auspostShipmentFactory = Mage::getModel('shippedfrom/auspost_shipping_shipments');
    $shipmentDataRetrieval = $auspostShipmentFactory->getShipment($queueEntry);
    if (!array_key_exists('errors', $shipmentDataRetrieval)) {
        $apConsignmentId = $shipmentDataRetrieval['shipments'][0]['items'][0]['tracking_details']['consignment_id'];
        $apArticleId = $shipmentDataRetrieval['shipments'][0]['items'][0]['tracking_details']['article_id'];
        $queueEntry->setApConsignmentId($apConsignmentId);
        $queueEntry->setApArticleId($apArticleId);
        $queueEntry->getResource()->saveAttribute($queueEntry, array('ap_consignment_id', 'ap_article_id'));
    }
}

$installer->endSetup();

