<?php

class FactoryX_Homepage_Model_System_Config_Source_Position
{
    public function toOptionArray()
    {
        return array(
            array(
                'label' => Mage::helper('homepage')->__('Before Original Content'),
                'value' => 'before'
            ),
            array(
                'label' => Mage::helper('homepage')->__('After Original Content'),
                'value' => 'after'
            )
        );
    }
}