<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('categorybanners/banner')} (
  `banner_id` int(11) NOT NULL auto_increment,
  `category_id` int(11) NOT NULL,
  `image` varchar(255) default NULL,
  `alt` varchar(255) default NULL,
  `start_date` datetime default NULL,
  `end_date` datetime default NULL,
  `status` tinyint(1) default NULL,
  `url` varchar(255) default NULL,
  `display_on_children` tinyint(1) default NULL,
  `displayed` tinyint(1) default NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `edited` timestamp NOT NULL,
  PRIMARY KEY  (`banner_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

");

$installer->endSetup();