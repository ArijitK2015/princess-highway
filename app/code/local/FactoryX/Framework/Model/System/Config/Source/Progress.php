<?php

/**
 * Class FactoryX_Framework_Model_System_Config_Source_Progress
 */
class FactoryX_Framework_Model_System_Config_Source_Progress
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage::getStoreConfig('framework/options/progress_checkout_enable'),
                'label' => 'No Progress Bar'
            ),
            array(
                'value' => 'bar',
                'label' => 'Progress Bar'
            ),
            array(
                'value' => 'checkbox',
                'label' => 'Progress Checkboxes'
            )
        );
    }
}