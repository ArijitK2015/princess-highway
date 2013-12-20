<?php
 
class Xigmapro_Jobs_Block_Adminhtml_Jobs_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('Jobs_form', array('legend'=>Mage::helper('Jobs')->__('Job information')));
       
        $fieldset->addField('position', 'text', array(
            'label'     => Mage::helper('Jobs')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'position',
        ));
 
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('Jobs')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('Jobs')->__('Active'),
                ),
 
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('Jobs')->__('Inactive'),
                ),
            ),
        ));
       $fieldset->addField('countrys', 'text', array(
            'label'     => Mage::helper('Jobs')->__('Country'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'countrys',
        ));
		 $fieldset->addField('locations', 'text', array(
            'label'     => Mage::helper('Jobs')->__('Location'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'locations',
        ));
		$fieldset->addField('statuss', 'select', array(
            'label'     => Mage::helper('Jobs')->__('Job Status'),
            'name'      => 'statuss',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('Jobs')->__('Full time'),
                ),
 
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('Jobs')->__('Part Time'),
                ),
				
				 array(
                    'value'     => 2,
                    'label'     => Mage::helper('Jobs')->__('Casual'),
                ),
				 array(
                    'value'     => 3,
                    'label'     => Mage::helper('Jobs')->__('Contract'),
                ),
            ),
        ));
		/* $fieldset->addField('statuss', 'text', array(
            'label'     => Mage::helper('Jobs')->__('Job Status'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'statuss',
        ));*/
		 $fieldset->addField('hours', 'text', array(
            'label'     => Mage::helper('Jobs')->__('Hours'),
            'class'     => '',
            'required'  => false,
            'name'      => 'hours',
        ));
		
		 $fieldset->addField('entitlements', 'text', array(
            'label'     => Mage::helper('Jobs')->__('Entitlements'),
            'class'     => '',
            'required'  => false,
            'name'      => 'entitlements',
        ));
        $fieldset->addField('requirements', 'editor', array(
            'name'      => 'requirements',
            'label'     => Mage::helper('Jobs')->__('Requirements'),
            'title'     => Mage::helper('Jobs')->__('requirements'),
            'style'     => 'width:98%; height:400px;',
            'wysiwyg'   => false,
            'required'  => true,
        ));
       $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('Jobs')->__('Online application email'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'email',
        ));
        if ( Mage::getSingleton('adminhtml/session')->getJobsData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getJobsData());
            Mage::getSingleton('adminhtml/session')->setJobsData(null);
        } elseif ( Mage::registry('Jobs_data') ) {
            $form->setValues(Mage::registry('Jobs_data')->getData());
        }
        return parent::_prepareForm();
    }
}