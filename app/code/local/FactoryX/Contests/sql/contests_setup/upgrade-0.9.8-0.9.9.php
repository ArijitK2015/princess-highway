<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'confirmation_email_template_id', 'int default NULL'
    );

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'confirmation_email', 'tinyint(1) default NULL'
    );


$installer->endSetup();