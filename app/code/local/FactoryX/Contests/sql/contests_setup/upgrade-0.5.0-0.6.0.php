<?php

$installer = $this;
$installer->startSetup();

// Update the dates column to handle the date properly

$installer
	->getConnection()
	->modifyColumn(
		$this->getTable('contests/contest'), 'start_date', 'datetime default NULL'
	);
	
$installer
	->getConnection()
	->modifyColumn(
		$this->getTable('contests/contest'), 'end_date', 'datetime default NULL'
	);

$installer->endSetup();