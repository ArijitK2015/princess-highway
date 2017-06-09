<?php
/**
 */

$installer = $this;

$installer->startSetup();

$table = $this->getTable('careers/old_careers');
Mage::helper('careers')->log(sprintf("update table '%s'", $table));

//$installer->run("ALTER TABLE `{$this->getTable('careers/old_careers')}` CHANGE `to` `email_to` VARCHAR(255)  NOT NULL  DEFAULT '';");

// ALTER TABLE fx_careers DROP COLUMN sort;
$sql = "ALTER TABLE `{$this->getTable('careers/old_careers')}` ADD COLUMN `sort` INT NOT NULL DEFAULT 0 AFTER `email`;";
Mage::helper('careers')->log(sprintf("sql: %s", $sql));
$installer->run($sql);

$installer->endSetup();