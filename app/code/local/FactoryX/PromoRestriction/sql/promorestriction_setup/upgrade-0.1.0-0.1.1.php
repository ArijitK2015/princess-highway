<?php
/**
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->dropTable(
    $this->getTable('promorestriction/restriction')
);

$table = $installer->getConnection()->newTable($installer->getTable('promorestriction/restriction'));
$table->addColumn(
    'restriction_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ),
    'Restriction Id'
);
$table->addColumn(
    'salesrule_id',
    Varien_Db_Ddl_Table::TYPE_SMALLINT,
    null,
    array(
        'identity'  => false,
        'unsigned'  => true,
        'nullable'  => false
    ),
    'Sales Rule Id'
);
$table->addColumn(
    'restricted_field',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    255,
    array(
        'identity'  => false,
        'nullable'  => false
    ),
    'Customer Restricted Field'
);
$table->addColumn(
    'type',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    255,
    array(
        'identity'  => false,
        'nullable'  => false,
    ),
    'Restriction Type'
);
$table->addForeignKey(
    $installer->getFkName(
        'promorestriction/restriction',
        'salesrule_id',
        'salesrule/rule',
        'rule_id'
    ),
    'salesrule_id',
    $installer->getTable('salesrule/rule'),
    'rule_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$table->setComment('Customer Promo Restriction');

$installer->getConnection()->createTable($table);

$installer->getConnection()->addIndex(
    $installer->getTable('promorestriction/restriction'),
    $installer->getIdxName(
        'promorestriction/restriction',
        array('salesrule_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('salesrule_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->endSetup();
