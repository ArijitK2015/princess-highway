<?php

$installer = $this;
$installer->startSetup();

// Add columns to the lookbook table 
$installer->getConnection()->addColumn(
    $this->getTable('lookbook/lookbook'), 'under_product_info_links_contact', 'varchar(255) default NULL'
);
$installer->getConnection()->addColumn(
    $this->getTable('lookbook/lookbook'), 'under_product_info_links_subject', 'varchar(255) default NULL'
);
$installer->getConnection()->addColumn(
    $this->getTable('lookbook/lookbook'), 'under_product_info_links_unavailable_prefix', 'varchar(255) default NULL'
);
$installer->getConnection()->addColumn(
    $this->getTable('lookbook/lookbook'), 'under_product_info_links_available_prefix', 'varchar(255) default NULL'
);

$installer->endSetup();