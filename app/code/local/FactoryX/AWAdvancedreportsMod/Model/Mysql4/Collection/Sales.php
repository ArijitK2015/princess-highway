<?php

/**
 * Class FactoryX_AWAdvancedreportsMod_Model_Mysql4_Collection_Sales
 */
class FactoryX_AWAdvancedreportsMod_Model_Mysql4_Collection_Sales extends AW_Advancedreports_Model_Mysql4_Collection_Sales
{
    /**
     * Add product info price
     * @return AW_Advancedreports_Model_Mysql4_Collection_Sales
     */
    public function addOriginalPrice()
    {
        $entityProduct = Mage::helper('advancedreports/sql')->getTable('catalog_product_entity');

        $entityAtribute = Mage::helper('advancedreports/sql')->getTable('eav_attribute');

        $entityValuesDecimal = Mage::helper('advancedreports/sql')->getTable('catalog_product_entity_decimal');

        $this->getSelect()
            ->join(
                array('_product2' => $entityProduct),
                "_product2.entity_id = item.product_id",
                array('p_product_id' => 'item.product_id')
            )
            ->joinLeft(
                array('_priceAttr' => $entityAtribute),
                "_priceAttr.attribute_code = 'price'",
                array()
            )
            ->joinRight(
                array('_priceValDecimal' => $entityValuesDecimal),
                "_priceValDecimal.attribute_id = _priceAttr.attribute_id AND _priceValDecimal.entity_id = _product2.entity_id",
                array("product_price" => 'value')
            );

        return $this;
    }
}
