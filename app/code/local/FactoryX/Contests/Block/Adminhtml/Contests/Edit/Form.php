<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the edit contest page
	 */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                                        'id' => 'edit_form',
                                        'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                        'method' => 'post',
										'enctype' => 'multipart/form-data'
                                     )
        );
 
        $form->setUseContainer(true);
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
	
}