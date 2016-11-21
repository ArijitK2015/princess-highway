<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Edit
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	/**
	 *
     */
	public function __construct()
	{
		parent::__construct();
		$this->_objectId = 'id';
		$this->_blockGroup = 'contests';
		$this->_controller = 'adminhtml_contests';
		$this->_updateButton('save', 'label', Mage::helper('contests')->__('Save Contest'));
		$this->_updateButton('delete', 'label', Mage::helper('contests')->__('Delete Contest'));

		$popupMessage = Mage::helper('contests')->__('The newsletter popup is currently enabled, enabling the contest popup will disable the newsletter popup. Are you sure ?');

		// Add the Save and Continue button
		$this->_addButton('saveandcontinue', array(
			'label' => Mage::helper('contests')->__('Save And Continue Edit'),
			'onclick' => Mage::getStoreConfigFlag('newsletter/popup/enable') ? 'saveAndContinueEdit(\''.$popupMessage.'\')' : 'saveAndContinueEdit()',
			'class' => 'save',
		), -100);

		if (Mage::getStoreConfigFlag('newsletter/popup/enable')) {
			$this->_formScripts[] = "
            function saveAndContinueEdit(message){
            		if (1 == $('is_popup').selectedIndex) {
						if( confirm(message) ) {
							editForm.submit($('edit_form').action+'back/edit/');
						}
						return false;
					} else {
						editForm.submit($('edit_form').action+'back/edit/');
					}
            }";
			$this->_updateButton('save','onclick','if (1 == $(\'is_popup\').selectedIndex) { if (confirm(\''.$popupMessage.'\')) {editForm.submit();} return false; } else {editForm.submit();}');
		} else {
			$this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";
		}
			
		// Add some JS to hide/show dates
		$this->_formScripts[] = "
			Validation.add('validate-datetime-au','Please use this date format: dd/mm/yyyy hh:ss A',function(v){
				if(Validation.get('IsEmpty').test(v)) return true;
                var regex = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2}) ([paPA][Mm])$/;
                if(!regex.test(v)) return false;
                var d = new Date(v.replace(regex, '$2/$1/$3 $4:$5 $6'));
                return ( parseInt(RegExp.$2, 10) == (1+d.getMonth()) ) &&
                            (parseInt(RegExp.$1, 10) == d.getDate()) &&
                            (parseInt(RegExp.$3, 10) == d.getFullYear() ) &&
                            (parseInt(RegExp.$4, 10) == (d.getHours() + ((RegExp.$6 == 'AM' || RegExp.$6 == 'am') ? 12 : 0) ) ) &&
                            (parseInt(RegExp.$5, 10) == d.getMinutes() );
			});
        ";
			
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

	/**
	 * @return mixed
     */
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

	/**
	 * @return mixed
     */
	public function getDrawUrl()
    {
        return $this->getUrl('*/*/draw', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }
}