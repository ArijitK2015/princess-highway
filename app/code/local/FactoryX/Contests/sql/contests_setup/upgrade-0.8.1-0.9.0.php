<?php

$installer = $this;
$installer->startSetup();
	
// Add colour setting for contest
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'background_colour', 'varchar(255) default NULL'
	);

$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'text_colour', 'varchar(255) default NULL'
	);

$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'button_background_colour', 'varchar(255) default NULL'
	);

$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'button_text_colour', 'varchar(255) default NULL'
	);

$installer->endSetup();