<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('couponvalidation/log')} (
  `log_id` int(11) NOT NULL auto_increment,
  `coupon_code` varchar(255) default NULL,
  `rule_id` int(11) default NULL,
  `comment` varchar(255) default NULL,
  `admin_user` varchar(255) default NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`log_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;");

$installer->endSetup();
