<?php

class FactoryX_Homepage_Model_Status extends Varien_Object 
{
	const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
	// const STATUS_AUTOMATIC = 2;

    public function addEnabledFilterToCollection($collection) 
	{
        $collection->addStatusFilter(array('in' => $this->getEnabledStatusIds()));
        return $this;
    }

    public function getEnabledStatusIds() 
	{
        return array(self::STATUS_ENABLED);
    }

    public function getDisabledStatusIds() 
	{
        return array(self::STATUS_DISABLED);
    }
	
	/*
	public function getAutomaticStatusIds() 
	{
        return array(self::STATUS_AUTOMATIC);
    }
	*/

    static public function getOptionArray() 
	{
        return array(
            self::STATUS_ENABLED => Mage::helper('homepage')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('homepage')->__('Disabled'),
			// self::STATUS_AUTOMATIC => Mage::helper('homepage')->__('Automatic')
        );
    }

}
