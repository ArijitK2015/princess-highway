<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$subscriberTable = $this->getTable('newsletter_subscriber');

// Add coupon
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_securehash')) 
{
     $conn->addColumn($subscriberTable, 'subscriber_securehash', 'varchar(32)');
}

$installer->endSetup();