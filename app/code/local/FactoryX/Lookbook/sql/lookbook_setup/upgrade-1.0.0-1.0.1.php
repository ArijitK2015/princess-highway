<?php

$installer = $this;

$installer->startSetup();

$installer
	->getConnection()
	->changeColumn (
		$this->getTable('lookbook/lookbook'), 'category_id', 'category_id', 'int(11) default NULL'
	);

$installer->endSetup();