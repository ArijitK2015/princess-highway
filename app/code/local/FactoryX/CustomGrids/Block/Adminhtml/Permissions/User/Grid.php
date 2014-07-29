<?php
/*
 *	Add store to the user permissions grid
 */ 
class FactoryX_CustomGrids_Block_Adminhtml_Permissions_User_Grid extends Mage_Adminhtml_Block_Permissions_User_Grid
{

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('admin/user_collection');

        // Get Store Name
        $collection->getSelect()->joinLeft(
            array('ustore' => 'ustorelocator_location'),
            'main_table.store = ustore.location_id',
            array('ustore.title')
        );

        $this->setCollection($collection);
        return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('user_id', array(
            'header'    => Mage::helper('adminhtml')->__('ID'),
            'width'     => 5,
            'align'     => 'right',
            'sortable'  => true,
            'index'     => 'user_id'
        ));

        $this->addColumn('username', array(
            'header'    => Mage::helper('adminhtml')->__('User Name'),
            'index'     => 'username'
        ));

        $this->addColumn('firstname', array(
            'header'    => Mage::helper('adminhtml')->__('First Name'),
            'index'     => 'firstname'
        ));

        $this->addColumn('lastname', array(
            'header'    => Mage::helper('adminhtml')->__('Last Name'),
            'index'     => 'lastname'
        ));

        // Get Stores
        $collection = Mage::getModel('ustorelocator/location')->getCollection()->addAttributeToSort('title','ASC');
        $store_array = array();
        foreach ($collection as $store)
		{            
            $store_array[$store->getId()] = Mage::helper('adminhtml')->__($store->getTitle());
        }

        $this->addColumn('title', array(
            'header'    => Mage::helper('adminhtml')->__('Store'),
            'width'     => 400,
            'align'     => 'left',
            'index'     => 'title',
            'type'      => 'options',
            'options'   =>  $store_array
        ));

        $this->addColumn('email', array(
            'header'    => Mage::helper('adminhtml')->__('Email'),
            'width'     => 40,
            'align'     => 'left',
            'index'     => 'email'
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('adminhtml')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array('1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')),
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }
}
