<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('abandonedcarts/log')} (
  `log_id` int(11) NOT NULL auto_increment,
  `customer_email` varchar(255) default NULL,
  `type` int(11) default NULL,
  `comment` text default NULL,
  `store` int(11) default NULL,
  `dryrun` varchar(255) default NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`log_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;");

$installer->endSetup();
