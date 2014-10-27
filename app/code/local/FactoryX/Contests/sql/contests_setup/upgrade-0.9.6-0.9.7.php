<?php

$installer = $this;
$installer->startSetup();

// Add a thank_you_link_type column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'thank_you_link_type', 'tinyint(1) default NULL'
	);


$installer->endSetup();