<?php
/**
 * iKantam
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade InstagramConnect to newer
 * versions in the future.
 *
 * @category    Ikantam
 * @package     FactoryX_Instagram
 * @copyright   Copyright (c) 2012 iKantam LLC (http://www.factoryx.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FactoryX_Instagram_Model_Instagramlist extends Mage_Core_Model_Abstract
{
    const UPDATE_TYPE_USER = 1;
    const UPDATE_TYPE_TAG = 0;
    const CACHE_TAG	= 'instagram_instagramlist';

    protected function _construct()
    {
       $this->_init("instagram/instagramlist");
    }

    /**
     * @return array
     */
    static public function getTypesOptionArray()
    {
        return array(
            self::UPDATE_TYPE_USER => Mage::helper('instagram')->__('By Users'),
            self::UPDATE_TYPE_TAG => Mage::helper('instagram')->__('By Tags')
        );
    }
}
