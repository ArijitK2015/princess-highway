<?php

$installer = $this;
$installer->startSetup();

// Remove the store_id column from the contests table 
$installer
	->getConnection()
	->dropColumn(
		$this->getTable('contests/contest'), 'store_id'
	);

// Create a proper store table
$installer->run("
	CREATE TABLE IF NOT EXISTS {$this->getTable('contests/store')} (
		`contest_id` smallint(6) unsigned,
		`store_id` smallint(6) unsigned
	)  ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();