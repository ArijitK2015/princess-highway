<?php

$installer = $this;
$installer->startSetup();

// Add a is_in_list column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'is_in_list', 'tinyint(1) NOT NULL default 1'
	);
	
// Add a list_image_url column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'list_image_url', 'varchar(255) default NULL'
	);

// Add a list_text column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'list_text', 'varchar(255) default NULL'
	);

// Add a is_in_list column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'allow_duplicate_referrals', 'tinyint(1) default NULL'
	);


$installer->endSetup();