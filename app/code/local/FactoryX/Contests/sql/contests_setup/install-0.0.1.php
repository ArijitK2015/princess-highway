<?php

$installer = $this;

$installer->startSetup();

// FIRST WE COPY DATA ACCROSS FROM OLD TABLES AND MODIFY THE URL REWRITES
$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('contests/contest')} LIKE `fx_competitions_competition`;
INSERT INTO {$this->getTable('contests/contest')} SELECT * FROM `fx_competitions_competition`;
DROP TABLE IF EXISTS `fx_competitions_competition`;

CREATE TABLE IF NOT EXISTS {$this->getTable('contests/referrer')} LIKE `fx_competitions_referrer`;
INSERT INTO {$this->getTable('contests/referrer')} SELECT * FROM `fx_competitions_referrer`;
DROP TABLE IF EXISTS `fx_competitions_referrer`;

CREATE TABLE IF NOT EXISTS {$this->getTable('contests/referee')} LIKE `fx_competitions_referee`;
INSERT INTO {$this->getTable('contests/referee')} SELECT * FROM `fx_competitions_referee`;
DROP TABLE IF EXISTS `fx_competitions_referee`;

CREATE TABLE IF NOT EXISTS {$this->getTable('contests/store')} LIKE `fx_competitions_store`;
INSERT INTO {$this->getTable('contests/store')} SELECT * FROM `fx_competitions_store`;
DROP TABLE IF EXISTS `fx_competitions_store`;

DROP TABLE IF EXISTS `fx_competitions_winner_tmp`;

UPDATE core_url_rewrite SET target_path = REPLACE(target_path,'competition','contest');

");

// SECOND WE ALTER THE COLUMNS THAT NEEDS TO BE ALTERED
$installer
	->getConnection()
	->changeColumn (
		$this->getTable('contests/contest'), 'competition_id', 'contest_id', 'int(11) NOT NULL auto_increment'
	);
	
$installer
	->getConnection()
	->changeColumn (
		$this->getTable('contests/contest'), 'is_promo', 'is_competition', 'tinyint(1) default NULL'
	);
	
$installer
	->getConnection()
	->changeColumn (
		$this->getTable('contests/contest'), 'promo_text', 'competition_text', 'varchar(255) default NULL'
	);
	
$installer
	->getConnection()
	->changeColumn (
		$this->getTable('contests/referrer'), 'competition_id', 'contest_id', 'int(11) NOT NULL'
	);
	
$installer
	->getConnection()
	->changeColumn (
		$this->getTable('contests/referrer'), 'promo', 'competition', 'varchar(255) default NULL'
	);

$installer
	->getConnection()
	->changeColumn (
		$this->getTable('contests/referee'), 'competition_id', 'contest_id', 'int(11) NOT NULL'
	);

$installer
	->getConnection()
	->changeColumn (
		$this->getTable('contests/store'), 'competition_id', 'contest_id', 'smallint(6) unsigned'
	);
	
// THEN WE MODIFY THE MEDIA FOLDER

$installer->endSetup();