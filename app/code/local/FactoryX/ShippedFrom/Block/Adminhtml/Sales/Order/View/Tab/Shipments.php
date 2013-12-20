<?php
/**
 * Order Shipments grid
 *
 * @category   Mage
 * @package    FactoryX_ShippedFrom
 * @author     Factory X Team <raphael@factoryx.com.au>
 */
class FactoryX_ShippedFrom_Block_Adminhtml_Sales_Order_View_Tab_Shipments
	extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Shipments
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    protected function _prepareCollection() {
    	
         $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('entity_id')
            ->addFieldToSelect('created_at')
            ->addFieldToSelect('increment_id')
            ->addFieldToSelect('total_qty')
            ->addFieldToSelect('shipping_name')
			->addFieldToSelect('shipped_from')
			->addFieldToSelect('shipped_by')
            ->setOrderFilter($this->getOrder())
        ;
        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns() {
		
		$this->addColumn('increment_id', array(
            'header' => Mage::helper('sales')->__('Shipment #'),
            'index' => 'increment_id',
        ));

        $this->addColumn('shipping_name', array(
            'header' => Mage::helper('sales')->__('Ship to Name'),
            'index' => 'shipping_name',
        ));

        $this->addColumn('created_at', array(
            'header' => Mage::helper('sales')->__('Date Shipped'),
            'index' => 'created_at',
            'type' => 'datetime',
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'type'  => 'number',
        ));
		
		$this->addColumn('shipped_from', array(
            'header' => Mage::helper('sales')->__('Shipped From'),
            'index' => 'shipped_from',
			'type' => 'options',
			'options' => Mage::helper('shippedfrom')->getStores()
        ));

		$this->addColumn('shipped_by', array(
            'header' => Mage::helper('sales')->__('Shipped By'),
            'index' => 'shipped_by',
			'type' => 'options',
			'options' => Mage::helper('shippedfrom')->getUsers(true)
        ));
		
		return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();

    }
			
}
