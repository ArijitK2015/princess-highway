<?php

class FactoryX_Contests_Model_Status extends Varien_Object 
{
	const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
	const STATUS_AUTOMATIC = 2;

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
	
	public function getAutomaticStatusIds() 
	{
        return array(self::STATUS_AUTOMATIC);
    }

    static public function getOptionArray() 
	{
        return array(
            self::STATUS_ENABLED => Mage::helper('contests')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('contests')->__('Disabled'),
			self::STATUS_AUTOMATIC => Mage::helper('contests')->__('Automatic')
        );
    }

}
