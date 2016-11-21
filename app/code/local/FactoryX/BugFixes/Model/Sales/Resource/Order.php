<?php

/**
 * Fix a bug where the middlename being empty would break the search in the orders grid: http://magento.stackexchange.com/questions/80196/magento-1-9-2-0-table-sales-flat-order-grid-contains-extra-space-in-customer
 * Class FactoryX_BugFixes_Model_Sales_Resource_Order
 */
class FactoryX_BugFixes_Model_Sales_Resource_Order extends Mage_Sales_Model_Resource_Order
{
    /**
     * @return Mage_Sales_Model_Resource_Order
     */
    protected function _initVirtualGridColumns()
    {
        if (version_compare(Mage::getVersion(),"1.9.2.0","<")) {
            return parent::_initVirtualGridColumns();
        } else {
            Mage_Sales_Model_Resource_Order_Abstract::_initVirtualGridColumns();
            $adapter       = $this->getReadConnection();
            $ifnullFirst   = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
            $ifnullMiddle  = $adapter->getIfNullSql('{{table}}.middlename', $adapter->quote(''));
            $ifnullLast    = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
            $concatAddress = $adapter->getConcatSql(array(
                $ifnullFirst,
                $adapter->quote(' '),
                $ifnullMiddle,
                new Zend_Db_Expr('IF({{table}}.middlename IS NULL OR {{table}}.middlename="", "", " ")'),
                $ifnullLast
            ));
            $this->addVirtualGridColumn(
                'billing_name',
                'sales/order_address',
                array('billing_address_id' => 'entity_id'),
                $concatAddress
            )
                ->addVirtualGridColumn(
                    'shipping_name',
                    'sales/order_address',
                    array('shipping_address_id' => 'entity_id'),
                    $concatAddress
                );

            return $this;
        }
    }
}