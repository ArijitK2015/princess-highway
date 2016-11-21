<?php

$installer = $this;

$installer->startSetup();

$installer->run("
UPDATE {$this->getTable('lookbook/lookbook')} SET include_in_nav = 'before' WHERE include_in_nav = 1;
UPDATE {$this->getTable('lookbook/lookbook')} SET include_in_nav = 'no' WHERE include_in_nav NOT IN ('no','before','after');
");

$installer->endSetup();