<?php

class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Media extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
	public function __construct()
    {
        parent::__construct();
    }
	
	/**
	 * Prepare the form of the media tab for the edit lookbook page
	 */
	protected function _prepareForm() 
	{
		$model = Mage::registry('lookbook_data');
		
        $form = new Varien_Data_Form();
		
		/**
         * Initialize reference object as form property
         * for using it in elements generation
         */
        $form->setDataObject($model);
		
        $this->setForm($form);
        $fieldset = $form->addFieldset('lookbook_form', array('legend' => Mage::helper('lookbook')->__('Lookbook Pictures')));
		
		// We use our own image type to handle the pictures properly
		$fieldset->addType('image_gallery','FactoryX_Lookbook_Model_Varien_Data_Form_Element_ImageGallery');
		
		// Add multiple images field
		$fieldset->addField('lookbook_image', 'image_gallery', array(
            'name'      => 'lookbook_image',
            'label'     => Mage::helper('lookbook')->__('Lookbook Images'),
            'title'     => Mage::helper('lookbook')->__('Lookbook Images')
        ));
		
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getLookbookData()) 
		{
			$form->setValues(Mage::getSingleton('adminhtml/session')->getLookbookData());
			Mage::getSingleton('adminhtml/session')->setLookbookData(null);
		} 
		elseif (Mage::registry('lookbook_data')) 
		{
			$form->setValues(Mage::registry('lookbook_data')->getData());
		}
	}
	
	/**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('lookbook')->__('Media');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('lookbook')->__('Media');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('*/*/' . $action);
    }
	
}