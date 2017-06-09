<?php

/**
 * Class FactoryX_Lookbook_Model_Status
 */
class FactoryX_Lookbook_Model_Status extends Varien_Object
{
	const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;

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
    static public function getOptionArray()
	{
        return array(
            self::STATUS_ENABLED => Mage::helper('lookbook')->__('Enabled'),
            self::STATUS_DISABLED => Mage::helper('lookbook')->__('Disabled')
        );
    }

}
