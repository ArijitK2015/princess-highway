<?php

$installer = $this;

$installer->startSetup();

try {

    $installer->run("
      CREATE TABLE {$this->getTable('careers/careers')} (
      `careers_id` int(11) unsigned NOT NULL auto_increment,
      `position` varchar(255) NOT NULL default '',
      `status` smallint(6) NOT NULL default '0',
      `hours` varchar(255) NOT NULL default '',
      `entitlements` varchar(255) NOT NULL default '',
      `email` varchar(255) NOT NULL default '',
      `sort` int(11) NOT NULL default '0',
      `requirements` text NOT NULL default '',
      `countrys` varchar(255) NOT NULL default '',
      `locations` varchar(255) NOT NULL default '',
      `statuss` text NOT NULL default '',
      `created_time` datetime NULL,
      `update_time` datetime NULL,
      PRIMARY KEY (`careers_id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    if ($installer->getConnection()->isTableExists($this->getTable('careers/old_careers')))
    {
        $oldTable = $this->getTable('careers/old_careers');
    }
    elseif ($installer->getConnection()->isTableExists($this->getTable('careers/old_careers_lowercase')))
    {
        $oldTable = $this->getTable('careers/old_careers_lowercase');
    }

    $installer->run("
      INSERT INTO {$oldTable} SELECT Jobs_id, position, status, hours, entitlements, email, 0, requirements, countrys, locations, statuss, created_time, update_time FROM {$this->getTable('careers/old_careers')};

      DROP TABLE IF EXISTS {$oldTable};
    ");

}
catch(Exception $e)
{
    Mage::logException($e);
}

$installer->endSetup();