<?php

/**
 * Class FactoryX_AWAdvancedreportsMod_Model_Mysql4_Collection_Region
 */
class FactoryX_AWAdvancedreportsMod_Model_Mysql4_Collection_Region extends AW_Advancedreports_Model_Mysql4_Collection_Abstract
{
    /**
     * Add address data to Report Collection
     * @return FactoryX_AWAdvancedreportsMod_Model_Mysql4_Collection_Region
     */
    public function addAddress()
    {
        $salesFlatOrderAddress = Mage::helper('advancedreports/sql')->getTable('sales_flat_order_address');
        $this->getSelect()
            ->joinLeft(array('flat_order_addr_ship' => $salesFlatOrderAddress), "flat_order_addr_ship.parent_id = main_table.entity_id AND flat_order_addr_ship.address_type = 'shipping'", array())
            ->joinLeft(array('flat_order_addr_bill' => $salesFlatOrderAddress), "flat_order_addr_bill.parent_id = main_table.entity_id AND flat_order_addr_bill.address_type = 'billing'", array())
            ->columns(array('region_id' => 'IFNULL(flat_order_addr_ship.region_id, flat_order_addr_bill.region_id)'))
            ->group('IFNULL(flat_order_addr_ship.region_id, flat_order_addr_bill.region_id)');
        return $this;
    }

    /**
     * Add items to select request
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Region
     */
    public function addOrderItemsCount($isAllStores = false)
    {
        if ($isAllStores) {
            $currencyRate = "main_table.store_to_base_rate";
        } else {
            $currencyRate = new Zend_Db_Expr("'1'");
        }

        $itemTable = Mage::helper('advancedreports/sql')->getTable('sales_flat_order_item');

        $this->getSelect()
            ->join(array('item' => $itemTable), "(item.order_id = main_table.entity_id AND item.parent_item_id IS NULL)",
                array(
                    'sum_qty' => 'SUM(item.qty_ordered)',
                    'sum_total' => "SUM(item.base_row_total * $currencyRate)",
                ))
            ->where("main_table.entity_id = item.order_id");
        return $this;
    }
}
