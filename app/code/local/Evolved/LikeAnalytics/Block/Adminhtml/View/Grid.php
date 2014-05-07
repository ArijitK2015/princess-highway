<?php

/**
 * Retail Evolved - Facebook Like Analytics
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled with this
 * package in the file EVOLVED_EULA.txt.
 * It is also available through the world-wide-web at this URL:
 * http://retailevolved.com/eula-1-0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to service@retailevolved.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * You may edit this file, but only at your own risk, as long as it is within
 * the constraints of the license agreement. Before upgrading the module (not Magento), 
 * be sure to back up your original installation as the upgrade may override your
 * changes.
 *
 * @category   Evolved
 * @package    Evolved_LikeAnalytics
 * @copyright  Copyright (c) 2010 Kaelex Inc. DBA Retail Evolved (http://retailevolved.com)
 * @license    http://retailevolved.com/eula-1-0 (Retail Evolved EULA 1.0)
 */

class Evolved_LikeAnalytics_Block_Adminhtml_View_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('viewGrid');
		$this->setDefaultSort('total_count');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getModel('evlikeanalytics/stat')->getCollection();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('url', array(
			'header'	=> Mage::helper('evlikeanalytics')->__('URL'),
			'align'		=> 'left',
			'width'		=> '100px',
			'index'		=> 'url',
		));
		
		$this->addColumn('description', array(
			'header'	=> Mage::helper('evlikeanalytics')->__('Description'),
			'align'		=> 'left',
			'width'		=> '100px',
			'index'		=> 'description',
		));
		
		$this->addColumn('share_count', array (
			'header'	=> Mage::helper('evlikeanalytics')->__('Shares'),
			'align'		=> 'right',
			'width'		=> '30px',
			'index'		=> 'share_count',
		));
		
		$this->addColumn('like_count', array (
			'header'	=> Mage::helper('evlikeanalytics')->__('Likes'),
			'align'		=> 'right',
			'width'		=> '30px',
			'index'		=> 'like_count',
		));
		
		$this->addColumn('comment_count', array (
			'header'	=> Mage::helper('evlikeanalytics')->__('Comments'),
			'align'		=> 'right',
			'width'		=> '30px',
			'index'		=> 'comment_count',
		));
		
		$this->addColumn('total_count', array (
			'header'	=> Mage::helper('evlikeanalytics')->__('Total'),
			'align'		=> 'right',
			'width'		=> '30px',
			'index'		=> 'total_count',
		));
		
		$this->addColumn('click_count', array (
			'header'	=> Mage::helper('evlikeanalytics')->__('Clicks'),
			'align'		=> 'right',
			'width'		=> '30px',
			'index'		=> 'click_count',
		));
		
		$this->addColumn('update_time', array(
			'header'	=> Mage::helper('evlikeanalytics')->__('Updated'),
			'align'		=> 'left',
			'width'		=> '80px',
			'index'		=> 'update_time',
			'type'		=> 'datetime'
		));
		
		$this->addColumn('page_actions', array(
            'header'    => Mage::helper('evlikeanalytics')->__('Action'),
            'width'     => 10,
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'evlikeanalytics/adminhtml_view_grid_renderer_action',
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('evlikeanalytics')->__('CSV'));
		
		return parent::_prepareColumns();
	}
	
	public function setFilterType($type) {
		$this->setFilterType($type);
		return $this;
	}
}
