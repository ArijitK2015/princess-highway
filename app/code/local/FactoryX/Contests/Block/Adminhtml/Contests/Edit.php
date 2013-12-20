<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'contests';
		$this->_controller = 'adminhtml_contests';
		$this->_updateButton('save', 'label', Mage::helper('contests')->__('Save Contest'));
		$this->_updateButton('delete', 'label', Mage::helper('contests')->__('Delete Contest'));
		
		// Add the Save and Continue button
		$this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('contests')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
		
		$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";
			
		// If we're editing (not creating), we add the draw winner button
		if ($this->getRequest()->getParam('id')) 
		{
			// We first count if winners have already been drawn
			$winnersCount = Mage::getModel('contests/contest')->load($this->getRequest()->getParam('id'))->winnersCount();
			if ($winnersCount > 0)
			{
				// We display a confirmation message if there is some
				$message = Mage::helper('contests')->__('It seems like %s winners have been drawn for this contest, are you sure you want draw new winners ?', $winnersCount);
				$this->_addButton('draw', array(
					'label' => Mage::helper('contests')->__('Draw Winner(s)'),
					'onclick' => 'drawConfirmWinner(\''.$message.'\')',
					'class' => 'save',
						), -100);
				
				$this->_formScripts[] = "
					function drawConfirmWinner(message) {
						if( confirm(message) ) {
							$(editForm.formId).action = '" . $this->getDrawUrl() . "';
							editForm.submit();
						}
						return false;
					}";
			}
			else
			{
				$this->_addButton('draw', array(
					'label' => Mage::helper('contests')->__('Draw Winner(s)'),
					'onclick' => 'drawWinner()',
					'class' => 'save',
						), -100);
				
				$this->_formScripts[] = "
					function drawWinner() {
						$(editForm.formId).action = '" . $this->getDrawUrl() . "';
						editForm.submit();
					}
				";
			}
		}
	}
		
	public function getHeaderText()
	{
		if( Mage::registry('contests_data') && Mage::registry('contests_data')->getId() ) 
		{
			return Mage::helper('contests')->__("Editing Contest");
		} 
		else 
		{
			return Mage::helper('contests')->__('Add Contest');
		}
	}
	
	public function getDrawUrl()
    {
        return $this->getUrl('*/*/draw', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}