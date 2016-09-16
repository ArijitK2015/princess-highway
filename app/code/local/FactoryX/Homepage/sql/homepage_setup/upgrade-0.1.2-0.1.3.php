<?php

$installer = $this;
$installer->startSetup();

// Add a link column to the image table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/image'), 'link', 'varchar(255) NOT NULL'
	);
	
// Add an alt column to the image table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/image'), 'alt', 'varchar(255) NOT NULL'
	);

$installer->endSetup();