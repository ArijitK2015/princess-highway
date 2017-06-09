<?php

$installer = $this;
$installer->startSetup();

// Add a show_credits column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'show_credits', 'tinyint(1) NOT NULL'
	);
	
// Add a model column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'model', 'varchar(255) default NULL'
	);

// Add a photography column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'photography', 'varchar(255) default NULL'
	);

// Add a make_up column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'make_up', 'varchar(255) default NULL'
	);

$installer->endSetup();