<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$subscriberTable = $this->getTable('newsletter_subscriber');

// Add last birthday coupon
if (!$conn->tableColumnExists($subscriberTable, 'last_birthday_coupon'))
{
    $conn->addColumn($subscriberTable, 'last_birthday_coupon', 'varchar(100)');
}

$installer->endSetup();
