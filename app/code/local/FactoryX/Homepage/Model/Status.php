<?php

/**
 * Class FactoryX_Homepage_Model_Status
 */
class FactoryX_Homepage_Model_Status extends Varien_Object
{
	const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
	const STATUS_AUTOMATIC = 2;

    /**
     * @param $collection
     * @return $this
     */
    public function addEnabledFilterToCollection($collection)
	{
        $collection->addStatusFilter(array('in' => $this->getEnabledStatusIds()));
        return $this;
    }

    /**
     * @return array
     */
    public function getEnabledStatusIds()
	{
        return array(self::STATUS_ENABLED);
    }

    /**
     * @return array
     */
    public function getDisabledStatusIds()
	{
        return array(self::STATUS_DISABLED);
    }

    /**
     * @return array
     */
    public function getAutomaticStatusIds()
	{
        return array(self::STATUS_AUTOMATIC);
    }

    /**
     * @return array
     */
    static public function getOptionArray()
	{
        return array(
            self::STATUS_ENABLED => Mage::helper('homepage')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('homepage')->__('Disabled'),
			self::STATUS_AUTOMATIC => Mage::helper('homepage')->__('Automatic')
        );
    }

}
