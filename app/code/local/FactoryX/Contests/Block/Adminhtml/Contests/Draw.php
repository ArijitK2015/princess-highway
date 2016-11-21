<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Draw
 */
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
			
			// Add some JS to hide/show fields
			$this->_formScripts[] = "
				Event.observe(window, 'load', function(){
					if ($('state_enable').selectedIndex == 0)
					{
						$('state').parentNode.parentNode.hide();
						$('one_per_state').parentNode.parentNode.hide();
						$('states').parentNode.parentNode.hide();
					}
				});
				Event.observe($('state_enable'),'change', function(){
					if ($('state_enable').selectedIndex == 0)
					{
						$('state').selectedIndex = 0;
						$('state').parentNode.parentNode.hide();
						$('one_per_state').selectedIndex = 0;
						$('one_per_state').parentNode.parentNode.hide();
					}
					else
					{
						$('state').parentNode.parentNode.show();
						$('one_per_state').parentNode.parentNode.show();
					}
				});
				
				Event.observe($('one_per_state'),'change', function(){
					if ($('one_per_state').checked == 0)
					{
						$('state').parentNode.parentNode.show();
						$('states').selectedIndex = 0;
						$('states').parentNode.parentNode.hide();
					}
					else
					{
						$('state').parentNode.parentNode.hide();
						$('state').selectedIndex = 0;
						$('states').parentNode.parentNode.show();
					}
				});
			";
		}
	}

	/**
	 * @return mixed
     */
	public function getHeaderText()
	{
		return Mage::helper('contests')->__('Drawing Winner(s)');
	}

	/**
	 * @return mixed
     */
	public function getDrawWinnerUrl()
    {
        return $this->getUrl('*/*/drawWinner', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}