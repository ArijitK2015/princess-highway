<?php
/**
 * FactoryX_ImageCdn
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FactoryX
 * @package    FactoryX_ImageCdn
 * @author     FactoryX Codemaster <codemaster@factoryx.com>
 * @copyright  Copyright (c) 2009 One Pica, Inc.
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class FactoryX_ImageCdn_Model_Mysql4_Cachedb extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('imagecdn/cachedb', 'cachedb_id');
    }
}
