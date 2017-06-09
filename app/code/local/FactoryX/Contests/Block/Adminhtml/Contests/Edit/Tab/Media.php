<?php

/**
 * Class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Media
 */
class FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Media extends Mage_Adminhtml_Block_Widget_Form
{
	protected $_exampleGaPictureUrl = "http://www.gormanshop.com.au/skin/frontend/default/gorman/images/giveaway/giveaway.gif";
	protected $_exampleRafPictureUrl = "http://shop.alannahhill.com.au/skin/frontend/default/theme010k/images/raf/bag-banner1.jpg";
	protected $_exampleListPictureUrl = "http://shop.jacklondon.com.au/giveaways/vespa/thumbnail.jpg";
	protected $_exampleThankYouPictureUrl = "http://shop.alannahhill.com.au/media/wysiwyg/2014/05/20140507-win-a-dream-bouquet-thank-you.jpg";

	/**
	 * Prepare the form of the media tab for the edit contest page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('contests_form', array('legend' => Mage::helper('contests')->__('Contest Picture')));
		
		// We use our own image type to handle the pictures properly
		$fieldset->addType('contestimage','FactoryX_Contests_Model_Varien_Data_Form_Element_ContestImage');

		// Field to upload an image related to the contest
        $fieldset->addField('image_url', 'contestimage', array(
			'label'     => Mage::helper('contests')->__('Frontend Contest Picture'),
			'class'		=> 'required-entry required-file',
			'required'  => true,
			'name'      => 'image_url',
			'note'      => Mage::helper('contests')->__('Recommended image size: 960px wide x 270px high for a Refer A Friend contest (<a target="_blank" rel="noopener noreferrer" href="%s">example</a>) or 354px wide x 658px high for a Give Away contest (<a target="_blank" href="%s">example</a>).', $this->_exampleRafPictureUrl, $this->_exampleGaPictureUrl),
		));
		
		// Field to upload an list image related to the contest
        $fieldset->addField('list_image_url', 'contestimage', array(
			'label'     => Mage::helper('contests')->__('List Contest Picture'),
			'name'      => 'list_image_url',
			'note'      => Mage::helper('contests')->__('Only displayed if you choose Yes in the List tab, recommended image size 250px wide by 200px high. (<a target="_blank" rel="noopener noreferrer" href="%s">example</a> N.B.: the example is not at the right dimensions)',$this->_exampleListPictureUrl),
		));
		
		// Field to upload an thank you image related to the contest
        $fieldset->addField('thank_you_image_url', 'contestimage', array(
			'label'     => Mage::helper('contests')->__('Thank You Picture'),
			'name'      => 'thank_you_image_url',
			'note'      => Mage::helper('contests')->__('Recommended image size 980px wide by any height. (<a target="_blank" rel="noopener noreferrer" href="%s">example</a>)',$this->_exampleThankYouPictureUrl),
		));
	  
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getContestsData()) 
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getContestsData());
			Mage::getSingleton('adminhtml/session')->setContestsData(null);
		} 
		elseif (Mage::registry('contests_data')) 
		{
			$form->setValues(Mage::registry('contests_data')->getData());
		}
	}
	
}