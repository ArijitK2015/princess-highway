<?php

$installer = $this;
$installer->startSetup();

// Add a popup_referrers column to the contests table
$installer
    ->getConnection()
    ->addColumn(
        $this->getTable('creditmemoreasons/reason'), 'sort_order', 'int(11) default NULL'
    );

$installer->endSetup();