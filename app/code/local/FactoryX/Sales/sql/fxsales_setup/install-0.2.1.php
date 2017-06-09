<?php
$installer = $this;

$installer->startSetup();

// Required tables
$statusTable = $installer->getTable('sales/order_status');
$statusStateTable = $installer->getTable('sales/order_status_state');
$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

// Statuses to add
$statusesToAdd = array(
    array('status' => 'processing_part_shipped', 'label' =>  'Processing - Partially Shipped'),
    array('status' => 'processing_part_shipped_nt', 'label' => 'Processing - Partially Shipped No Tracking'),
    array('status' => 'processing_shipped_nt','label' =>  'Processing - Shipped No Tracking'),
    array('status' => 'processing_stage2','label' =>  'Processing - Stage 2')
);

// Status / State Mapping
$statusStateMapping = array(
    array(
        'status' => 'processing_part_shipped',
        'state' => 'processing',
        'is_default' => 0
    ),
    array(
        'status' => 'processing_part_shipped_nt',
        'state' => 'processing',
        'is_default' => 0
    ),
    array(
        'status' => 'processing_shipped_nt',
        'state' => 'processing',
        'is_default' => 0
    ),
    array(
        'status' => 'processing_stage2',
        'state' => 'processing',
        'is_default' => 0
    )
);

// Test if statuses already exist
foreach ($statusesToAdd as $key => $statusToAdd)
{
    $statusCode = $statusToAdd['status'];
    $query = "SELECT count(*) FROM {$statusTable} WHERE status = '$statusCode'";
    $count = $readConnection->fetchOne($query);
    if ($count)
    {
        unset($statusesToAdd[$key]);
    }
}
if (count($statusesToAdd))
{
    // Insert statuses
    $installer->getConnection()->insertArray(
        $statusTable,
        array(
            'status',
            'label'
        ),
        $statusesToAdd
    );

    // Insert states and mapping of statuses to states
    $installer->getConnection()->insertArray(
        $statusStateTable,
        array(
            'status',
            'state',
            'is_default'
        ),
        $statusStateMapping
    );
}

$installer->endSetup();
