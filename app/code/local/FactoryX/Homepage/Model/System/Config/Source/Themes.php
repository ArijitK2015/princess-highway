<?php

class FactoryX_Homepage_Model_System_Config_Source_Themes
{
    public function toOptionArray()
    {
        return Mage::helper('homepage')->getAvailableDesigns();
    }
}