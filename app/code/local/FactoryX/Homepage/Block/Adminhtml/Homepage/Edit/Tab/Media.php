<?php

class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_Media extends Mage_Adminhtml_Block_Widget_Form
{

	/**
	 * Prepare the form of the media tab for the edit homepage page
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form();
        $this->setForm($form);
		
        $fieldset = $form->addFieldset('homepage_form', array('legend' => Mage::helper('homepage')->__('Homepage Picture')));
		
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getHomepageData()) 
		{
			$data = Mage::getSingleton('adminhtml/session')->getHomepageData();
		} 
		elseif (Mage::registry('homepage_data')) 
		{
			$data = Mage::registry('homepage_data')->getData();
		}
		
		// We add the images data to the original data array
		$data = Mage::helper('homepage')->addImagesData($data);
		
		// Add our custom layout image type
		$fieldset->addType('layoutimage','FactoryX_Homepage_Model_Varien_Data_Form_Element_LayoutImage');
		
		// Display the chosen layout using our custom layout image type
		$fieldset->addField('layout_image', 'layoutimage', array(
			'label'     => Mage::helper('homepage')->__('Chosen Layout'),
			'text' => $data['layout'],
			'name'	=>	'layout_image'
		));
		
		// Hidden field to store the real layout value
		$fieldset->addField('layout', 'hidden', array(
			  'name'               => 'layout'
		));
		
		// Add the 'Change Layout' button
		$changeLayoutButton = $fieldset->addField('layoutbutton', 'text', array(
			'label' => Mage::helper('homepage')->__('Change Layout'), 
			'name' => 'layoutbutton'
		));
		
		// Renderer the button using our custom block
		$changeLayoutButton->setRenderer($this->getLayout()->createBlock('homepage/adminhtml_template_edit_renderer_button'));
		
		// If the amount of pictures is set
		if (array_key_exists('amount',$data))
		{
			// We add a note to help 
			$fieldset->addField('note', 'note', array(
				'text'     => Mage::helper('homepage')->__('<ul><li>- The width of the slider images must be 980px.</li><li>- The TOTAL width of the vertical images must be 980px.</li><li>- The TOTAL height of the vertical images must all be equals.</ul>'),
			));
			
			// Add our custom homepageimage type
			$fieldset->addType('homepageimage','FactoryX_Homepage_Model_Varien_Data_Form_Element_HomepageImage');
			
			// Based on the amount of pictures to display
			for($i=1;$i<=$data['amount'];$i++)
			{
				// We add the picture picker using our custom homepageimage type
				$fieldset->addField('image_'.$i, 'homepageimage', array(
					'label'     => Mage::helper('homepage')->__('Homepage Image %s',$i),
					'class'		=> 'required-entry required-file',
					'required'  => true,
					'name'      => 'image_'.$i
				));
				
				// We add the picture link
				$fieldset->addField('link_'.$i, 'text', array(
					'label' => Mage::helper('homepage')->__('Homepage Image Link %s',$i),
					'class'	=> 'required-entry',
					'required' => true,
					'name' => 'link_'.$i,
				));
				
				// We add the picture alt title
				$fieldset->addField('alt_'.$i, 'text', array(
					'label' => Mage::helper('homepage')->__('Homepage Image Title %s',$i),
					'class'	=> 'required-entry',
					'required' => true,
					'name' => 'alt_'.$i,
				));
				
				// We add the popup picker
				$fieldset->addField('popup_'.$i, 'select', array(
					'label' => Mage::helper('homepage')->__('Is Homepage Image Link %s a popup ?',$i),
					'name' => 'popup_'.$i,
					'values' => array(
						array(
							'value' => 0,
							'label' => Mage::helper('homepage')->__('No'),
						),
						array(
							'value' => 1,
							'label' => Mage::helper('homepage')->__('Yes'),
						)
					),
					'note'      => Mage::helper('homepage')->__('Popup means whether the link should be open in a new window or not.')
				));
			}
		}
	  
		// We fill the form based on the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getHomepageData()) 
		{
			$form->setValues($data);
			Mage::getSingleton('adminhtml/session')->setHomepageData(null);
		} 
		elseif (Mage::registry('homepage_data')) 
		{
			$form->setValues($data);
		}
	}
	
}