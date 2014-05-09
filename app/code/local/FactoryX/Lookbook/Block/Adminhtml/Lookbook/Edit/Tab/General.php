<?php

class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form of the general tab to edit a lookbook
	 */
	protected function _prepareForm() 
	{
		// Model registered as a lookbook
		$model = Mage::registry('lookbook');

        $form = new Varien_Data_Form();
        $this->setForm($form);
		
        $fieldset = $form->addFieldset('lookbook_form', array('legend' => Mage::helper('lookbook')->__('General Information')));
		
		// We retrieve the data from the session or the registered data
		if (Mage::getSingleton('adminhtml/session')->getLookbookData()) 
		{
			$data = Mage::getSingleton('adminhtml/session')->getLookbookData();
			Mage::getSingleton('adminhtml/session')->setLookbookData(null);
		} 
		elseif (Mage::registry('lookbook_data')) 
		{
			$data = Mage::registry('lookbook_data')->getData();
		}
		
		// Dummy values for disabled fields
		if ($data['category_id']) $data['category_text'] = $data['category_id'];
		$data['lookbook_type_text'] = $data['lookbook_type'];

		// Field for the title of the lookbook
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('lookbook')->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'title',
        ));
		
		// Field for the identifier (URL Key) of the lookbook validated with javascript
        $fieldset->addField('identifier', 'text', array(
            'label' => Mage::helper('lookbook')->__('Identifier'),
            'class' => 'required-entry validate-identifier',
            'required' => true,
            'name' => 'identifier',
			'note'      => Mage::helper('lookbook')->__('eg: domain.com/identifier')
            )
        );
		
		// Frozen field for the type
		$fieldset->addField('lookbook_type_text', 'select', array(
            'label' => Mage::helper('lookbook')->__('Lookbook Type'),
            'name' => 'lookbook_type_text',
			'disabled' => true,
            'values' => array(
                array(
                    'value' => 'category',
                    'label' => Mage::helper('lookbook')->__('Category Lookbook'),
                ),
				array(
                    'value' => 'images',
                    'label' => Mage::helper('lookbook')->__('Images Lookbook'),
				),
				array(
                    'value' => 'slideshow',
                    'label' => Mage::helper('lookbook')->__('Slideshow Lookbook'),
				)
			)
		));
		
		// Hidden field for the real type
		$fieldset->addField('lookbook_type', 'hidden', array(
			  'name'	=> 'lookbook_type'
		));
		
		if (!$data['category_id']) 
		{
			// Field for the default URL of the images lookbook
			$fieldset->addField('default_url', 'text', array(
				'label' => Mage::helper('lookbook')->__('Default URL'),
				'name' => 'default_url',
				'note'      => Mage::helper('lookbook')->__('This URL will be used as links on the images of the lookbook')
				)
			);
		}
			
		if ($data['category_id']) 
		{
		
			$fieldset->addField('category_text', 'text', array(
				'label' => Mage::helper('lookbook')->__('Category ID'),
				'name' => 'category_text',
				'disabled' => true
			));
			
			$fieldset->addField('category_id', 'hidden', array(
				  'name'	=> 'category_id'
			));
		}
		
		// Field for the status (Enabled or not)
		$fieldset->addField('status', 'select', array(
            'label' => Mage::helper('lookbook')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('lookbook')->__('Disabled'),
                ),
				array(
                    'value' => 1,
                    'label' => Mage::helper('lookbook')->__('Enabled'),
				)
			)
		));
		
		// Field for the menu inclusion (Enabled or not)
		$fieldset->addField('include_in_nav', 'select', array(
            'label' => Mage::helper('lookbook')->__('Include In Navigation Menu'),
            'name' => 'include_in_nav',
            'values' => array(
                array(
                    'value' => 0,
                    'label' => Mage::helper('lookbook')->__('No'),
                ),
				array(
                    'value' => 1,
                    'label' => Mage::helper('lookbook')->__('Yes'),
				)
			)
		));
		
		// Field for the sort order
		$fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('lookbook')->__('Sort Order'),
            'name' => 'sort_order',
			'note'      => Mage::helper('lookbook')->__('Used to sort the navigation menu.')
		));
		
		if ($data['lookbook_type'] != "slideshow")
		{
			// Field for the looks per page
			$fieldset->addField('looks_per_page', 'select', array(
				'label' => Mage::helper('lookbook')->__('Looks Per Page'),
				'name' => 'looks_per_page',
				'values' => array(
					array(
						'value' => 3,
						'label' => Mage::helper('lookbook')->__('3'),
					),
					array(
						'value' => 4,
						'label' => Mage::helper('lookbook')->__('4'),
					),
					array(
						'value' => 5,
						'label' => Mage::helper('lookbook')->__('5'),
					)
				)
			));
		}
		
		if ($data['category_id']) 
		{
			// Field for the show products links
			$fieldset->addField('show_child_products_link', 'select', array(
				'label' => Mage::helper('lookbook')->__('Show Child Products Links'),
				'name' => 'show_child_products_link',
				'values' => array(
					array(
						'value' => 0,
						'label' => Mage::helper('lookbook')->__('No'),
					),
					array(
						'value' => 1,
						'label' => Mage::helper('lookbook')->__('Yes'),
					)
				),
				'note'      => Mage::helper('lookbook')->__('This will display the child products links below the picture of the look.')
			));
		}
		
		if ($data['lookbook_type'] != "slideshow")
		{
			// Field for the zoom on hover
			$fieldset->addField('zoom_on_hover', 'select', array(
				'label' => Mage::helper('lookbook')->__('Zoom On Hover'),
				'name' => 'zoom_on_hover',
				'values' => array(
					array(
						'value' => 0,
						'label' => Mage::helper('lookbook')->__('No'),
					),
					array(
						'value' => 1,
						'label' => Mage::helper('lookbook')->__('Yes'),
					)
				),
				'note'      => Mage::helper('lookbook')->__('This will add a bubble up zoom on the look pictures.')
			));
		
			// Field for the look color
			$fieldset->addField('look_color', 'select', array(
				'label' => Mage::helper('lookbook')->__('Look Color'),
				'name' => 'look_color',
				'values' => array(
					array(
						'value' => 'white',
						'label' => Mage::helper('lookbook')->__('White')
					),
					array(
						'value' => 'black',
						'label' => Mage::helper('lookbook')->__('Black'),
					)
				)
			));
		
		}
		
		if ($data['category_id']) 
		{
			// Field for the show shop the look pix
			$fieldset->addField('show_shop_pix', 'select', array(
				'label' => Mage::helper('lookbook')->__('Show Shop The Look Picture'),
				'name' => 'show_shop_pix',
				'values' => array(
					array(
						'value' => 0,
						'label' => Mage::helper('lookbook')->__('No'),
					),
					array(
						'value' => 1,
						'label' => Mage::helper('lookbook')->__('Yes'),
					)
				),
				'note'      => Mage::helper('lookbook')->__('This will display a shop the look picture below each look.')
			));
			
			// Add our custom shopthelookimage type
			$fieldset->addType('shopthelookimage','FactoryX_Lookbook_Model_Varien_Data_Form_Element_ShopthelookImage');
			
			// Field for the shop the look pix
			$fieldset->addField('shop_pix', 'shopthelookimage', array(
				'label'     => Mage::helper('lookbook')->__('Shop The Look Image'),
				'name'      => 'shop_pix'
			));
		}

		// We fill the form based on the retrieved data
        $form->setValues($data);
		
        return parent::_prepareForm();
    }
	
}