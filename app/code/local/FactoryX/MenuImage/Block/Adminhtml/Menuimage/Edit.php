<?php

/**
 * Class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Edit
 */
class FactoryX_MenuImage_Block_Adminhtml_Menuimage_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     *	Constructor for the Edit page
     */
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'menuimage';
        $this->_controller = 'adminhtml_menuimage';
        $this->_updateButton('save', 'label', Mage::helper('menuimage')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('menuimage')->__('Delete'));

        // Add the Save and Continue button
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('menuimage')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }";

        $blockCount = Mage::helper('menuimage')->getBlockCount();

        for ($i = 1; $i<=$blockCount;$i++)
        {
            $this->_formScripts[] = "
            Event.observe(window, 'load', function(){
                if ($('type_$i').selectedIndex == 2)
                {
                }
                else if ($('type_$i').selectedIndex != 0)
                {
                    var id = $$('input[name=\"product_id_$i\"]')[0].id;
                    $(id).parentNode.parentNode.hide();
                    $(id).parentNode.parentNode.previous().hide();
                }
                else
                {
                    var id = $$('input[name=\"product_id_$i\"]')[0].id;
                    $(id).parentNode.parentNode.hide();
                    $(id).parentNode.parentNode.previous().hide();
                }
			});
            Event.observe($('type_$i'),'change', function(){
                    if ($('type_$i').selectedIndex == 2)
                    {
                        var id = $$('input[name=\"product_id_$i\"]')[0].id;
                        $(id).parentNode.parentNode.show();
                        $(id).parentNode.parentNode.previous().show();
                    }
                    else if ($('type_$i').selectedIndex != 0)
                    {
                        var id = $$('input[name=\"product_id_$i\"]')[0].id;
                        $(id).parentNode.parentNode.hide();
                        $(id).parentNode.parentNode.previous().hide();
                        $(id).value = '';
                    }
                    else
                    {
                        var id = $$('input[name=\"product_id_$i\"]')[0].id;
                        $(id).parentNode.parentNode.hide();
                        $(id).parentNode.parentNode.previous().hide();
                        $(id).value = '';
                    }
                });";
        }

        $this->_formScripts[] = "
            Event.observe(window, 'load', function(){
                if ($('category_text'))
				{
				    document.getElementById('category_id').value = $('category_text').value;
				}
            });";
    }

    /**
     *	Getter for the header text
     */
    public function getHeaderText()
    {
        if( Mage::registry('menuimage_data') && Mage::registry('menuimage_data')->getId() )
        {
            return Mage::helper('menuimage')->__("Editing Menu Image");
        }
        else
        {
            return Mage::helper('menuimage')->__('Add Menu Image');
        }
    }
}