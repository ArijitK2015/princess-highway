<?php

$installer = $this;
$installer->startSetup();
	
// Add colour setting for contest
$installer
	->getConnection()
	->addColumn(
		$this->getTable('contests/contest'), 'custom_css', 'text default NULL'
	);
$installer->endSetup();