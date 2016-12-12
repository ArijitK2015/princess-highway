<?php

/**
 * Class FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Preferredbrands
 */
class FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Preferredbrands extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $optionsArray;

    /**
     *
     */
    public function __construct()
    {
        $this->optionsArray = array();

        $this->addColumn('brand', array(
            'label' => Mage::helper('adminhtml')->__('Brand Name'),
            'size'  => 40,
        ));
        $this->addColumn('list', array(
            'label' => Mage::helper('adminhtml')->__('API Subscriber List ID'),
            'size'  => 40,
        ));
        $this->addColumn('img_url', array(
            'label' => Mage::helper('adminhtml')->__('Brand Image URLs, use ";" to separate'),
            'size'  => 40,
        ));
        $this->addColumn('template', array(
            'label' => Mage::helper('adminhtml')->__('Email Template'),
            'size'  => 30,
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add brand');

        parent::__construct();
        $this->setTemplate('factoryx/campaignmonitor/system/config/form/field/array_dropdown.phtml');

        // subscriber options
        $templateCollection =  Mage::getResourceSingleton('core/email_template_collection');
        foreach($templateCollection as $template) {
            $this->optionsArray['template'][$template->getTemplateId()] = $template->getTemplateCode();
        }
        if (array_key_exists('template', $this->optionsArray)) {
            asort($this->optionsArray['template']);
        }
    }

    /**
     * @param string $columnName
     * @return string
     * @throws Exception
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $column     = $this->_columns[$columnName];
        $inputName  = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';
        if (isset($this->optionsArray[$columnName])){
            $rendered = '<select name="'.$inputName.'">';
            $rendered .= '<option value=""></option>';
            foreach($this->optionsArray[$columnName] as $att => $name) {
                $rendered .= '<option value="'.$att.'">'.$name.'</option>';
            }
            $rendered .= '</select>';
        } else {
            return '<input type="text" class="input-text" name="' . $inputName . '" value="#{' . $columnName . '}" ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . '/>';
        }

        return $rendered;
    }
}
