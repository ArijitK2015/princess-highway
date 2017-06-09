<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('abandonedcarts/link')} (
  `link_id` int(11) NOT NULL auto_increment,
  `token_hash` text default NULL,
  `customer_email` varchar(255) default NULL,
  `expiration_date` datetime default NULL,
  PRIMARY KEY  (`link_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;");

$installer->endSetup();
