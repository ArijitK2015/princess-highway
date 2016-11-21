<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('lookbook/lookbook')} (
  `lookbook_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `identifier` varchar(255) default NULL,
  `category_id` int(11) NOT NULL,
  `status` tinyint(1) default NULL,
  `looks_per_page` varchar(255) default NULL,
  `lookbook_width` varchar(255) default NULL,
  `look_width` varchar(255) default NULL,
  `look_height` varchar(255) default NULL,
  `look_border` varchar(255) default NULL,
  `look_color` varchar(255) default NULL,
  `show_child_products_link` tinyint(1) default NULL,
  `zoom_on_hover` tinyint(1) default NULL,
  `added` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `edited` timestamp NOT NULL,
  PRIMARY KEY  (`lookbook_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('lookbook/store')} (
	`lookbook_id` smallint(6) unsigned,
	`store_id` smallint(6) unsigned
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;

");

$installer->endSetup();