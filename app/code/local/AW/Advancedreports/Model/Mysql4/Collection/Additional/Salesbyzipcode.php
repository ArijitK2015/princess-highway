<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_ARUnits/Salesbyzipcode
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-COMMUNITY.txt
 */
class AW_Advancedreports_Model_Mysql4_Collection_Additional_Salesbyzipcode
    extends AW_Advancedreports_Model_Mysql4_Collection_Abstract
{
    /**
     * Add order item
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Additional_Salesbyzipcode
     */
    public function addOrderItems()
    {
        $filterField = $this->_helper()->confOrderDateFilter();
        if ($this->_helper()->checkSalesVersion('1.4.0.0')){
            $itemTable = $this->_helper()->getSql()->getTable('sales_flat_order_item');
            $this->getSelect()
                    ->join( array('item'=>$itemTable), "(item.order_id = main_table.entity_id AND item.parent_item_id IS NULL)" )
                    ->order("main_table.{$filterField} DESC")
                    ;
        } else {
            $itemTable = $this->_helper()->getSql()->getTable('sales_flat_order_item');
            $this->getSelect()
                    ->join( array('item'=>$itemTable), "(item.order_id = e.entity_id AND item.parent_item_id IS NULL)" )
                    ->order("e.{$filterField} DESC")
                    ;
        }
        return $this;
    }



    /**
     * Add zip info
     *
     * @return AW_Advancedreports_Model_Mysql4_Collection_Additional_Salesbyzipcode
     */
    public function addZipInfo()
    {
        if ($this->_helper()->checkSalesVersion('1.4.0.0')) {
            $salesFlatOrderAddress = $this->_helper()->getSql()->getTable('sales_flat_order_address');
            $this->getSelect()
                ->joinLeft(array('flat_order_addr_ship'=>$salesFlatOrderAddress), "flat_order_addr_ship.parent_id = main_table.entity_id AND flat_order_addr_ship.address_type = 'shipping'", array(
                        'shipping_postcode' => 'postcode',
                    ))
                ->joinLeft(array('flat_order_addr_bil'=>$salesFlatOrderAddress), "flat_order_addr_bil.parent_id = main_table.entity_id AND flat_order_addr_bil.address_type = 'billing'", array(
                        'billing_postcode' => 'postcode',
                    ))
                ;
        } else {
            $this
                ->joinAttribute('billing_postcode', 'order_address/postcode', 'billing_address_id', null, 'left')
                ->joinAttribute('shipping_postcode', 'order_address/postcode', 'shipping_address_id', null, 'left')
                ->addAttributeToSelect('billing_postcode')
                ->addAttributeToSelect('shipping_postcode')
                ;
        }
        return $this;
    }


    public function addOrderItemsCount()
    {
        $filterField = $this->_helper()->confOrderDateFilter();
        if ($this->_helper()->checkSalesVersion('1.4.0.0')) {
            $this->getSelect()
                   ->columns(array('qty_ordered_count'=>'total_qty_ordered'))
                   ;
        } else {
            $itemTable = $this->_helper()->getSql()->getTable('sales_flat_order_item');
            $this->getSelect()
                    ->joinRight( array('item'=>$itemTable), "(item.order_id = e.entity_id AND item.parent_item_id IS NULL)", array('qty_ordered_count'=>'COUNT(qty_ordered)'))
                    ->order("e.{$filterField} DESC")
                    ->group('e.entity_id')
                    ;
        }
        return $this;
    }

}