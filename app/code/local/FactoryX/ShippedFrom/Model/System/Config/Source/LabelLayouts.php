<?php

/**
 * Class FactoryX_ShippedFrom_Model_System_Config_Source_LabelLayouts
 */
class FactoryX_ShippedFrom_Model_System_Config_Source_LabelLayouts
    extends Varien_Object
{
    /**
     * @var array
     */
    protected $_options = array(
        "A4-1pp"        => 'A4-1pp',
        "A4-3pp"        => 'A4-3pp',
        "A4-4pp"        => 'A4-4pp',
        "A6-1pp"        => 'A6-1pp'
    );

    /**
     * toOptionArray
     *
     * @return array $options
     */
    public function toOptionArray()
    {
        return $this->_options;
    }

}
