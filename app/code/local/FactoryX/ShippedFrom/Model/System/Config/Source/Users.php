<?php

/**
 * Class FactoryX_ShippedFrom_Model_System_Config_Source_Users
 */
class FactoryX_ShippedFrom_Model_System_Config_Source_Users
    extends Varien_Object
{

    /**
     * @var
     */
    protected $_options;

    /**
     * @param $args
     */
    public function generateOptions($args)
    {
        $id  = 0;
        if (array_key_exists('user_id', $args['row'])) {
            $id = $args['row']['user_id'];
        } elseif (array_key_exists('id', $args['row'])) {
            $id = $args['row']['id'];
        }

        $this->_options[] = array(
            'value' => $id,
            'label' => $args['row']['username']
        );
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options = array(
            'user' => "Logged In User"
        );

        $users = Mage::getModel('admin/user')->getCollection()->setOrder('username', 'ASC');

        // Call iterator walk method with collection query string and callback method as parameters
        // Has to be used to handle massive collection instead of foreach
        Mage::getSingleton('core/resource_iterator')->walk($users->getSelect(), array(array($this, 'generateOptions')));

        return $this->_options;
    }
}
