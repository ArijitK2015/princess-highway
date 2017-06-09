<?php
/**
 */

$installer = $this;

$installer->startSetup();

/*
Mage::helper('picklist/mysql4_install')->attemptQuery($installer, "
    ALTER TABLE `{$this->getTable('picklist_job_log')}` 
        CHANGE `to` `email_to` VARCHAR(255)  NOT NULL  DEFAULT '';
");
*/

Mage::helper('picklist/mysql4_install')->prepareForDb();

Mage::log(sprintf("%s->create table '%s'", get_class($this), $this->getTable('picklist_request_log')) );

Mage::helper('picklist/mysql4_install')->attemptQuery($installer, "
    CREATE TABLE IF NOT EXISTS `{$this->getTable('picklist_request_log')}` (
      `request_id` int(10) unsigned NOT NULL auto_increment,
      `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
      `source_client` varchar(255) NOT NULL default '',
      `source_ip` varchar(255) NULL,
      `http_request` text,
      `response_code` varchar(10) NULL,
      PRIMARY KEY  (`request_id`),
      KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
