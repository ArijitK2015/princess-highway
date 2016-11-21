<?php
/**
 */

class FactoryX_ShippedFrom_Model_System_Config_Source_Users extends Varien_Object {

    protected $options;

    /**
     * @param $args
     */
    public function generateOptions($args)
    {
        //Mage::log(sprintf("%s->row=%s", __METHOD__, print_r($args['row'], true)) );
        $id  = 0;
        if (array_key_exists('user_id', $args['row'])) {
            $id = $args['row']['user_id'];
        }
        else if (array_key_exists('id', $args['row'])) {
            $id = $args['row']['id'];
        }
        $this->options[] = array(
            'value' => $id,
            'label' => $args['row']['username']
        );
    }

    /**
     * @return array
     */
    public function toOptionArray() {

        $this->options = array(
            'user' => "Logged In User"
        );

        $users = Mage::getModel('admin/user')->getCollection()->setOrder('username', 'ASC');
        //Mage::log(sprintf("%s->sql=%s", __METHOD__, $users->getSelect()) );

        // Call iterator walk method with collection query string and callback method as parameters
        // Has to be used to handle massive collection instead of foreach
        Mage::getSingleton('core/resource_iterator')->walk($users->getSelect(), array(array($this, 'generateOptions')));

        return $this->options;
    }
}
