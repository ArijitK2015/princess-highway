<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin/permission_variable');

if ($installer->getConnection()->isTableExists($tableName)) {
   $installer->run("
    DELETE FROM {$tableName} WHERE variable_name = 'general/store_information/hours';
    DELETE FROM {$tableName} WHERE variable_name = 'general/store_information/head_phone';
    DELETE FROM {$tableName} WHERE variable_name = 'trans_email/ident_marketing/email';
    DELETE FROM {$tableName} WHERE variable_name = 'trans_email/ident_marketing/name';
   ");
}

$installer->endSetup();
