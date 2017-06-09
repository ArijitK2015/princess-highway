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

/**
 * ImageCDN extention to use custom GD lib
 */
class FactoryX_ImageCdn_Model_Varien_Image extends Varien_Image
{
    /**
     * Hijacks the normal method to add ImageCDN hooks. Fails back to parent method
     * as appropriate.
     *
     * @param null $adapter
     * @return none
     * @internal param string $destination
     * @internal param string $newName
     */
    protected function _getAdapter($adapter=null)
    {    	
        if( !isset($this->_adapter) ) {
            $this->_adapter = Mage::getModel('imagecdn/varien_gd2');
        }
        return $this->_adapter;
    }
}
