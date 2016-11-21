<?php

/**
 * Class FactoryX_ExtendedApi_Model_Catalog_Product_Api
 */
class FactoryX_ExtendedApi_Model_Catalog_Product_Api extends Mage_Api_Model_Resource_Abstract {
    
    const ATTRIBUTE_CODE = 'group_price';

    /**
     *
     */
    public function __construct() {
        $this->_storeIdSessionField = 'product_store_id';
    }

    /**
     * @param $productId
     * @param null $identifierType
     * @return array
     */
    public function info($productId, $identifierType = null) {
        $product = $this->_initProduct($productId, $identifierType);
        $groupPrices = $product->getData(self::ATTRIBUTE_CODE);
        
        if (!is_array($groupPrices)) {
            return array();
        }
        
        $result = array();
        
        foreach ($groupPrices as $groupPrice) {
            $row = array();
            $row['customer_group_id'] = $groupPrice['cust_group'];
            $row['website'] = ($groupPrice['website_id'] ?
                Mage::app()->getWebsite($groupPrice['website_id'])->getCode() :
                'all'
            );
            $row['price'] = $groupPrice['price'];
            $result[] = $row;
        }
        return $result;
    }


    /**
     * @param $productId
     * @param $groupPrices
     * @param null $identifierType
     * @return bool
     */
    public function update($productId, $groupPrices, $identifierType = null) {
        $product = $this->_initProduct($productId, $identifierType);
    
        $updatedGroupPrices = $this->prepareGroupPrices($product, $groupPrices);
        if (is_null($updatedGroupPrices)) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Group Prices'));
        }
        
        $product->setData(self::ATTRIBUTE_CODE, $updatedGroupPrices);
        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach($errors as $code=>$error) {
                    $strErrors[] = ($error === true)? Mage::helper('catalog')->__('Value for "%s" is invalid.', $code) : Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $code, $error);
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }
            $product->save();
        }
        catch (Mage_Core_Exception $e) {
            $this->_fault('not_updated', $e->getMessage());
        }
        return true;
    }

    /**
     * @param $product
     * @param null $groupPrices
     * @return array|null
     */
    public function prepareGroupPrices($product, $groupPrices = null) {
        if (!is_array($groupPrices)) {
            return null;
        }
        
        if (!is_array($groupPrices)) {
            $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Group Prices'));
        }
        
        $updateValue = array();
        
        foreach ($groupPrices as $groupPrice) {
            if (!is_array($groupPrice)
                || !isset($groupPrice['price'])) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid Group Prices'));
            }
        
            if (!isset($groupPrice['website']) || $groupPrice['website'] == 'all') {
                $groupPrice['website'] = 0;
            }
            else {
                try {
                    $groupPrice['website'] = Mage::app()->getWebsite($groupPrice['website'])->getId();
                }
                catch (Mage_Core_Exception $e) {
                    $groupPrice['website'] = 0;
                }
            }
            if (intval($groupPrice['website']) > 0 && !in_array($groupPrice['website'], $product->getWebsiteIds())) {
                $this->_fault('data_invalid', Mage::helper('catalog')->__('Invalid group prices. The product is not associated to the requested website.'));
            }
            $updateValue[] = array(
                'website_id' => $groupPrice['website'],
                'cust_group' => $groupPrice['customer_group_id'],
                'price'      => $groupPrice['price']
            );
        }
        return $updateValue;
    }

    /**
     * @param $productId
     * @param null $identifierType
     * @return mixed
     */
    protected function _initProduct($productId, $identifierType = null) {
        $product = Mage::helper('catalog/product')->getProduct($productId, 0, $identifierType);
        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }        
        return $product;
    }
}
