<?php

$installer = $this;
$installer->startSetup();

$adminUserTable = $installer->getTable('admin/user');

if ($conn->tableColumnExists($adminUserTable, 'location_id'))
{
    $conn->dropForeignKey($adminUserTable, $this->getFkName('admin_user','location_id','ustorelocator_location','location_id'));
    $conn->dropColumn($adminUserTable, 'location_id');
}

if (Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('ustorelocator_location')) ) {
    $installer
        ->getConnection()
        ->dropTable($this->getTable('ustorelocator_location'));
}

$installer->endSetup();
