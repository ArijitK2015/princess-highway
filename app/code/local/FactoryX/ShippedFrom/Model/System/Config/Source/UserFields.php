<?php

/**
 * Class FactoryX_ShippedFrom_Model_System_Config_Source_UserFields
 */
class FactoryX_ShippedFrom_Model_System_Config_Source_UserFields
    extends Varien_Object
{

    /**
     * @var array
     */
    protected $_options = array(
        'user_id'   => "User Id",
        'username'  => "User Name",
        'fullname'  => "User Full Name",
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
