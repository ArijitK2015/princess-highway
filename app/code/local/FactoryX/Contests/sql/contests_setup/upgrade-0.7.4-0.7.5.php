<?php

$installer = $this;
$installer->startSetup();

// Add a is_in_list column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'allow_duplicate_entries', 'tinyint(1) default NULL'
	);


$installer->endSetup();