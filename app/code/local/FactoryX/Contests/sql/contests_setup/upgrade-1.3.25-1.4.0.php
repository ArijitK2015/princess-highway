<?php

$installer = $this;
$installer->startSetup();

$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'email_template_id', 'int default NULL'
    );


$installer->endSetup();