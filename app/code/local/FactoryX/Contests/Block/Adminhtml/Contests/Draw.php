<?php

class FactoryX_Contests_Block_Adminhtml_Contests_Draw extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *	Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'contests';
		$this->_controller = 'adminhtml_contests';
		// Remove useless buttons
		$this->_removeButton('save');
		$this->_removeButton('delete');
			
		// Add the draw a winner button if we're editing (hidden if new contest page)
		if ($this->getRequest()->getParam('id')) 
		{
			$this->_addButton('draw', array(
				'label' => Mage::helper('contests')->__('Draw Winner(s)'),
				'onclick' => 'drawWinner()',
				'class' => 'save',
					), -100);
			
			$this->_formScripts[] = "
				function drawWinner() {
					$(editForm.formId).action = '" . $this->getDrawWinnerUrl() . "';
					editForm.submit();
				}
			";
		}
	}
	
	public function getHeaderText()
	{
		return Mage::helper('contests')->__('Drawing Winner(s)');
	}
	
	public function getDrawWinnerUrl()
    {
        return $this->getUrl('*/*/drawWinner', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}