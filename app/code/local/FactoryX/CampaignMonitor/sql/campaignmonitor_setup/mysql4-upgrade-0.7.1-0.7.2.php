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

// Add date of birth
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_promocode')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_promocode', 'varchar(100)');
}


$installer->endSetup();