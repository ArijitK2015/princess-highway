<?php

$installer = $this;
$installer->startSetup();

// Add a slider column to the homepage table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/homepage'), 'slider', 'tinyint(1) NOT NULL'
	);
	
// Add a amount column to the homepage table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/homepage'), 'amount', 'varchar(255) default NULL'
	);

// Add a layout column to the homepage table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/homepage'), 'layout', 'varchar(255) default NULL'
	);

$installer->endSetup();