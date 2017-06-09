<?php

/**
 * Class FactoryX_AskStylist_Block_Adminhtml_System_Config_Form_Field_Linkedemails
 */
class FactoryX_AskStylist_Block_Adminhtml_System_Config_Form_Field_Linkedemails extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $optionsArray;

    /**
     *
     */
    public function __construct()
    {
        $this->optionsArray = array();

        $this->addColumn('enquiry', array(
            'label' => Mage::helper('adminhtml')->__('Enquiry Field Value'),
            'size'  => 40,
        ));
        $this->addColumn('email', array(
            'label' => Mage::helper('adminhtml')->__('Email'),
            'size'  => 40,
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add linked email');

        parent::__construct();
        $this->setTemplate('factoryx/askstylist/system/config/form/field/array_dropdown.phtml');

        $this->optionsArray['email'] = array();
        // Get the possible emails
        $config = Mage::getSingleton('adminhtml/config')->getSection('trans_email')->groups->children();
        // Loop through them
        foreach ($config as $node)
        {
            $this->optionsArray['email'][$node->getName()] = Mage::helper('adminhtml')->__((string) $node->label);
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
