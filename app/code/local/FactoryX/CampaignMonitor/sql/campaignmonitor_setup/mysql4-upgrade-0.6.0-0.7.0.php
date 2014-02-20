<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$subscriberTable = $this->getTable('newsletter_subscriber');

// Alternate state column to have full width

if ($conn->tableColumnExists($subscriberTable, 'subscriber_state')) 
{
     $conn->modifyColumn($subscriberTable, 'subscriber_state', 'varchar(32)');
}

$installer->endSetup();