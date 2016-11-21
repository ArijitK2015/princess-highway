<?php

$installer = $this;
$installer->startSetup();

$installer->addAttribute(
    'creditmemo',
    'reason',
    array(
        'type' => 'varchar',
        'grid' => true,
        'source' => 'creditmemoreasons/system_config_source_reasons'
    )
);

$installer->endSetup();