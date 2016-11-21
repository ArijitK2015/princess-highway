<?php

$installer = $this;
$installer->startSetup();

// Add referrer line
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('contests/contest'), 'more_friend_line', 'text default NULL'
    );
