<?php

/**
 * Class FactoryX_Instagram_Model_Options
 */
class FactoryX_Instagram_Model_Options{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $array = array();
        $collection = Mage::getResourceModel('instagram/instagramlist_collection');
        foreach ($collection as $list)
        {
            $array[] = array('value'    => $list->getListId(), 'label'  => $list->getTitle());
        }
        return $array;
    }
}