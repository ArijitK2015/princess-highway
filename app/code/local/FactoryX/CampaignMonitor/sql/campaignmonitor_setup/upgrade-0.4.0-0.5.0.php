<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$subscriberTable = $this->getTable('newsletter_subscriber');

// Add coupon
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_coupon')) 
{
     $conn->addColumn($subscriberTable, 'subscriber_coupon', 'varchar(20)');
}

$installer->endSetup();