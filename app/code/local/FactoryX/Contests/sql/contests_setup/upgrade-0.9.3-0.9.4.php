<?php

$installer = $this;
$installer->startSetup();
	
// Add options setting for contest
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'competition_options', 'text default NULL'
	);

// Add Facebook app ID
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'facebook_app_id', 'text default NULL'
	);	
$installer->endSetup();

// Add Facebook app secret
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'facebook_app_secret', 'text default NULL'
	);	
$installer->endSetup();