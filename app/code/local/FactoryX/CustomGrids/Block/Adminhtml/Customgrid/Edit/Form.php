<?php

/**
 * Class FactoryX_CustomGrids_Block_Adminhtml_Customgrid_Edit_Form
 */
class FactoryX_CustomGrids_Block_Adminhtml_Customgrid_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _getModel()
    {
        return Mage::registry('current_model');
    }

    protected function _getHelper()
    {
        return Mage::helper('customgrids');
    }

    /**
     * @return string
     */
    protected function _getModelTitle()
    {
        return 'Column';
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

        $allowedBlockTypes = Mage::getSingleton('customgrids/config')->getAllowedBlocksToOptionArray();

        $fieldset->addField('grid_block_type', 'select', array(
            'name'     => 'grid_block_type',
            'label'    => $this->_getHelper()->__('Grid Block Type'),
            'required' => true,
            'values' => $allowedBlockTypes
        ));

        if (Mage::app()->getRequest()->getParam('remove') || $model->getRemove())
        {
            $fieldset->addField('remove', 'hidden', array(
                'name' => 'remove'
            ));

            foreach ($allowedBlockTypes as $blockType)
            {
                $relatedEntityType = Mage::getSingleton('customgrids/config')->getRelatedEntityType($blockType['value']);
                $fieldset->addField('remove_column_' . $blockType['value'], 'select', array(
                    'name' => 'remove_column_' . $blockType['value'],
                    'label' => $this->_getHelper()->__('Column to remove for %s', $blockType['label']),
                    'values' => Mage::getSingleton('customgrids/config_' . $relatedEntityType)->getDefaultCollection($blockType['value']),
                    'class' => 'remove-column-block'
                ));
            }

        }
        else
        {
            foreach ($allowedBlockTypes as $blockType)
            {
                $relatedEntityType = Mage::getSingleton('customgrids/config')->getRelatedEntityType($blockType['value']);
                $fieldset->addField('attribute_code_'.$blockType['value'], 'select', array(
                    'name'     => 'attribute_code_'.$blockType['value'],
                    'label'    => $this->_getHelper()->__('Attribute Id for %s',$blockType['label']),
                    'values'   => Mage::getSingleton('customgrids/config_'.$relatedEntityType)->getAttributeCollection($blockType['value'],true),
                    'class' => 'attribute-code-block'
                ));

                $fieldset->addField('after_column_'.$blockType['value'], 'select', array(
                    'name'     => 'after_column_'.$blockType['value'],
                    'label'    => $this->_getHelper()->__('Add column after'),
                    'values'   => Mage::getSingleton('customgrids/config_'.$relatedEntityType)->getAfterColumns($blockType['value']),
                    'class' => 'after-column-block'
                ));
            }
        }

        $adminRoles = $this->_getHelper()->getAdminRoles();

        $fieldset->addField('admin_roles', 'multiselect', array(
            'name'     => 'admin_roles',
            'label'    => $this->_getHelper()->__('Allowed Admin Roles'),
            'required' => true,
            'values' => $adminRoles
        ));

        if ($model) {
            $data = $model->getData();
            if (array_key_exists('attribute_code',$data))
            {
                $attribute = $data['attribute_code'];
                unset($data['attribute_code']);
                $data['attribute_code_'.$data['grid_block_type']] = $attribute;
            }
            if (Mage::app()->getRequest()->getParam('remove') || $model->getRemove())
            {
                $data['remove'] = 1;
            }
            $form->setValues($data);
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
