<?php
 
class FactoryX_Careers_Block_Adminhtml_Careers extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_Careers';
        $this->_blockGroup = 'careers';
        $this->_headerText = Mage::helper('careers')->__('Careers Manager');


        $this->_addButton('btn_careers_view', array(
            'label'     => Mage::helper('careers')->__('CMS Career Image'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/editCareerPage/btn/0') . '\')',
            'class'     => 'edit',
        ),-1,1);

        $this->_addButton('btn_careers_list', array(
            'label'     => Mage::helper('careers')->__('CMS List Image'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/editCareerPage/btn/1') . '\')',
            'class'     => 'edit',
        ),-1,2);

        $this->_addButton('btn_careers_page', array(
            'label'     => Mage::helper('careers')->__('CMS Detail Image'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/editCareerPage/btn/2') . '\')',
            'class'     => 'edit',
        ),-1,3);

        $this->_addButtonLabel = Mage::helper('careers')->__('Add Careers');
        parent::__construct();
    }
}