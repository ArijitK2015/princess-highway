<?php

$installer = $this;
$installer->startSetup();
	
// Add a sort_order column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'sort_order', 'int(11) default NULL'
	);

$installer->endSetup();