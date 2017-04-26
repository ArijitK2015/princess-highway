<?php

$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('shippedfrom/account');

// create the table if it doesn't exist
if ($installer->getConnection()->isTableExists($tableName) != true) {
    $table = $installer->getConnection()
        ->newTable($tableName)
        ->addColumn('account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Account Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
        ), 'Name')
        ->addColumn('valid_from', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Valid From')
        ->addColumn('valid_to', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Valid To')
        ->addColumn('expired', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Expired')
        ->addColumn('details_lodgement_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Details Lodgement Postcode')
        ->addColumn('details_abn', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Details ABN')
        ->addColumn('details_acn', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Details ACN')
        ->addColumn('details_contact_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Details Contact Number')
        ->addColumn('details_email_address', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Details Email Address')
        ->addColumn('merchant_location_id', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
            'nullable'  => false,
        ), 'Merchant Location ID')
        ->addColumn('credit_blocked', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
            'nullable'  => false,
        ), 'Credit Blocked')
        ->addColumn('api_key', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
            'nullable'  => false,
        ), 'API Key')
        ->addColumn('api_pwd', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
            'nullable'  => false,
        ), 'API Password')
        ->addColumn('account_no', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'nullable'  => false,
            'nullable'  => false,
        ), 'Account Number')
        ->setComment('Account');

    $installer->getConnection()->createTable($table);
}

$installer->endSetup();