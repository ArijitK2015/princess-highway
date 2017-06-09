<?php
/**
 * FactoryX_AvaTax
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FactoryX
 * @package    FactoryX_AvaTax
 * @author     FactoryX Codemaster <codemaster@factoryx.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


$installer = $this;

$this->startSetup();

$installer->run("
	TRUNCATE {$this->getTable('imagecdn/cachedb')};
	ALTER TABLE {$this->getTable('imagecdn/cachedb')} CHANGE `url_hash` `url` VARCHAR(255) DEFAULT '' NOT NULL;
");

$this->endSetup();
