<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$subscriberTable = $this->getTable('newsletter_subscriber');

// Add first name
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_firstname')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_firstname', 'varchar(100)');
}

// Add last name
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_lastname')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_lastname', 'varchar(100)');
}

// Add mobile number
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_mobile')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_mobile', 'varchar(10)');
}

// Add date of birth
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_dob')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_dob', 'date');
}

// Add state
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_state')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_state', 'varchar(50)');
}

// Add source
if (!$conn->tableColumnExists($subscriberTable, 'source')) 
{
	 $conn->addColumn($subscriberTable, 'source', 'varchar(100)');
}

// Add periodicity
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_periodicity')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_periodicity', 'varchar(50)');
}

// Add job interest
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_jobinterest')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_jobinterest', 'tinyint(1) not null');
}

// Add subscription date
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_subscriptiondate')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_subscriptiondate', 'date not null');
}

// Add postcode
if (!$conn->tableColumnExists($subscriberTable, 'subscriber_postcode')) 
{
	 $conn->addColumn($subscriberTable, 'subscriber_postcode', 'varchar(4)');
}

$installer->endSetup();
