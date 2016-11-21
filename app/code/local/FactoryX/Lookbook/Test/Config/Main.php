<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Lookbook_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.7.10");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('lookbook','FactoryX_Lookbook_Helper_Data');
        $this->assertHelperAlias('lookbook/image','FactoryX_Lookbook_Helper_Image');

        // Blocks
        $this->assertBlockAlias('lookbook/lookbook','FactoryX_Lookbook_Block_Lookbook');
        $this->assertBlockAlias('lookbook/lookbook_navigation','FactoryX_Lookbook_Block_Lookbook_Navigation');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook','FactoryX_Lookbook_Block_Adminhtml_Lookbook');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_tabs','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tabs');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_tab_credits','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Credits');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_tab_developer','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Developer');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_tab_general','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_General');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_tab_media','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Media');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_tab_media_gallery_content','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Media_Gallery_Content');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_tab_socialMedia','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_SocialMedia');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_tab_store','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Tab_Store');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_edit_form','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Edit_Form');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_grid','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Grid');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_choosecat','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Choosecat');
        $this->assertBlockAlias('lookbook/adminhtml_lookbook_choosecat_form','FactoryX_Lookbook_Block_Adminhtml_Lookbook_Choosecat_Form');
        $this->assertBlockAlias('lookbook/adminhtml_template_grid_renderer_action','FactoryX_Lookbook_Block_Adminhtml_Template_Grid_Renderer_Action');

        // Models
        $this->assertModelAlias('lookbook/lookbook','FactoryX_Lookbook_Model_Lookbook');
        $this->assertModelAlias('lookbook/lookbook_image','FactoryX_Lookbook_Model_Lookbook_Image');
        $this->assertModelAlias('lookbook/lookbook_media','FactoryX_Lookbook_Model_Lookbook_Media');
        $this->assertModelAlias('lookbook/lookbook_media_config','FactoryX_Lookbook_Model_Lookbook_Media_Config');
        $this->assertModelAlias('lookbook/status','FactoryX_Lookbook_Model_Status');
        $this->assertModelAlias('lookbook/varien_data_form_element_categorySelect','FactoryX_Lookbook_Model_Varien_Data_Form_Element_CategorySelect');
        $this->assertModelAlias('lookbook/varien_data_form_element_imageGallery','FactoryX_Lookbook_Model_Varien_Data_Form_Element_ImageGallery');
        $this->assertModelAlias('lookbook/varien_data_form_element_shopthelookImage','FactoryX_Lookbook_Model_Varien_Data_Form_Element_ShopthelookImage');

        // Resources Models
        $this->assertResourceModelAlias('lookbook/lookbook','FactoryX_Lookbook_Model_Mysql4_Lookbook');
        $this->assertResourceModelAlias('lookbook/lookbook_collection','FactoryX_Lookbook_Model_Mysql4_Lookbook_Collection');
        $this->assertResourceModelAlias('lookbook/lookbook_media','FactoryX_Lookbook_Model_Mysql4_Lookbook_Media');
        $this->assertResourceModelAlias('lookbook/lookbook_media_collection','FactoryX_Lookbook_Model_Mysql4_Lookbook_Media_Collection');

    }

    public function testLayoutDefinitions()
    {
        // Backend
        $this->assertLayoutFileDefined('adminhtml','factoryx/lookbook.xml');
        $this->assertLayoutFileExists('adminhtml','factoryx/lookbook.xml');
        $this->assertLayoutFileExistsInTheme('adminhtml','factoryx/lookbook.xml','default','default');

        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/lookbook.xml');
        $this->assertLayoutFileExists('frontend','factoryx/lookbook.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/lookbook.xml','default','base');

    }
}