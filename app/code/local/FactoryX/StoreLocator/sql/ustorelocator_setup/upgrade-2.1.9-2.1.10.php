<?php
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('ustorelocator_location');

$conn = $installer->getConnection();

$field = 'ip_address';
if ($conn->tableColumnExists($table, $field)) {
    // change column size
    $sql = sprintf("ALTER TABLE `%s` MODIFY `%s` VARCHAR(255);", $table, $field);
    $installer->run($sql);
}

$installer->endSetup();