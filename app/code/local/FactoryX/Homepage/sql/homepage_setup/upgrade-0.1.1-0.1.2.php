<?php

$installer = $this;
$installer->startSetup();

// Add an index column to the image table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/image'), 'index', 'int(10) NOT NULL'
	);

$installer->endSetup();