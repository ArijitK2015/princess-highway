<?php

/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why:
 */
class FactoryX_NotificationBoard_Block_Adminhtml_Notification_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _getModel()
    {
        return Mage::registry('current_model');
    }

    protected function _getHelper()
    {
        return Mage::helper('factoryx_notificationboard');
    }

    /**
     * @return string
     */
    protected function _getModelTitle()
    {
        return 'Notification';
    }

    /**
     * @return mixed
     */
    protected function _prepareForm()
    {
        $model      = $this->_getModel();
        $modelTitle = $this->_getModelTitle();
        $form       = new Varien_Data_Form(array(
            'id'     => 'edit_form',
            'action' => $this->getUrl('*/*/save'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $this->_getHelper()->__("$modelTitle Information"),
            'class'  => 'fieldset-wide',
        ));

        // We retrieve the data from the session or the registered data
        if ($model->getData())
        {
            $data = $model->getData();
        }

        // Dummy values for empty dates
        if (
            is_array($data)
            &&
            array_key_exists('start_date', $data)
            &&
            $data['start_date'] == "0000-00-00 00:00:00"
        ) {
            $data['start_date'] = "";
        }
        if (
            is_array($data)
            &&
            array_key_exists('end_date', $data)
            &&
            $data['end_date'] == "0000-00-00 00:00:00"
        ) {
            $data['end_date'] = "";
        }

        if ($model && $model->getId()) {
            $modelPk = $model->getResource()->getIdFieldName();
            $fieldset->addField($modelPk, 'hidden', array(
                'name' => $modelPk,
            ));
        }

        $fieldset->addField('notification_title', 'text', array(
            'name'     => 'notification_title',
            'label'    => $this->_getHelper()->__('Notification Title'),
            'title'    => $this->_getHelper()->__('The title of notification, for back-end management only'),
            'required' => true,
        ));

        $fieldset->addField('status', 'select', array(
            'name'     => 'status',
            'label'    => $this->_getHelper()->__('Notification Status'),
            'required' => true,
            'options'  => array(
                0 => 'Disable',
                1 => 'Enable'
            )
        ));

        // Output format for the start and end dates
        $outputFormat = "dd/MM/yyyy";

        $fieldset->addField('start_date', 'date', array(
            'name'         => 'start_date',
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'label'        => $this->_getHelper()->__('Start Date'),
            'title'        => $this->_getHelper()->__('Start Date'),
            'format'       => $outputFormat,
            'class'        => 'validate-date-au',
            'style'        => 'width:140px!important',
            'note'         => $this->_getHelper()->__('Defaults to 00:00:00')
        ));

        $fieldset->addField('end_date', 'date', array(
            'name'         => 'end_date',
            'image'        => $this->getSkinUrl('images/grid-cal.gif'),
            'label'        => $this->_getHelper()->__('End Date'),
            'title'        => $this->_getHelper()->__('End Date'),
            'format' => $outputFormat,
            'class'        => 'validate-date-au',
            'style'        => 'width:140px!important',
            'note'         => $this->_getHelper()->__('Defaults to 23:59:59')
        ));

        $fieldset->addField('message', 'textarea', array(
            'name'     => 'message',
            'label'    => $this->_getHelper()->__('Notification Message'),
            'title'    => $this->_getHelper()->__('Message that gets displayed on the front-end'),
            'required' => true,
        ));

        $fieldset->addField('style', 'textarea', array(
            'name'     => 'style',
            'label'    => $this->_getHelper()->__('Extra Style'),
            'required' => false,
        ));


//        $fieldset->addField('name', 'text' /* select | multiselect | hidden | password | ...  */, array(
//            'name'      => 'name',
//            'label'     => $this->_getHelper()->__('Label here'),
//            'title'     => $this->_getHelper()->__('Tooltip text here'),
//            'required'  => true,
//            'options'   => array( OPTION_VALUE => OPTION_TEXT, ),                 // used when type = "select"
//            'values'    => array(array('label' => LABEL, 'value' => VALUE), ),    // used when type = "multiselect"
//            'style'     => 'css rules',
//            'class'     => 'css classes',
//        ));
//          // custom renderer (optional)
//          $renderer = $this->getLayout()->createBlock('Block implementing Varien_Data_Form_Element_Renderer_Interface');
//          $field->setRenderer($renderer);

//      // New Form type element (extends Varien_Data_Form_Element_Abstract)
//        $fieldset->addType('custom_element','MyCompany_MyModule_Block_Form_Element_Custom');  // you can use "custom_element" as the type now in ::addField([name], [HERE], ...)


        if ($model) {
            $form->setValues($model->getData());
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
