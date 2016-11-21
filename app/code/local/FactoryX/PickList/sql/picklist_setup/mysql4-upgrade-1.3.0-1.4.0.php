<?php
/**
remove the default value CURRENT_TIMESTAMP from created_at fields

mysql will convert these to whatever @@global.time_zone or @@session.time_zone is set to, when magento expects utc

*/

$installer = $this;

$installer->startSetup();

// ALTER TABLE picklist_job_log MODIFY COLUMN created_at TIMESTAMP null; 
// ALTER TABLE `{$this->getTable('picklist_job_log')}` MODIFY COLUMN created_at TIMESTAMP null; 

Mage::helper('picklist/mysql4_install')->attemptQuery($installer, "
    ALTER TABLE `{$this->getTable('picklist_job_log')}` MODIFY COLUMN created_at TIMESTAMP null;
");

Mage::helper('picklist/mysql4_install')->attemptQuery($installer, "
    ALTER TABLE `{$this->getTable('picklist_request_log')}` MODIFY COLUMN created_at TIMESTAMP null;
");

$installer->endSetup();
