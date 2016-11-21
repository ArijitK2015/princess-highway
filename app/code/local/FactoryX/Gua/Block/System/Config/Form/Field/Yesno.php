<?php

/**
 * Class FactoryX_Gua_Block_System_Config_Form_Field_Yesno
 */
class FactoryX_Gua_Block_System_Config_Form_Field_Yesno extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Override method to output our custom HTML with JavaScript
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        // If our Yes/No toggle is 'Yes' then set the other field to 'No'
        if($element->getValue()==1) {
            $_frm = $element->getForm();
			if ($element->getId() == "gua_ecommerce_enable")
			{
				$_elm = $_frm->getElement("gua_ecommerce_enableec");
				$_elm->setValue(0);
			}
			else
			{
				$_elm = $_frm->getElement("gua_ecommerce_enable");
				$_elm->setValue(0);
			}
        }
        // Get the default HTML for this option
        $html = parent::_getElementHtml($element);
		// Set up additional JavaScript for our toggle action.
        $javaScript = "
            <script type=\"text/javascript\">
                var options,enabled,enabledec;
                Event.observe('gua_ecommerce_enable', 'change', function(){
                    enabled=$('gua_ecommerce_enable').value;
					options = $$('select#gua_ecommerce_enableec option');
                    if (enabled) {
						options[1].selected = true;
                        // $('gua_ecommerce_enableec').value = 0;
                    } else {
						options[0].selected = true;
                        // $('gua_ecommerce_enableec').value = 1;
                    }
                });
				Event.observe('gua_ecommerce_enableec', 'change', function(){
                    enabledec=$('gua_ecommerce_enableec').value;
					options = $$('select#gua_ecommerce_enable option');
                    if (enabledec) {
						options[1].selected = true;
                        // $('gua_ecommerce_enable').value = 0;
                    } else {
						options[0].selected = true;
                        // $('gua_ecommerce_enable').value = 1;
                    }
                });
            </script>";
 
        $html .= $javaScript;
        return $html;
    }
}