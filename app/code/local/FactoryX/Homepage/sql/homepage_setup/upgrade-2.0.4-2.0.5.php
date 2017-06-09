<?php

$installer = $this;
$installer->startSetup();

// Add a slider_speed column to the homepage table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('homepage/homepage'), 'slider_speed', 'varchar(255) default NULL'
	);
$installer->endSetup();