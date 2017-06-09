<?php

$installer = $this;
$installer->startSetup();
	
// Add a default_url column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'default_url', 'varchar(255) default NULL'
	);

$installer->endSetup();