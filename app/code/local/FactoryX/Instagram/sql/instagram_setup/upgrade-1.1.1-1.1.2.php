<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2012 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer FactoryX_Instagram_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/** adding category attribute  */
$installer->removeAttribute('catalog_category', 'instagram_category_source_user');

/** adding category attribute */
$installer->removeAttribute('catalog_category', 'instagram_category_source');

/**
 * Create product attribute 'instagram_source_user'
 */
$installer->removeAttribute('catalog_product', 'instagram_source_user');

$installer->removeAttribute('catalog_product', 'instagram_source');

$installer->endSetup();
