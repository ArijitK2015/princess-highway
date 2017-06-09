<?php

$installer = $this;

$installer->startSetup();

$installer->run("
UPDATE {$this->getTable('lookbook/lookbook')} SET layout = 'default' where layout = '';
");

$installer->endSetup();