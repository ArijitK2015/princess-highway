<?php

$installer = $this;
$installer->startSetup();

// Remove the identifier column from the homepage table 
$installer
	->getConnection()
	->dropColumn(
		$this->getTable('homepage/homepage'), 'identifier'
	);

$installer->endSetup();