<?php

$installer = $this;

$installer->startSetup();

$installer->run("

        ALTER TABLE  {$this->getTable('sales_flat_order')}
        ADD  `easyreview_notified` TINYINT NOT NULL DEFAULT  '0',
        ADD easyreview_hashcode VARCHAR(50);

");

$installer->endSetup();
