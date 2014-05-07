<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE {$this->getTable('evlikeanalytics_stat')} (
	`stat_id`				int(11) unsigned NOT NULL auto_increment,
	`url`					varchar(255) NOT NULL default '',
	`description`			varchar(255) NOT NULL default '',
	`share_count`			int(10) NOT NULL default 0,
	`like_count`			int(10) NOT NULL default 0,
	`comment_count`			int(10) NOT NULL default 0,
	`total_count`			int(10) NOT NULL default 0,
	`click_count`			int(10) NOT NULL default 0,
	`update_time`			datetime NOT NULL,
	PRIMARY KEY (`stat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{$this->getTable('evlikeanalytics_stat')}` ADD INDEX `IDX_EVSTAT_URL` ( `url` );
ALTER TABLE `{$this->getTable('evlikeanalytics_stat')}` ADD INDEX `IDX_EVSTAT_DESC` ( `description` );

	");
	
$installer->endSetup();
