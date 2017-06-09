<?php

$installer = $this;

$installer->startSetup();

$conn = $installer->getConnection();
$careersTable = $this->getTable('careers/careers');

// Add first name
if ($conn->tableColumnExists($careersTable, 'statuss'))
{
    $conn->changeColumn($careersTable, 'statuss', 'work_type', 'smallint(6) NULL');
}

$installer->endSetup();