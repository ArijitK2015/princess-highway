<?php

/**
 * Class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_Media
 */
class FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_Media extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Prepare the form of the media tab for the edit homepage page
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('homepage_form', array('legend' => Mage::helper('homepage')->__('Homepage Layout')));

        // We fill the form based on the session or the registered data
        if (Mage::getSingleton('adminhtml/session')->getHomepageData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getHomepageData();
        }
        elseif (Mage::registry('homepage_data'))
        {
            $data = Mage::registry('homepage_data')->getData();
        }
        else $data = array();

        // We add the images data to the original data array
        $data = Mage::helper('homepage')->addImagesData($data);

        if (!$data['slider'])
        {
            // We add the over images to the original data array
            $data = Mage::helper('homepage')->addOverImagesData($data);
        }

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
                'text'     => Mage::helper('homepage')->__('<ul><li>- The width of the slider images must be 1200px.</li><li>- The TOTAL width of the vertical images must be 1200px.</li><li>- The TOTAL height of the vertical images must all be equals.</ul>'),
            ));

            // Based on the amount of pictures to display
            for($i=1;$i<=$data['amount'];$i++)
            {

                $fieldset = $form->addFieldset('homepage_form'.$i, array('legend' => Mage::helper('homepage')->__('Homepage Picture # %s',$i)));

                // We add the type picker
                $fieldset->addField('type_'.$i, 'select', array(
                    'label' => Mage::helper('homepage')->__('Type %s',$i),
                    'name' => 'type_'.$i,
                    'values' => array(
                        array(
                            'value' => 'image',
                            'label' => Mage::helper('homepage')->__('Image'),
                        ),
                        array(
                            'value' => 'block',
                            'label' => Mage::helper('homepage')->__('CMS Block'),
                        )
                    )
                ));

                // Generate the product chooser widget
                $config = array(
                    'input_id'    => 'block_id_'.$i,
                    'input_name'  => 'block_id_'.$i,
                    'input_label' => Mage::helper('homepage')->__('Block %s',$i),
                    'button_text' => 'Select Block...'
                );

                $model = Mage::getModel('cms/block');
                if (array_key_exists('type_'.$i,$data) && $data['type_'.$i] == "block" && $data['block_id_'.$i])
                {
                    $model->setData(array('block_id_'.$i => $data['block_id_'.$i]));
                    unset($data['block_id_'.$i]);
                }


                // Create our chooser with our custom block
                Mage::helper('chooserwidget')->createCmsBlockChooser($model, $fieldset, $config);

                // Add our custom homepageimage type
                $fieldset->addType('homepageimage','FactoryX_Homepage_Model_Varien_Data_Form_Element_HomepageImage');

                // We add the picture picker using our custom homepageimage type
                $fieldset->addField('image_'.$i, 'homepageimage', array(
                    'label'     => Mage::helper('homepage')->__('Homepage Image %s',$i),
                    'class'		=> 'required-file',
                    'required'  => true,
                    'name'      => 'image_'.$i
                ));

                if (!$data['slider'])
                {
                    // We add the picture picker using our custom homepageimage type
                    $fieldset->addField('image_over_'.$i, 'homepageimage', array(
                        'label'     => Mage::helper('homepage')->__('Homepage Over Image %s',$i),
                        'class'		=> 'required-file',
                        'name'      => 'image_over_'.$i
                    ));
                }

                // We add the picture link
                $fieldset->addField('link_'.$i, 'text', array(
                    'label' => Mage::helper('homepage')->__('Homepage Image Link %s',$i),
                    'required' => true,
                    'name' => 'link_'.$i,
                ));

                // We add the picture alt title
                $fieldset->addField('alt_'.$i, 'text', array(
                    'label' => Mage::helper('homepage')->__('Homepage Image Title %s',$i),
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

        // Dynamic dependencies mapping
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence'));

        for($i=1;$i<=$data['amount'];$i++)
        {
            $this->getChild('form_after')
                ->addFieldMap('type_'.$i, 'type_'.$i)
                ->addFieldMap('image_'.$i, 'image_'.$i)
                ->addFieldMap('link_'.$i, 'link_'.$i)
                ->addFieldMap('alt_'.$i, 'alt_'.$i)
                ->addFieldMap('popup_'.$i, 'popup_'.$i)
                //->addFieldMap('block_id_'.$i, 'block_id_'.$i)
                ->addFieldDependence('image_'.$i, 'type_'.$i, 'image')
                ->addFieldDependence('link_'.$i, 'type_'.$i, 'image')
                ->addFieldDependence('alt_'.$i, 'type_'.$i, 'image')
                ->addFieldDependence('popup_'.$i, 'type_'.$i, 'image')
                /*->addFieldDependence('block_id_'.$i, 'type_'.$i, 'block')*/;

            if  (!$data['slider']) {
                $this->getChild('form_after')
                    ->addFieldMap('image_over_'.$i, 'image_over_'.$i)
                    ->addFieldDependence('image_over_'.$i, 'type_'.$i, 'image');
            }
        }
    }

}