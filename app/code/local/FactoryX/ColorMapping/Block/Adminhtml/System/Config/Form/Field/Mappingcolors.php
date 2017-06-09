<?php

/**
 * Class FactoryX_ColorMapping_Block_Adminhtml_System_Config_Form_Field_Mappingcolors
 */
class FactoryX_ColorMapping_Block_Adminhtml_System_Config_Form_Field_Mappingcolors extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $optionsArray;

    /**
     *
     */
    public function __construct()
    {
        $this->optionsArray = array();

        $this->addColumn('basecolors', array(
            'label' => Mage::helper('colormapping')->__('Base Color'),
            'size'  => 50,
        ));        
        $this->addColumn('colors', array(
            'label' => Mage::helper('colormapping')->__('Color'),
            'size'  => 50,
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add linked color');
        
        parent::__construct();
        $this->setTemplate('factoryx/colormapping/system/config/form/field/mappingcolors.phtml');
        
        // Base colors
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', Mage::helper('colormapping')->getBaseColour());
		if ($attribute->usesSource()) 
		{
			$options = $attribute->getSource()->getAllOptions(false);
			$this->optionsArray['basecolors'] = array();
			foreach($options as $option)
			{
				$this->optionsArray['basecolors'][$option['value']] = $option['label'];
			}
		}
        asort($this->optionsArray['basecolors']);

        // Colors
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', Mage::helper('colormapping')->getColour());
		if ($attribute->usesSource()) 
		{
			// Use frontend values for the color
			$options = $attribute->setStoreId(1)->getSource()->getAllOptions(false);               
			$this->optionsArray['colors'] = array();
			foreach($options as $option)
			{
				$this->optionsArray['colors'][$option['value']] = $option['label'];
			}
		}
        asort($this->optionsArray['colors']);
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
