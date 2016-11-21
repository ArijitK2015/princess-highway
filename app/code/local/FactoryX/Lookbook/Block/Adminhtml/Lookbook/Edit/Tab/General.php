<?php

/**
 * Class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_General
 */
class FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare the form of the general tab to edit a lookbook
     */
    protected function _prepareForm()
    {
        // Model registered as a lookbook
        Mage::registry('lookbook');

        $form = new Varien_Data_Form();
        $this->setForm($form);

        // General Information
        $fieldset = $form->addFieldset('lookbook_form', array(
                'legend'    => Mage::helper('lookbook')->__('General Information'),
                //'class'     => 'fieldset-wide',
                'expanded'  => true // open
            )
        );

        // Look & Feel
        $fieldset2 = $form->addFieldset('lookbook_form2', array(
                'legend'    => Mage::helper('lookbook')->__('Look & Feel'),
                //'class'     => 'fieldset-wide',
                'expanded'  => false // closed
            )
        );

        // Look & Feel / Info to display under product - Show Child Products with Links
        $fieldset2_1 = $form->addFieldset('lookbook_form2_1', array(
                'legend'    => Mage::helper('lookbook')->__('Look & Feel :: Info to display under product - Show Child Products with Link Options'),
                //'class'     => 'fieldset-wide',
                'expanded'  => false // closed
            )
        );        

        // Actions
        $fieldset3 = $form->addFieldset('lookbook_form3', array(
                'legend'    => Mage::helper('lookbook')->__('Actions'),
                //'class'     => 'fieldset-wide',
                'expanded'  => false // closed
            )
        );

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
        else $data = array();

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
                ),
                array(
                    'value' => 'flipbook',
                    'label' => Mage::helper('lookbook')->__('Flipbook'),
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
        $includeInNavField = $fieldset->addField('include_in_nav', 'select', array(
            'label' => Mage::helper('lookbook')->__('Include In Navigation Menu'),
            'name' => 'include_in_nav',
            'values' => array(
                array(
                    'value' => 'no',
                    'label' => Mage::helper('lookbook')->__('No'),
                ),
                array(
                    'value' => 'before',
                    'label' => Mage::helper('lookbook')->__('Yes, Before Category Nav'),
                ),
                array(
                    'value' => 'after',
                    'label' => Mage::helper('lookbook')->__('Yes, After Category Nav'),
                ),
                array(
                    'value' => 'category',
                    'label' => Mage::helper('lookbook')->__('Yes, Under Category Nav'),
                )
            )
        ));

        // Display possible choices for the category
        $navCatField = $fieldset->addField('nav_category', 'select', array(
            'label'     => Mage::helper('lookbook')->__('Choose a Category'),
            'name'      => 'nav_category',
            'values'    => Mage::helper('lookbook')->getSelectableCategories()
        ));

        // Field for the sort order
        $sortOrderField = $fieldset->addField('sort_order', 'text', array(
            'label' => Mage::helper('lookbook')->__('Sort Order'),
            'name' => 'sort_order',
            'note'      => Mage::helper('lookbook')->__('Used to sort the navigation menu.')
        ));

        if ($data['lookbook_type'] == "slideshow") {
            // Field for the looks per page
            $fieldset->addField('direction', 'select', array(
                'label' => Mage::helper('lookbook')->__('Direction'),
                'name' => 'direction',
                'values' => array(
                    array(
                        'value' => 'vertical',
                        'label' => Mage::helper('lookbook')->__('Vertical'),
                    ),
                    array(
                        'value' => 'horizontal',
                        'label' => Mage::helper('lookbook')->__('Horizontal'),
                    )
                )
            ));
        }

        if ($data['lookbook_type'] == "flipbook") {

            // Field for the looks per page
            $fieldset2->addField('page_prompt', 'select', array(
                'label' => Mage::helper('lookbook')->__('Page Prompt'),
                'name' => 'page_prompt',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('lookbook')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('lookbook')->__('No'),
                    )
                )
            ));
        }

        if ($data['lookbook_type'] != "slideshow" && $data['lookbook_type'] != "flipbook") {

            // Field for the looks per page
            $fieldset2->addField('looks_per_page', 'select', array(
                'label' => Mage::helper('lookbook')->__('Looks Per Page'),
                'name' => 'looks_per_page',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('lookbook')->__('1'),
                    ),
                    array(
                        'value' => 2,
                        'label' => Mage::helper('lookbook')->__('2'),
                    ),
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

            // Field for the looks per page
            $fieldset2->addField('slides_per_group', 'select', array(
                'label' => Mage::helper('lookbook')->__('Slides Per Group'),
                'name' => 'slides_per_group',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('lookbook')->__('1'),
                    ),
                    array(
                        'value' => 2,
                        'label' => Mage::helper('lookbook')->__('2'),
                    ),
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

            // Field for the looks per page
            $fieldset2->addField('layout', 'select', array(
                'label' => Mage::helper('lookbook')->__('Lookbook Layout'),
                'name' => 'layout',
                'values' => array(
                    array(
                        'value' => 'default',
                        'label' => Mage::helper('lookbook')->__('Default'),
                    ),
                    array(
                        'value' => 'grid',
                        'label' => Mage::helper('lookbook')->__('Grid'),
                    )
                )
            ));
        }

        $fieldset2->addField('root_template', 'select', array(
            'label' => Mage::helper('lookbook')->__('Page Layout'),
            'name' => 'root_template',
            'values' => Mage::getSingleton('page/source_layout')->toOptionArray()
        ));

        // Field for the looks per page
        $navTypeField = $fieldset3->addField('nav_type', 'select', array(
            'label' => Mage::helper('lookbook')->__('Navigation Type'),
            'name' => 'nav_type',
            'values' => array(
                array(
                    'value' => "fontawesome",
                    'label' => Mage::helper('lookbook')->__('FontAwesome Dot & Arrow'),
                ),
                array(
                    'value' => "arrows",
                    'label' => Mage::helper('lookbook')->__('Arrows'),
                ),
                array(
                    'value' => "dots",
                    'label' => Mage::helper('lookbook')->__('Dots'),
                ),
                array(
                    'value' => "arrows-dots",
                    'label' => Mage::helper('lookbook')->__('Arrows & Dots'),
                ),
                array(
                    'value' => "scrollbar",
                    'label' => Mage::helper('lookbook')->__('Scrollbar'),
                )
                /*,
                array(
                    'value' => "numbered",
                    'label' => Mage::helper('lookbook')->__('Numbered'),
                )
                */
            )
        ));

        // Get corresponding folder
        $folder = Mage::getDesign()->getSkinBaseDir(array('_theme' => 'default')).'/images/factoryx/lookbook';;
        $subfolder = 'nav';

        // Get list of corresponding pictures
        $navPix = Mage::helper('lookbook')->dirFiles($folder,$subfolder);

        // Add our custom pickable image type
        $fieldset3->addType('dropdown_images','FactoryX_Lookbook_Model_Varien_Data_Form_Element_DropdownImages');

        // Field for the slider navigation style
        $navDropdownField = $fieldset3->addField('slider_nav_dropdown', 'dropdown_images', array(
            'label' => Mage::helper('lookbook')->__('Arrows Style'),
            'name' => 'slider_nav_dropdown',
            'values' => $navPix
        ));

        $fieldset3->addField('slider_nav_style', 'hidden', array(
            'name'	=> 'slider_nav_style'
        ));

        // Get corresponding folder
        $subfolder = 'pag';

        // Get list of corresponding pictures
        $pagPix = Mage::helper('lookbook')->dirFiles($folder,$subfolder);

        // Field for the slider pagination style
        $pagDropdownField = $fieldset3->addField('slider_pagination_dropdown', 'dropdown_images', array(
            'label' => Mage::helper('lookbook')->__('Dots Style'),
            'name' => 'slider_pagination_dropdown',
            'values' => $pagPix
        ));

        $fieldset3->addField('slider_pagination_style', 'hidden', array(
            'name'	=> 'slider_pagination_style'
        ));

        if ($data['category_id'])
        {
            // Field to show under product
            $underProductInfoField = $fieldset2->addField('under_product_info', 'select', array(
                'label' => Mage::helper('lookbook')->__('Info to display under product'),
                'name' => 'under_product_info',
                'values' => array(
                    array(
                        'value' => 0,
                        'label' => Mage::helper('lookbook')->__('None'),
                    ),
                    array(
                        'value' => 1,
                        'label' => Mage::helper('lookbook')->__('Show Child Products with Links'),
                    ),
                    array(
                        'value' => 3,
                        'label' => Mage::helper('lookbook')->__('Show Bundle Product Description'),
                    )
                )
            ));

            // Show Child Products with Links - Unavailable Product Contact
            $underProductInfoContactField = $fieldset2_1->addField('under_product_info_links_contact', 'text', array(
                'label' => Mage::helper('lookbook')->__('Unavailable Product : Contact'),
                'class' => 'validate-email',
                'name'  => 'under_product_info_links_contact',
                'note'  => Mage::helper('lookbook')->__('Optional field to add a mailto href using the subject below.')                
            ));

            // Show Child Products with Links - Unavailable Product Subject
            $underProductInfoSubjectField = $fieldset2_1->addField('under_product_info_links_subject', 'text', array(
                'label' => Mage::helper('lookbook')->__('Unavailable Product : Subject'),
                'name'  => 'under_product_info_links_subject',
                'note'  => Mage::helper('lookbook')->__('Optional field the mailto subject (uses placeholder %PRODUCT_NAME% for the product name).')
            ));

            // Show Child Products with Links - Available Product Prefix
            $underProductInfoUnavailableField = $fieldset2_1->addField('under_product_info_links_unavailable_prefix', 'text', array(
                'label' => Mage::helper('lookbook')->__('Unavailable Product : Prefix'),
                'name'  => 'under_product_info_links_unavailable_prefix',
                'note'  => Mage::helper('lookbook')->__('Optional prefix to use as a prompt before unavailable products e.g. PREORDER')
            ));

            // Show Child Products with Links - Available Product Prefix
            $underProductInfoAvailableField = $fieldset2_1->addField('under_product_info_links_available_prefix', 'text', array(
                'label' => Mage::helper('lookbook')->__('Available Product : Prefix'),
                'name'  => 'under_product_info_links_available_prefix',
                'note'  => Mage::helper('lookbook')->__('Optional prefix to use as a prompt before available products e.g. PREORDER')
            ));

            // show look number
            $fieldset2->addField('show_look_number', 'select', array(
                'label' => Mage::helper('lookbook')->__('Show look number'),
                'name' => 'show_look_number',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('lookbook')->__('Yes'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('lookbook')->__('No'),
                    )
                )
            ));

            // Field for bundle click through
            $bundleClickField = $fieldset3->addField('bundle_click', 'select', array(
                'label' => Mage::helper('lookbook')->__('Click through to'),
                'name' => 'bundle_click',
                'values' => array(
                    array(
                        'value' => 0,
                        'label' => Mage::helper('lookbook')->__('None'),
                    ),
                    array(
                        'value' => 1,
                        'label' => Mage::helper('lookbook')->__('Bundle product'),
                    ),
                    array(
                        'value' => 2,
                        'label' => Mage::helper('lookbook')->__('Bundle product Pop-up'),
                    ),
                    array(
                        'value' => 3,
                        'label' => Mage::helper('lookbook')->__('URL'),
                    )
                )
            ));

            // Field for the identifier (URL Key) of the lookbook validated with javascript
            $clickToUrlField = $fieldset3->addField('click_to_url', 'text', array(
                    'label' => Mage::helper('lookbook')->__('URL'),
                    'class' => 'validate-url',
                    'required' => false,
                    'name' => 'click_to_url',
                    'note' => Mage::helper('lookbook')->__('eg: domain.com/uri')
                )
            );

            // Open link in new tab?
            $clickNewTabField = $fieldset3->addField('click_new_tab', 'select', array(
                'label' => Mage::helper('lookbook')->__('Open in new tab'),
                'name' => 'click_new_tab',
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
        }

        if ($data['lookbook_type'] == "flipbook")
        {
            // Open link in new tab?
            $fieldset3->addField('click_popup', 'select', array(
                'label' => Mage::helper('lookbook')->__('Open Image in a popup on click'),
                'name' => 'click_popup',
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
        }

        if ($data['lookbook_type'] != "slideshow"  && $data['lookbook_type'] != "flipbook")
        {
            // Field for the zoom on hover
            $fieldset3->addField('zoom_on_hover', 'select', array(
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
            $fieldset2->addField('look_color', 'select', array(
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
            $showShopPixField = $fieldset2->addField('show_shop_pix', 'select', array(
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
            $shopPixField = $fieldset->addField('shop_pix', 'shopthelookimage', array(
                'label'     => Mage::helper('lookbook')->__('Shop The Look Image'),
                'name'      => 'shop_pix'
            ));

            $this->setChild('form_after', $this->getLayout()
                ->createBlock('adminhtml/widget_form_element_dependence')
                ->addFieldMap($underProductInfoField->getHtmlId(), $underProductInfoField->getName())
                ->addFieldMap($underProductInfoSubjectField->getHtmlId(), $underProductInfoSubjectField->getName())
                ->addFieldMap($underProductInfoContactField->getHtmlId(), $underProductInfoContactField->getName())
                ->addFieldMap($underProductInfoUnavailableField->getHtmlId(), $underProductInfoUnavailableField->getName())
                ->addFieldMap($underProductInfoAvailableField->getHtmlId(), $underProductInfoAvailableField->getName())
                ->addFieldMap($showShopPixField->getHtmlId(), $showShopPixField->getName())
                ->addFieldMap($shopPixField->getHtmlId(), $shopPixField->getName())
                ->addFieldMap($bundleClickField->getHtmlId(), $bundleClickField->getName())
                ->addFieldMap($clickToUrlField->getHtmlId(), $clickToUrlField->getName())
                ->addFieldMap($clickNewTabField->getHtmlId(), $clickNewTabField->getName())
                ->addFieldDependence($underProductInfoSubjectField->getName(), $underProductInfoField->getName(), 1)
                ->addFieldDependence($underProductInfoContactField->getName(), $underProductInfoField->getName(), 1)
                ->addFieldDependence($underProductInfoUnavailableField->getName(), $underProductInfoField->getName(), 1)
                ->addFieldDependence($underProductInfoAvailableField->getName(), $underProductInfoField->getName(), 1)
                ->addFieldDependence($shopPixField->getName(), $showShopPixField->getName(), 1)
                ->addFieldDependence($clickToUrlField->getName(), $bundleClickField->getName(), 3)
                ->addFieldDependence($clickNewTabField->getName(), $bundleClickField->getName(), [1, 2, 3])
            );
        }



        // We fill the form based on the retrieved data
        $form->setValues($data);

        if ($this->getChild('form_after')
            && $this->getChild('form_after')->getType() == "adminhtml/widget_form_element_dependence") {
            $afterBlock = $this->getChild('form_after');
        } else {
            $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence'));
            $afterBlock = $this->getChild('form_after');
        }

        $afterBlock
            ->addFieldMap($includeInNavField->getHtmlId(), $includeInNavField->getName())
            ->addFieldMap($navCatField->getHtmlId(), $navCatField->getName())
            ->addFieldMap($sortOrderField->getHtmlId(), $sortOrderField->getName())
            ->addFieldMap($navTypeField->getHtmlId(), $navTypeField->getName())
            ->addFieldMap($navDropdownField->getHtmlId(), $navDropdownField->getName())
            ->addFieldMap($pagDropdownField->getHtmlId(), $pagDropdownField->getName())
            ->addFieldDependence($navCatField->getName(), $includeInNavField->getName(), 'category')
            ->addFieldDependence($sortOrderField->getName(), $includeInNavField->getName(), ['before','after','category'])
            ->addFieldDependence($navDropdownField->getName(), $navTypeField->getName(), ['arrows', 'arrows-dots'])
            ->addFieldDependence($pagDropdownField->getName(), $navTypeField->getName(), 'dots');

        return parent::_prepareForm();
    }

}