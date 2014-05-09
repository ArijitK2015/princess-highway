<?php

$installer = $this;
$installer->startSetup();

// Add a start_date column to the homepage table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/homepage'), 'start_date', 'datetime default NULL'
	);
	
// Add a end_date column to the homepage table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/homepage'), 'end_date', 'datetime default NULL'
	);
	
// Add a displayed column to the homepage table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/homepage'), 'displayed', 'tinyint(1) default NULL'
	);

$installer->endSetup();