<?php
/**
 */

class FactoryX_PickList_Model_System_Config_Source_Stores extends Varien_Object {

    /**
     * @return array
     */
    public function toOptionArray() {

        $options = array();

        if ((string)Mage::getConfig()->getModuleConfig('Unirgy_StoreLocator')->active == 'true')
        {
            $stores = Mage::getModel('ustorelocator/location')->getCollection()
                ->setOrder('region', 'ASC')
                ->setOrder('store_code', 'ASC');

            foreach ($stores as $store) {
                $code = strtolower($store->getData('store_code'));
                $region = $store->getData('region');
                if (empty($region)) {
                    $region = "?";
                }
                $options[] = array('value' => $code, 'label' => sprintf("%s - %s", $region, $code));
            }
        }
        
        return $options;
    }
}
