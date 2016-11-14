<?php
$this->startSetup();

$table = $this->getTable('ustorelocator_location');

$conn = $this->getConnection();

if (!$conn->tableColumnExists($table, 'ip_address')) {
	 $conn->addColumn($table, 'ip_address', 'VARCHAR(15) NULL AFTER website_url');
}

$this->endSetup();