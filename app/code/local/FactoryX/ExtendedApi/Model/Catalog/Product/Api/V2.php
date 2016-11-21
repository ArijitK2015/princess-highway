<?php

/**
 * Class FactoryX_ExtendedApi_Model_Catalog_Product_Api_V2
 */
class FactoryX_ExtendedApi_Model_Catalog_Product_Api_V2 extends FactoryX_ExtendedApi_Model_Catalog_Product_Api {

    /**
     * @param $product
     * @param null $groupPrices
     * @return array|null
     */
    public function prepareGroupPrices($product, $groupPrices = null) {
        if (!is_array($groupPrices)) {
            return null;
        }
        $updateValue = array();

        foreach ($groupPrices as $groupPrice) {
            if (!is_object($groupPrice)
                || !isset($groupPrice->price)) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Group Prices'));
            }

            if (!isset($groupPrice->website) || $groupPrice->website == 'all') {
                $groupPrice->website = 0;
            } else {
                try {
                    $groupPrice->website = Mage::app()->getWebsite($groupPrice->website)->getId();
                } catch (Mage_Core_Exception $e) {
                    $groupPrice->website = 0;
                }
            }

            if (intval($groupPrice->website) > 0 && !in_array($groupPrice->website, $product->getWebsiteIds())) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid group prices. The product is not associated to the requested website.'));
            }

            /*if (!isset($groupPrice->customer_group_id)) {
                $tierPrice->customer_group_id = 'all';
            }

            if ($tierPrice->customer_group_id == 'all') {
                $tierPrice->customer_group_id = Mage_Customer_Model_Group::CUST_GROUP_ALL;
            }*/

            $updateValue[] = array(
                'website_id' => $groupPrice->website,
                'cust_group' => $groupPrice->customer_group_id,
                'price'      => $groupPrice->price
            );
        }
        return $updateValue;
    }
}
