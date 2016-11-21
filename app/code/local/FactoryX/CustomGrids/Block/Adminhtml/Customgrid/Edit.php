<?php

/**
 * Class FactoryX_CustomGrids_Block_Adminhtml_Customgrid_Edit
 */
class FactoryX_CustomGrids_Block_Adminhtml_Customgrid_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *
     */
    public function __construct()
    {
        // $this->_objectId = 'id';
        parent::__construct();
        $this->_blockGroup      = 'customgrids';
        $this->_controller      = 'adminhtml_customgrid';
        $this->_mode            = 'edit';
        $modelTitle = $this->_getModelTitle();
        $this->_updateButton('save', 'label', $this->_getHelper()->__("Save $modelTitle"));
        $this->_addButton('saveandcontinue', array(
            'label'     => $this->_getHelper()->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

        $data = Mage::registry('current_model');
        $modelType = $data->getGridBlockType() ? $data->getGridBlockType() : 'Mage_Adminhtml_Block_Catalog_Product_Grid';

        $allowedBlockTypes = Mage::getSingleton('customgrids/config')->getAllowedBlocksToOptionArray();

        foreach ($allowedBlockTypes as $allowedBlockType)
        {
            $blockType = $allowedBlockType['value'];
            if ($blockType != $modelType)
            {
                if (Mage::app()->getRequest()->getParam('remove') || $data->getRemove())
                {
                    $this->_formScripts[] = "
                    Event.observe(window, 'load', function(){
                        $('remove_column_$blockType').parentNode.parentNode.hide();
                    });";
                }
                else
                {
                    $this->_formScripts[] = "
                    Event.observe(window, 'load', function(){
                        $('attribute_code_$blockType').parentNode.parentNode.hide();
                        $('after_column_$blockType').parentNode.parentNode.hide();
                    });";
                }
            }
        }

        if (Mage::app()->getRequest()->getParam('remove') || $data->getRemove())
        {
            $this->_formScripts[] = "
            Event.observe(window, 'load', function(){
                $('remove_column_$modelType').parentNode.parentNode.show();
            });";


            $this->_formScripts[] = "
            Event.observe($('grid_block_type'),'change', function(){
                $$('.remove-column-block').each(function(item){
                    item.parentNode.parentNode.hide();
                });
                var value = $('grid_block_type').value;
                $('remove_column_'+value).parentNode.parentNode.show();
            });";
        }
        else
        {
            $this->_formScripts[] = "
            Event.observe(window, 'load', function(){
                $('attribute_code_$modelType').parentNode.parentNode.show();
                $('after_column_$modelType').parentNode.parentNode.show();
            });";


            $this->_formScripts[] = "
            Event.observe($('grid_block_type'),'change', function(){
                $$('.attribute-code-block').each(function(item){
                    item.parentNode.parentNode.hide();
                });
                var value = $('grid_block_type').value;
                $('attribute_code_'+value).parentNode.parentNode.show();
                $$('.after-column-block').each(function(item){
                    item.parentNode.parentNode.hide();
                });
                var value = $('grid_block_type').value;
                $('after_column_'+value).parentNode.parentNode.show();
            });";
        }
    }

    protected function _getHelper(){
        return Mage::helper('customgrids');
    }

    protected function _getModel(){
        return Mage::registry('current_model');
    }

    /**
     * @return string
     */
    protected function _getModelTitle(){
        return 'Column';
    }

    /**
     * @return mixed
     */
    public function getHeaderText()
    {
        $model = $this->_getModel();
        $modelTitle = $this->_getModelTitle();
        if ($model && $model->getId()) {
           return $this->_getHelper()->__("Edit $modelTitle (ID: {$model->getId()})");
        }
        else {
           return $this->_getHelper()->__("New $modelTitle");
        }
    }


    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }

    /**
     * @return mixed
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    /**
     * Get form save URL
     *
     * @deprecated
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
        $this->setData('form_action_url', 'save');
        return $this->getFormActionUrl();
    }


}
