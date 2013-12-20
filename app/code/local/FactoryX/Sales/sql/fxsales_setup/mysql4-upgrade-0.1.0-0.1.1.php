<?php
/**
order_status_history
sales_flat_order_status_history

ALTER TABLE sales_flat_order_status_history ADD track_user VARCHAR(50) AFTER comment;

References
http://stackoverflow.com/questions/8527689/create-custom-order-statuses-in-magento-1-5
*/
$installer = $this;
$installer->startSetup();

// add track_user
//$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
//$setup->addAttribute('sales_flat_order_status_history', 'track_user', array('type' => 'varchar'));

/*
add new statuses and assign to state 'processing'
*/

/*
$sql = "INSERT INTO '{$this->getTable('sales/order_status')}' ('status', 'label') VALUES ('processing_stage2',  'Processing - Stage 2');";
Mage::helper('fx_sales')->log(sprintf("%s", $sql));

$installer->run($sql);

$installer->run("
    INSERT INTO '{$this->getTable('sales/order_status')}' ('status', 'label') VALUES ('processing_part_shipped_nt',  'Processing - Partially Shipped No Tracking');
    INSERT INTO '{$this->getTable('sales/order_status')}' ('status', 'label') VALUES ('processing_shipped_nt',  'Processing - Shipped No Tracking');
    INSERT INTO '{$this->getTable('sales/order_status')}' ('status', 'label') VALUES ('processing_part_shipped',  'Processing - Partially Shipped');
    INSERT INTO '{$this->getTable('sales/order_status_state')}' ('status','state','is_default') VALUES ('processing_stage2', 'processing', '0');
    INSERT INTO '{$this->getTable('sales/order_status_state')}' ('status','state','is_default') VALUES ('processing_part_shipped_nt', 'processing', '0');
    INSERT INTO '{$this->getTable('sales/order_status_state')}' ('status','state','is_default') VALUES ('processing_shipped_nt', 'processing', '0');
    INSERT INTO '{$this->getTable('sales/order_status_state')}' ('status','state','is_default') VALUES ('processing_part_shipped', 'processing', '0');
");
*/
$installer->endSetup();
