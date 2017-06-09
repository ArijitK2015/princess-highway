<?php

$installer = $this;
$installer->startSetup();

// add created_by field to sales_flat_creditmemo
// desc sales_flat_creditmemo;

$attribute = 'created_by';
Mage::helper('creditmemoreasons')->log(sprintf("%s->add attribute '%s' to 'creditmemo'", get_class($this), $attribute));

$installer->addAttribute(
    'creditmemo',
    $attribute,
    array(
        'type'      => 'varchar',
        'grid'      => true
    )
);


// example adding creait memo reason at the item leval
/*
$installer->addAttribute(
    'creditmemo_item',
    'reason',
    array(
        'type'      => 'int',
        'grid'      => true,
        'source'    => 'creditmemoreasons/system_config_source_reasons'
    )
);
*/

$installer->endSetup();
