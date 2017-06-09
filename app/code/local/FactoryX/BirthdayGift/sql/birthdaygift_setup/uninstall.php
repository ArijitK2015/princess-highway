<?php

$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$subscriberTable = $this->getTable('newsletter_subscriber');

// Add last birthday coupon
if ($conn->tableColumnExists($subscriberTable, 'last_birthday_coupon'))
{
    $conn->dropColumn($subscriberTable, 'last_birthday_coupon', 'varchar(100)');
}

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->removeAttribute('customer', 'last_birthday_coupon');

$installer->endSetup();
