<?php

class FactoryX_Lookbook_Model_Status extends Varien_Object 
{
	const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

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

    static public function getOptionArray() 
	{
        return array(
            self::STATUS_ENABLED => Mage::helper('lookbook')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('lookbook')->__('Disabled')
        );
    }

}
