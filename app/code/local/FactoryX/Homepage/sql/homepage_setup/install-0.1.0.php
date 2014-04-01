<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('homepage/homepage')} (
  `homepage_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `identifier` varchar(255) default NULL,
  `status` tinyint(1) default NULL,
  `start_date` timestamp default 0,
  `end_date` timestamp default 0,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `edited` timestamp NOT NULL,
  PRIMARY KEY  (`homepage_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('homepage/image')} (
  `image_id` int(11) NOT NULL auto_increment,
  `homepage_id` int(11) NOT NULL,
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`image_id`),
  FOREIGN KEY  (`homepage_id`) REFERENCES {$this->getTable('homepage/homepage')}(`homepage_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('homepage/store')} (
	`homepage_id` smallint(6) unsigned,
	`store_id` smallint(6) unsigned
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

");

$installer->endSetup();