<?php

/**
 * Class FactoryX_Lookbook_Model_Lookbook_Media
 */
class FactoryX_Lookbook_Model_Lookbook_Media extends Mage_Core_Model_Abstract
{
	/**
	 *	Constructor for the lookbook media model
	 */
    public function _construct()
    {
        parent::_construct();
        $this->_init('lookbook/lookbook_media');
    }

    /**
     * Remove images if a media object is deleted.
     *
     * @return $this
     */
    public function delete()
    {
        $filepath = Mage::getSingleton('lookbook/lookbook_media_config')
            ->getMediaPath($this->getPath());

        $result = parent::delete();

        unlink($filepath);
        
        return $result;
    }

    /**
     * Process media data before saving (ie adding creation en update time.
     *
     * @internal param Mage_Core_Model_Abstract $object
     * @return Mage_Cms_Model_Resource_Page
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        // Modify create / update dates
        if ($this->isObjectNew() && !$this->hasCreationTime()) 
		{
            $this->setCreationTime(Mage::getSingleton('core/date')->gmtDate());
        }

        $this->setUpdateTime(Mage::getSingleton('core/date')->gmtDate());

        return $this;
    }
}