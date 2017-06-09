<?php

$installer = $this;
$installer->startSetup();
	
// Add colour setting for contest
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'new_subscriber_counter', 'smallint(2) default 0'
	);
$installer->endSetup();