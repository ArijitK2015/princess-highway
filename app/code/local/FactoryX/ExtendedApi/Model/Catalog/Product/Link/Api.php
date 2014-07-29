<?php
/**
 *	Customization to retrieve the associated products (see items method)
 */
class FactoryX_ExtendedApi_Model_Catalog_Product_Link_Api extends Mage_Catalog_Model_Product_Link_Api
{
    /**
     * Retrieve product link associations
     *
     * @param string $type
     * @param int|sku $productId
     * @param  string $identifierType
     * @return array
     */
    public function items($type, $productId, $identifierType = null) {
		if ($type == "associated") 
		{
			$product = $this->_initProduct($productId, $identifierType);
			try 
			{
				$result = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null,$product);
			}
			catch (Exception $e) 
			{
				$this->_fault('data_invalid', Mage::helper('catalog')->__('The product is not configurable.'));
			}			
		}
		else 
		{
			$typeId = $this->_getTypeId($type);
			$product = $this->_initProduct($productId, $identifierType);
			$link = $product->getLinkInstance()->setLinkTypeId($typeId);
			$collection = $this->_initCollection($link, $product);
			$result = array();
			foreach ($collection as $linkedProduct) {
				$row = array(
						'product_id' => $linkedProduct->getId(),
						'type'       => $linkedProduct->getTypeId(),
						'set'        => $linkedProduct->getAttributeSetId(),
						'sku'        => $linkedProduct->getSku()
				);
			
				foreach ($link->getAttributes() as $attribute) {
					$row[$attribute['code']] = $linkedProduct->getData($attribute['code']);
				}
			
				$result[] = $row;
			}
		}
        return $result;
    }

    /**
     * Initialize and return product model
     *
     * @param int $productId
     * @param  string $identifierType
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct($productId, $identifierType = null)
    {
        $loadByIdOnFalse = false;
        if ($identifierType === null) {
            $identifierType = 'sku';
            $loadByIdOnFalse = true;
        }
        $product = Mage::getModel('catalog/product')
            ->setStoreId($this->_getStoreId());
        if ($identifierType == 'sku') {
            $idBySku = $product->getIdBySku($productId);
            if ($idBySku) {
                $productId = $idBySku;
            }
            if ($idBySku || $loadByIdOnFalse) {
                $product->load($productId);
            }
        } elseif ($identifierType == 'id') {
            $product->load($productId);
        }

        if (!$product->getId()) {
            $this->_fault('product_not_exists');
        }

        return $product;
    }
}