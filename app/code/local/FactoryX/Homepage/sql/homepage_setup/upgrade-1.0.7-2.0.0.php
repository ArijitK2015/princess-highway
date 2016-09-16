<?php

$installer = $this;
$installer->startSetup();

// Add a sort_order column to the homepage table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/homepage'), 'sort_order', 'int(10) NULL'
	);

$installer->endSetup();