<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('customgrids/column')} (
  `column_id` int(11) NOT NULL auto_increment,
  `attribute_code` varchar(255) NOT NULL,
  `grid_block_type` varchar(255) NOT NULL,
  PRIMARY KEY  (`column_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;");

$installer->endSetup();
