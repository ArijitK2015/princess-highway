<?php
/**
 */

$installer = $this;

$installer->startSetup();

// ALTER TABLE picklist_job_log ADD COLUMN email_sent SMALLINT DEFAULT 0 AFTER output_type;
Mage::helper('picklist/mysql4_install')->attemptQuery($installer, "
    ALTER TABLE `{$this->getTable('picklist_job_log')}` ADD email_sent SMALLINT DEFAULT 0 AFTER output_type;
");

$installer->endSetup();
