<?php
/**
 */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'fx_promo_restriction'
 */
if (!Mage::getSingleton('core/resource')->getConnection('core_write')->showTableStatus($installer->getTable('promorestriction/restriction')) ) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('promorestriction/restriction'))
        ->addColumn('salesrule_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'identity'  => false,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Sales Rule Id')
        ->addColumn('restricted_field', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'identity'  => false,
            'nullable'  => false,
            'primary'   => true,
        ), 'Customer Restricted Field')
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
            'identity'  => false,
            'nullable'  => false,
            'primary'   => true,
        ), 'Restriction Type')
        ->addForeignKey($installer->getFkName(
            'promorestriction/restriction',
            'salesrule_id',
            'salesrule/rule',
            'rule_id'
        ),
            'salesrule_id',
            $installer->getTable('salesrule/rule'),
            'rule_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE,
            Varien_Db_Ddl_Table::ACTION_NO_ACTION)
        ->setComment('Customer Promo Restriction');
    $installer->getConnection()->createTable($table);

    $installer->getConnection()
        ->addIndex(
            $installer->getTable('promorestriction/restriction'),
            $installer->getIdxName(
                'promorestriction/restriction',
                ['salesrule_id'],
                Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
            ),
            ['salesrule_id'],
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        );
}
else {
    Mage::helper('promorestriction')->log(sprintf("skip create '%s', table already exists", $installer->getTable('promorestriction/restriction')));
}

$installer->endSetup();
