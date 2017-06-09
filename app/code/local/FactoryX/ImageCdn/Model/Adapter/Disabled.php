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
 * This class is used when the extension is disabled
 */
class FactoryX_ImageCdn_Model_Adapter_Disabled extends FactoryX_ImageCdn_Model_Adapter_Abstract
{
    /**
     * @param string $relFilename
     * @param string $tempfile
     * @return bool
     */
    protected function _save($relFilename, $tempfile) {
    	return false;
    }

    /**
     * @param string $relFilename
     * @return bool
     */
    protected function _remove($relFilename) {
    	return false;
    }

    /**
     * @return bool
     */
    protected function _clearCache() {
    	return false;
    }

    /**
     * @param $filename
     * @return bool
     */
    public function getUrl($filename) {
    	return false;
    }

    /**
     * @return bool
     */
    protected function _onConfigChange() {
		return false;
	}
    
}