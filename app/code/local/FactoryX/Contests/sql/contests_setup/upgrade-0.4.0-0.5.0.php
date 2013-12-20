<?php

$installer = $this;
$installer->startSetup();

// Add a popup_referrers column to the contests table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'popup_referers', 'varchar(255) default NULL'
	);


$installer->endSetup();