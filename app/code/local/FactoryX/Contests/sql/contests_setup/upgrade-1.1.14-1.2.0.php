<?php

$installer = $this;

$installer->startSetup();

$foreignKeys = $installer
    ->getConnection()
    ->getForeignKeys(
        $this->getTable('contests/referrer')
    );

if (!empty($foreignKeys)) {
    $firstFk = reset($foreignKeys);

    $installer
        ->getConnection()
        ->dropForeignKey(
            $installer->getTable('contests/referrer'),
            $firstFk['FK_NAME']
        );
}

$installer
    ->getConnection()
    ->addForeignKey(
        $installer->getFkName('contests/referrer', 'contest_id', 'contests/contest', 'contest_id'),
        $installer->getTable('contests/referrer'),
        'contest_id',
        $installer->getTable('contests/contest'),
        'contest_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$foreignKeys = $installer
    ->getConnection()
    ->getForeignKeys(
        $this->getTable('contests/referee')
    );

if (!empty($foreignKeys)) {
    $firstFk = reset($foreignKeys);

    $installer
        ->getConnection()
        ->dropForeignKey(
            $installer->getTable('contests/referee'),
            $firstFk['FK_NAME']
        );
}

$installer
    ->getConnection()
    ->addForeignKey(
        $installer->getFkName('contests/referee', 'contest_id', 'contests/contest', 'contest_id'),
        $installer->getTable('contests/referee'),
        'contest_id',
        $installer->getTable('contests/contest'),
        'contest_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE,
        Varien_Db_Ddl_Table::ACTION_CASCADE
    );

$foreignKeys = $installer
    ->getConnection()
    ->getForeignKeys(
        $this->getTable('contests/referee')
    );

$installer->endSetup();