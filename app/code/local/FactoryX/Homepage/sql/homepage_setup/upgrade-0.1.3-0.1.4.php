<?php

$installer = $this;
$installer->startSetup();

// Add a popup column to the image table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/image'), 'popup', 'tinyint(1) DEFAULT 0'
	);

$installer->endSetup();