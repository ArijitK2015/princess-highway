<?php

$installer = $this;

$installer->startSetup();

// Add a lookbook_type column to the lookbook table 
$installer
	->getConnection()
	->addColumn(
		$this->getTable('lookbook/lookbook'), 'lookbook_type', 'varchar(255) default NULL'
	);

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('lookbook/image')} (
  `image_id` int(11) NOT NULL auto_increment,
  `lookbook_id` int(11) NOT NULL,
  `url` varchar(255) default NULL,
  PRIMARY KEY  (`image_id`),
  FOREIGN KEY  (`lookbook_id`) REFERENCES {$this->getTable('lookbook/lookbook')}(`lookbook_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;
");

$installer->endSetup();