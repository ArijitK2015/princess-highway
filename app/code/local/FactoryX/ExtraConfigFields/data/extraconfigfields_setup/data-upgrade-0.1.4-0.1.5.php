<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$tableName = $installer->getTable('admin/permission_variable');

if ($installer->getConnection()->isTableExists($tableName)) {

    $dataToInsert = array(
        array('variable_name' => 'general/store_information/hours', 'is_allowed' => 1),
        array('variable_name' => 'general/store_information/head_phone', 'is_allowed' => 1),
        array('variable_name' => 'trans_email/ident_marketing/email', 'is_allowed' => 1),
        array('variable_name' => 'trans_email/ident_marketing/name', 'is_allowed' => 1),
    );

    $alreadyAllowedPaths = Mage::getResourceModel('admin/variable')
        ->getAllowedPaths();

    foreach ($dataToInsert as $key => $entry) {
        if (array_key_exists($entry['variable_name'], $alreadyAllowedPaths)) {
            unset($dataToInsert[$key]);
        }
    }

    if ($dataToInsert) {
        $installer->getConnection()->insertMultiple(
            $tableName,
            $dataToInsert
        );
    }
}

$installer->endSetup();