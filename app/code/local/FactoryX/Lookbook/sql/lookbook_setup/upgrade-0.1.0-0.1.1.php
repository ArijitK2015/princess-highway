<?php

$installer = $this;
$installer->startSetup();

// Add a include_in_nav column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'include_in_nav', 'tinyint(1) NOT NULL'
	);

$installer->endSetup();