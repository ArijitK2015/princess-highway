<?php
/**
 * Add logging table for missing images
 */

$installer = $this;

$this->startSetup();

$installer->run("
	ALTER TABLE {$this->getTable('imagecdn/cachedb')} ADD `url_referrer` VARCHAR(255) DEFAULT '' NULL;
");

$this->endSetup();