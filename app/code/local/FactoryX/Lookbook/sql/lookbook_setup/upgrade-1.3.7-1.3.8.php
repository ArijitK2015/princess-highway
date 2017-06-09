<?php

$installer = $this;

$installer->startSetup();

// root_template
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'root_template', 'varchar(255) default NULL'
    );

// site_css
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'site_css', 'text default NULL'
    );

// facebook_css
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'facebook_css', 'text default NULL'
    );

// include_in_nav
$installer
    ->getConnection()
    ->modifyColumn(
        $this->getTable('lookbook/lookbook'), 'include_in_nav', 'varchar(255) default NULL'
    );

// nav_category
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'nav_category', 'int(11) NOT NULL'
    );

// slider_nav_style
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'slider_nav_style', 'varchar(255) default NULL'
    );

// slider_pagination_style
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'slider_pagination_style', 'varchar(255) default NULL'
    );

// page_prompt
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('lookbook/lookbook'), 'page_prompt', 'tinyint(1) default NULL'
    );

$installer->endSetup();