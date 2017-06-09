<?php
/**
 * Add logging table for missing images
 */

$installer = $this;

$this->startSetup();

$installer->run("
	ALTER TABLE {$this->getTable('imagecdn/cachedb')} ADD `http_code` VARCHAR(3) DEFAULT '' NULL;
	ALTER TABLE {$this->getTable('imagecdn/cachedb')} ADD `last_upload` datetime DEFAULT NULL;
");

$this->endSetup();