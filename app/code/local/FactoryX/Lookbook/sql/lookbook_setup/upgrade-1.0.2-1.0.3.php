<?php

$installer = $this;
$installer->startSetup();

// Add a look_description_padding column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'look_description_padding', 'varchar(255) default NULL'
	);
	
// Add a look_description_max_lines column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'look_description_max_lines', 'varchar(255) default NULL'
	);

// Add a look_description_height column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'look_description_height', 'varchar(255) default NULL'
	);
	
// Add a look_description_border_width column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'look_description_border_width', 'varchar(255) default NULL'
	);
	
// Add a look_description_side_padding column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'look_description_side_padding', 'varchar(255) default NULL'
	);
	
// Add a bar_width column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'bar_width', 'varchar(255) default NULL'
	);
	
// Add a look_scale_height column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'look_scale_height', 'varchar(255) default NULL'
	);
	
// Add a look_scale_side_padding column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'look_scale_side_padding', 'varchar(255) default NULL'
	);

$installer->endSetup();