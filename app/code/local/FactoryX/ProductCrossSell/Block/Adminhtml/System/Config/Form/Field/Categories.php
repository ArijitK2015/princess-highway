<?php

/**
 * Class FactoryX_ProductCrossSell_Block_Adminhtml_System_Config_Form_Field_Categories
 */
class FactoryX_ProductCrossSell_Block_Adminhtml_System_Config_Form_Field_Categories extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $optionsArray;

    /**
     *
     */
    public function __construct()
    {
        $this->optionsArray = array();

        $this->addColumn('categories', array(
            'label' => Mage::helper('adminhtml')->__('Product Category'),
            'size'  => 40,
        ));
        $this->addColumn('images', array(
            'label' => Mage::helper('adminhtml')->__('Custom Image'),
            'size'  => 40,
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add linked custom image');

        parent::__construct();
        $this->setTemplate('factoryx/crosssells/system/config/form/field/array_dropdown.phtml');

        $this->optionsArray['categories'] = Mage::helper('productcrosssell')->getProductCategories();

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
            foreach($this->optionsArray[$columnName] as $att => $name)
            {
                $rendered .= '<option value="'.$att.'">'.$name.'</option>';
            }
            $rendered .= '</select>';
        }
        else
        {
            return '<input type="text" class="input-text" name="' . $inputName . '" value="#{' . $columnName . '}" ' . ($column['size'] ? 'size="' . $column['size'] . '"' : '') . '/>';
        }

        return $rendered;
    }
}
