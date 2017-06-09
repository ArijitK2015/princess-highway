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
 * Observers.
 */
class FactoryX_ImageCdn_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Observer to clean the cache every so often since there could be URLs that aren't
     * being used anymore. Without this, those URLs would never leave the cache.
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function cleanCache(Mage_Cron_Model_Schedule $schedule) {
    	Mage::getSingleton('imagecdn/cache')->cleanCache();
    }
    
	/**
	 * When an admin config setting related to the this extension is changed, the cache
	 * must be cleared because the cache usually isn't relevant anymore. For example, if
	 * a user change the CDN type from Amazon S3 to FTP, the cache for Amazon shouldn't
	 * be used for the FTP CDN. The method also runs the adapter specific method as well.
	 *
	 * @param Mage_Cron_Model_Schedule $schedule
	 * @return bool
	 */
    public function onConfigChange($schedule) {
    	Mage::getSingleton('imagecdn/cache')->clearCache();
    	return Mage::Helper('imagecdn')->factory()->onConfigChange();
    }

    /**
     * @param $schedule
     */
    public function reupload(Mage_Cron_Model_Schedule $schedule)
    {
        if (Mage::getStoreConfig('imagecdn/cronjob/status')){
            $ids = Mage::getResourceModel('imagecdn/cachedb_collection')->addFieldToFilter('http_code', '403');
            foreach ($ids as $id) {
                $cachedb = Mage::getModel('imagecdn/cachedb')->load($id->getId());
                if ($cachedb) {
                    $cachedb->reupload();
                }
            }
        }
    }
}
