<?php

$installer = $this;
$installer->startSetup();
	
// Add a direction column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'direction', 'varchar(255) default NULL'
	);

$installer->endSetup();