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
     * return group pricing info
     *
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
     * return parent info
     *
     * @param $productId
     * @param null $identifierType
     * @return array
     */
    public function parent($productId, $identifierType = null) {
        Mage::helper('extended_api')->log(sprintf("%s->productId: %s [%s]", __METHOD__, $productId, $identifierType));
        $_product = $this->_initProduct($productId, $identifierType);
        Mage::helper('extended_api')->log(sprintf("%s->product[%d]['type']=%s", __METHOD__, $_product->getId(), $_product->getTypeId()));
        $result = array();
        if ($_product->getTypeId() == "simple") {
            //$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($_product->getId());
            $parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getId());
            Mage::helper('extended_api')->log(sprintf("%s->parentIds: %s", __METHOD__, print_r($parentIds, true)) );
            foreach($parentIds as $id) {
                $result[] = $id;
            }
            //$result = $parentIds;
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
    
        $updatedGroupPrices = $this->_prepareGroupPrices($product, $groupPrices);
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
    private function _prepareGroupPrices($product, $groupPrices = null) {
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
        $storeId =  Mage::app()->getStore()->getId();
        if (!is_numeric($productId)) {
            $productId = strtolower($productId);
        }
        Mage::helper('extended_api')->log(sprintf("%s->productId: %s|%s [%s]", __METHOD__, $productId, $storeId, $identifierType));
        $product = Mage::helper('catalog/product')->getProduct($productId, $storeId);
        //$product = Mage::helper('catalog/product')->getProduct($productId, $storeId, $identifierType);
        Mage::helper('extended_api')->log(sprintf("%s->product: %s|%d", __METHOD__, get_class($product), $product->getId()));
        if (!$product->getId()) {
            $this->_fault('not_exists');
        }
        return $product;
    }
}
