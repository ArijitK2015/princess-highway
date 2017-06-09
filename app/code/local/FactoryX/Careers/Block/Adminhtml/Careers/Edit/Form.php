<?php
 
class FactoryX_Careers_Block_Adminhtml_Careers_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                                        'id' => 'edit_form',
                                        'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                        'method' => 'post',
                                     )
        );
 
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('career_form', array('legend'=>Mage::helper('careers')->__('Career information')));

        $fieldset->addField('position', 'text', array(
            'label'     => Mage::helper('careers')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'position',
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('careers')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('careers')->__('Active'),
                ),

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('careers')->__('Inactive'),
                ),
            ),
        ));

        $afterElementHtml = '<p class="nm"><small>area/territory/area etc these are used to group the lists and render the list links</small></p>';
        $areas = Mage::helper('careers')->getStates();
        $values = array();
        foreach($areas as $area){
            $values[] = array('value'=>$area,'label'=>$area);
        }
        $fieldset->addField('area', 'select', array(
            'label'     => Mage::helper('careers')->__('Area'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'area',
            'values'    => $values,
            'after_element_html'    => $afterElementHtml
        ));

        $fieldset->addField('locations', 'text', array(
            'label'     => Mage::helper('careers')->__('Location'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'locations',
        ));
        $workTypes = Mage::helper('careers')->getWorkTypes();
        $values = array();
        foreach($workTypes as $workType){
            $values[] = array('value'=>$workType,'label'=>$workType);
        }
        $fieldset->addField('work_type', 'select', array(
            'label'     => Mage::helper('careers')->__('Work Type'),
            'name'      => 'work_type',
            'values'    => $values
        ));

        $fieldset->addField('hours', 'text', array(
            'label'     => Mage::helper('careers')->__('Hours'),
            'class'     => '',
            'required'  => false,
            'name'      => 'hours',
        ));

        $fieldset->addField('entitlements', 'text', array(
            'label'     => Mage::helper('careers')->__('Entitlements'),
            'class'     => '',
            'required'  => false,
            'name'      => 'entitlements',
        ));

        $fieldset->addField('requirements', 'editor', array(
            'name'      => 'requirements',
            'label'     => Mage::helper('careers')->__('Requirements'),
            'title'     => Mage::helper('careers')->__('requirements'),
            'wysiwyg'   => false,
            'required'  => true,
        ));

        $afterElementHtml = '<p class="nm"><small>enter an email addrss for a mailto anchor OR a relative url for webforms etc</small></p>';
        $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('careers')->__('Link to'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'email',
            'after_element_html'    => $afterElementHtml
        ));

        $fieldset->addField('sort', 'text', array(
            'label'     => Mage::helper('careers')->__('Sort Order'),
            'class'     => 'validate-number',
            'required'  => true,
            'name'      => 'sort',
            'value'     => '0'
        ));


        if ( Mage::getSingleton('adminhtml/session')->getCareersData() )
        {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCareersData());
            Mage::getSingleton('adminhtml/session')->setCareersData(null);
        }
        elseif(Mage::registry('careers_data'))
        {
            $data = Mage::registry('careers_data')->getData();
            $form->setValues($data);
        }

        return parent::_prepareForm();
    }
}