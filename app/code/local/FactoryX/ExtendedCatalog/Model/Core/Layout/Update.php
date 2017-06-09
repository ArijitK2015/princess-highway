<?php

/**
 * This rewrite modifies the caching behavior so that the layout cache key references a SHA1
 * hash of the layout XML instead of the XML itself to avoid duplication. The layout XML must
 * still be generated once for each layout key, but it will not be saved if the identical
 * contents already exist, saving considerable cache backend storage.
 */
class FactoryX_ExtendedCatalog_Model_Core_Layout_Update extends Mage_Core_Model_Layout_Update
{

    const XML_KEY_PREFIX = 'XML_';

    public function loadCache()
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }

        if (!$result = Mage::app()->loadCache($this->getCacheId())) {
            return false;
        }

        // BEGIN CODE ADDED
        if (strlen($result) === 40) { // sha1
            if (!$result = Mage::app()->loadCache(self::XML_KEY_PREFIX . $result)) {
                return false;
            }
        }
        // END CODE ADDED

        $this->addUpdate($result);

        return true;
    }

    public function saveCache()
    {
        if (!Mage::app()->useCache('layout')) {
            return false;
        }
        $str = $this->asString();
        $hash = sha1($str);
        $tags = $this->getHandles();
        $tags[] = self::LAYOUT_GENERAL_CACHE_TAG;
        Mage::app()->saveCache($hash, $this->getCacheId(), $tags, null);
        if ( ! Mage::app()->getCache()->test(self::XML_KEY_PREFIX . $hash)) {
            Mage::app()->saveCache($str, self::XML_KEY_PREFIX . $hash, $tags, null);
        }
        return TRUE;
    }

}