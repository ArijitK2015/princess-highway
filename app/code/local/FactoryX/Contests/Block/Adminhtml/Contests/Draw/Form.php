<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Draw_Form
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Draw_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form to draw the winners
	 */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                                        'id' => 'edit_form',
                                        'action' => $this->getUrl('*/*/drawWinner', array('id' => $this->getRequest()->getParam('id'))),
                                        'method' => 'post',
										'enctype' => 'multipart/form-data'
                                     )
        );
 
        $form->setUseContainer(true);
        $this->setForm($form);
		
        return parent::_prepareForm();
    }
	
}