<?php

$installer = $this;

$installer->startSetup();

$conn = $installer->getConnection();
$userTable = $this->getTable('admin_user');

// Add first name
if ($conn->tableColumnExists($userTable, 'store') && !$conn->tableColumnExists($userTable, 'location_id'))
{
    $conn->changeColumn($userTable, 'store', 'location_id', 'int(10) NULL');
    $conn->addForeignKey(
        $this->getFkName('admin_user','location_id','ustorelocator_location','location_id'),
        $this->getTable('admin_user'),
        'location_id',
        $this->getTable('ustorelocator_location'),
        'location_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );
}

$installer->endSetup();