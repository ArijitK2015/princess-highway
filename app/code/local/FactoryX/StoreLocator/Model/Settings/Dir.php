<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pp
 * Date: 11-10-21
 * Time: 0:47
 */
 
class FactoryX_StoreLocator_Model_Settings_Dir
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>'asc', 'label'=>Mage::helper('ustorelocator')->__('Ascending')),
            array('value'=>'desc', 'label'=>Mage::helper('ustorelocator')->__('Descending')),
        );
    }
}
