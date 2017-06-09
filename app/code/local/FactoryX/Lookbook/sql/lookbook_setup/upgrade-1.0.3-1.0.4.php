<?php

$installer = $this;
$installer->startSetup();

// Add a show_shop_pix column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'show_shop_pix', 'tinyint(1) NOT NULL'
	);
	
// Add a shop_pix column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'shop_pix', 'varchar(255) default NULL'
	);

$installer->endSetup();