<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('contests/contest')} (
  `contest_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `identifier` varchar(255) default NULL,
  `status` tinyint(1) default NULL,
  `start_date` timestamp default 0,
  `end_date` timestamp default 0,
  `type` tinyint(1) default NULL,
  `store_id` tinyint(1) default NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `terms` text NOT NULL,
  `image_url` varchar(255) default NULL,
  `email_image_url` varchar(255) default NULL,
  `share_on_facebook` tinyint(1) default NULL,
  `is_promo` tinyint(1) default NULL,
  `promo_text` varchar(255) default NULL,
  `minimum_word_count` smallint(2) default NULL,
  `displayed` tinyint(1) default NULL,
  PRIMARY KEY  (`contest_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('contests/referrer')} (
  `referrer_id` int(11) NOT NULL auto_increment,
  `contest_id` int(11) NOT NULL,
  `email` varchar(255) default NULL,
  `name` varchar(255) default NULL,
  `mobile` varchar(10) default NULL,
  `state` varchar(50) default NULL,
  `promo` varchar(255) default NULL,
  `is_winner` tinyint(1) default 0,
  `entry_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`referrer_id`),
  FOREIGN KEY  (`contest_id`) REFERENCES {$this->getTable('contests/contest')}(`contest_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('contests/referee')} (
  `referee_id` int(11) NOT NULL auto_increment,
  `contest_id` int(11) NOT NULL,
  `referrer_id` int(11) NOT NULL,
  `email` varchar(255) default NULL,
  `entry_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`referee_id`),
  FOREIGN KEY  (`contest_id`) REFERENCES {$this->getTable('contests/contest')}(`contest_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();
