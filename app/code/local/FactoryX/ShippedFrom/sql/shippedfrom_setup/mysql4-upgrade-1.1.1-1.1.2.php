<?php
    $installer = $this;
    $installer->startSetup();
    $installer->getConnection()->addColumn($this->getTable('admin/user'), 'store', 'INT NULL');
    $installer->endSetup();