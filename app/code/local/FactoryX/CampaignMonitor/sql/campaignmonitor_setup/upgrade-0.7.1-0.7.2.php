<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$subscriberTable = $this->getTable('newsletter_subscriber');

// Add coupon
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_preferredstore')) 
{
     $conn->addColumn($subscriberTable, 'subscriber_preferredstore', 'varchar(200)');
}

$installer->endSetup();