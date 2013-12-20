<?php

$installer = $this;
$installer->startSetup();

// Add a is_popup column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'is_popup', 'tinyint(1) NOT NULL default 0'
	);

// Add a popup_text column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'popup_text', 'varchar(255) default NULL'
	);


$installer->endSetup();