<?php

/**
 * Class FactoryX_CreditmemoReasons_Model_System_Config_Source_Admins
 */
class FactoryX_CreditmemoReasons_Model_System_Config_Source_Admins  {

    /**
     * @return array
     */
    public function toOptionArray() {
        // Get admin users
        $model = Mage::getModel("admin/user");
        $admins = $model->getCollection();
        $options = array();
        // Mage_Admin_Model_User
        foreach($admins as $admin) {
            $name = sprintf("%s %s", $admin->getFirstname(), $admin->getLastname());
            //Mage::helper('creditmemoreasons')->log(sprintf("%s->admin %s:%s", __METHOD__, $admin->getUsername(), $name) );
            $options[strtolower($admin->getUsername())] = $name;
            //$options[strtolower($admin->getUsername())] = $admin->getUsername();
            /*
            $options[] = array(
                'value' => strtolower($admin->getUsername()),
                'label' => $name
            );
            */
        }
        //Mage::helper('creditmemoreasons')->log(sprintf("%s->options: %s", __METHOD__, print_r($options, true)) );
        return $options;
    }

    /**
     * @param string $valueField
     * @param string $labelField
     * @return array
     */
    public function toOptionHash($valueField='id', $labelField='name') {
        $options = array();
        $admins = Mage::getModel("admin/user")->getCollection()->setOrder($labelField, 'ASC');
        foreach ($admins as $admin) {
            $options[$admin->getData($valueField)] = $admin->getData($labelField);
        }
        //Mage::helper('creditmemoreasons')->log(sprintf("%s->options: %s", __METHOD__, print_r($options, true)) );
        return $options;
    }


}