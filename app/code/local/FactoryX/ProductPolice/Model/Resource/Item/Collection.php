<?php
/**
 * Who:  Alvin Nguyen
 * When: 2/10/2014
 * Why:  
 */

class FactoryX_ProductPolice_Model_Resource_Item_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract{
    public function _construct(){
        $this->_init('factoryx_productpolice/item');
    }

    /**
     * @param $attributesCodes
     * @return $this
     */
    public function addProductData($attributesCodes)
    {
        foreach ($attributesCodes as $attributeCode) {
            $attributeTableAlias = $attributeCode . '_table';
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $attributeCode);

            if ($attributeCode == "sku"){
                // SKU in a different table hence requires different handling
                $this->getSelect()->join(
                    array($attributeTableAlias => $attribute->getBackendTable()),
                    "main_table.product_id = {$attributeTableAlias}.entity_id",
                    array($attributeCode => 'sku')
                );
            }else{
                $this->getSelect()->join(
                    array($attributeTableAlias => $attribute->getBackendTable()),
                    "main_table.product_id = {$attributeTableAlias}.entity_id AND {$attributeTableAlias}.attribute_id={$attribute->getId()}",
                    array($attributeCode => 'value')
                );
            }

            $this->_map['fields'][$attributeCode] = 'value';
        }
        return $this;
    }
}