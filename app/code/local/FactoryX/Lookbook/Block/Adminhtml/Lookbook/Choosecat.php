<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Choosecat
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Choosecat extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *	Constructor for the choosecat page
	 */
	public function __construct()
	{
		parent::__construct();

		$this->_objectId = 'id';
		$this->_blockGroup = 'lookbook';
		$this->_controller = 'adminhtml_lookbook';
		$this->_mode = 'choosecat'; // edit by default

		// Remove useless buttons
		$this->_removeButton('save');
		$this->_removeButton('delete');

		// Add the continue button
		$this->_addButton('continuelookbook', array(
			'label' => Mage::helper('lookbook')->__('Continue'),
			'onclick' => 'continuelookbook()',
			'class' => 'save',
		), -100);

		// Add the choose layout URL
		$this->_formScripts[] = "
			choosecatForm = new varienForm('choosecat_form', '');
			function continuelookbook() {
				$(choosecatForm.formId).action = '" . $this->getContinueLookbookUrl() . "';
				choosecatForm.submit();
			}
		";

		// Add some JS to hide/show options
		$this->_formScripts[] = "
			Event.observe(window, 'load', function(){
				if ($('lookbook_type').selectedIndex != 0)
				{
					$('lookbook_type').parentNode.parentNode.next().hide();
					$('lookbook_type').parentNode.parentNode.next().next().hide();
				}
			});
			
			Event.observe($('lookbook_type'),'change', function(){
				if ($('lookbook_type').selectedIndex != 0)
				{
					$('lookbook_type').parentNode.parentNode.next().hide();
					$('lookbook_type').parentNode.parentNode.next().next().hide();
				}
				else
				{
					$('lookbook_type').parentNode.parentNode.next().show();
					$('lookbook_type').parentNode.parentNode.next().next().show();
				}
			});
        ";
	}

	/**
	 *	Getter for the header text
	 */
	public function getHeaderText()
	{
		return Mage::helper('lookbook')->__('Lookbook');
	}

	/**
	 *	Getter for the continue URL
	 */
	public function getContinueLookbookUrl()
	{
		if ($id = $this->getRequest()->getParam('id'))
		{
			return $this->getUrl('*/*/new', array('id' => $id));
		}
		else return $this->getUrl('*/*/new', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
	}
}