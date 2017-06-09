<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('creditmemoreasons/reason')} (
  `reason_id` int(11) NOT NULL auto_increment,
  `title` varchar(255) default NULL,
  `identifier` varchar(255) NOT NULL UNIQUE,
  PRIMARY KEY  (`reason_id`)
)  ENGINE = InnoDB DEFAULT CHARSET = utf8;");

$installer->endSetup();
