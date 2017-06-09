<?php
$installer = $this;
$installer->startSetup();

$table = $installer->getTable('ustorelocator_location');

$conn = $installer->getConnection();

$field = 'store_code';
if ($conn->tableColumnExists($table, $field)) {
    $sql = sprintf("ALTER TABLE `%s` MODIFY `%s` VARCHAR(4);", $table, $field);
    $installer->run($sql);
}

$installer->endSetup();