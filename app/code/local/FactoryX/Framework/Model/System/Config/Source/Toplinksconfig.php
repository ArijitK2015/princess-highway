<?php

/**
 * Class FactoryX_Framework_Model_System_Config_Source_Toplinksconfig
 */
class FactoryX_Framework_Model_System_Config_Source_Toplinksconfig
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'badge',
                'label' => 'Show Badge'
            ),
            array(
                'value' => 'count',
                'label' => 'Show Items count'
            )
        );
    }
}