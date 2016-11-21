<?php

/**
 * Who:  Alvin Nguyen
 * When: 3/02/15
 * Why:
 */
class FactoryX_CreditmemoReasons_Block_Adminhtml_Reason_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _getModel()
    {
        return Mage::registry('current_model');
    }

    protected function _getHelper()
    {
        return Mage::helper('creditmemoreasons');
    }

    /**
     * @return string
     */
    protected function _getModelTitle()
    {
        return 'Reason';
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

        if ($model && $model->getId()) {
            $modelPk = $model->getResource()->getIdFieldName();
            $fieldset->addField($modelPk, 'hidden', array(
                'name' => $modelPk,
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'     => 'title',
            'label'    => $this->_getHelper()->__('Reason Title'),
            'required' => true
        ));

        $fieldset->addField('identifier', 'text', array(
            'name'     => 'identifier',
            'label'    => $this->_getHelper()->__('Reason Identifier'),
            'required' => true
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'     => 'sort_order',
            'label'    => $this->_getHelper()->__('Sort Order')
        ));

        if ($model) {
            $form->setValues($model->getData());
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
