<?php
/**
 *
 */

$installer = $this;

$installer->startSetup();

Mage::helper('picklist/mysql4_install')->prepareForDb();

Mage::log(sprintf("%s->create table '%s'", get_class($this), $this->getTable('picklist_job_log')) );

Mage::helper('picklist/mysql4_install')->attemptQuery($installer, "
    CREATE TABLE IF NOT EXISTS `{$this->getTable('picklist_job_log')}` (
      `job_id` int(10) unsigned NOT NULL auto_increment,
      `created_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
      `job_type` varchar(255) NOT NULL default '',
      `source_type` varchar(255) NOT NULL default '',
      `source_ip` varchar(255) NULL,
      `http_request` text,
      `output_type` varchar(255) NOT NULL default '',
      `http_response` text,
      PRIMARY KEY  (`job_id`),
      KEY `created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$moduleName = "FactoryX_PickList";
Mage::helper('picklist/mysql4_install')
    ->createInstallNotice(
        sprintf("%s was installed successfully.", $moduleName),
        sprintf("%s has been installed successfully. Go to the system configuration section of your Magento admin to configure the pick list and get it up and running.", $moduleName)
);

$installer->endSetup();
