<?php

/**
 * Class FactoryX_StoreLocator_Model_Api
 */
class FactoryX_StoreLocator_Model_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * @param null $filters
     * @return array
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('ustorelocator/location')->getCollection();

        $result = array();
        foreach ($collection as $location) {
            $result[] = $location->toArray();
        }
        return $result;
    }
}