<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Homepage_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("2.3.16");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('homepage','FactoryX_Homepage_Helper_Data');

        // Blocks
        $this->assertBlockAlias('homepage/homepage','FactoryX_Homepage_Block_Homepage');
        $this->assertBlockAlias('homepage/homepage_blocks_cell','FactoryX_Homepage_Block_Homepage_Blocks_Cell');
        $this->assertBlockAlias('homepage/homepage_blocks_slider','FactoryX_Homepage_Block_Homepage_Blocks_Slider');
        $this->assertBlockAlias('homepage/homepages','FactoryX_Homepage_Block_Homepages');
        $this->assertBlockAlias('homepage/adminhtml_homepage','FactoryX_Homepage_Block_Adminhtml_Homepage');
        $this->assertBlockAlias('homepage/adminhtml_homepage_chooselayout','FactoryX_Homepage_Block_Adminhtml_Homepage_Chooselayout');
        $this->assertBlockAlias('homepage/adminhtml_homepage_chooselayout_form','FactoryX_Homepage_Block_Adminhtml_Homepage_Chooselayout_Form');
        $this->assertBlockAlias('homepage/adminhtml_homepage_details','FactoryX_Homepage_Block_Adminhtml_Homepage_Details');
        $this->assertBlockAlias('homepage/adminhtml_homepage_details_form','FactoryX_Homepage_Block_Adminhtml_Homepage_Details_Form');
        $this->assertBlockAlias('homepage/adminhtml_homepage_edit','FactoryX_Homepage_Block_Adminhtml_Homepage_Edit');
        $this->assertBlockAlias('homepage/adminhtml_homepage_edit_form','FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Form');
        $this->assertBlockAlias('homepage/adminhtml_homepage_edit_tabs','FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tabs');
        $this->assertBlockAlias('homepage/adminhtml_homepage_edit_tab_general','FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_General');
        $this->assertBlockAlias('homepage/adminhtml_homepage_edit_tab_media','FactoryX_Homepage_Block_Adminhtml_Homepage_Edit_Tab_Media');
        $this->assertBlockAlias('homepage/adminhtml_homepage_grid','FactoryX_Homepage_Block_Adminhtml_Homepage_Grid');
        $this->assertBlockAlias('homepage/adminhtml_homepage_grid_renderer_layoutImage','FactoryX_Homepage_Block_Adminhtml_Homepage_Grid_Renderer_LayoutImage');
        $this->assertBlockAlias('homepage/adminhtml_template_edit_renderer_button','FactoryX_Homepage_Block_Adminhtml_Template_Edit_Renderer_Button');
        $this->assertBlockAlias('homepage/adminhtml_template_grid_renderer_action','FactoryX_Homepage_Block_Adminhtml_Template_Grid_Renderer_Action');

        // Models
        $this->assertModelAlias('homepage/homepage','FactoryX_Homepage_Model_Homepage');
        $this->assertModelAlias('homepage/image','FactoryX_Homepage_Model_Image');
        $this->assertModelAlias('homepage/observer','FactoryX_Homepage_Model_Observer');
        $this->assertModelAlias('homepage/status','FactoryX_Homepage_Model_Status');
        $this->assertModelAlias('homepage/homepage_media_config','FactoryX_Homepage_Model_Homepage_Media_Config');
        $this->assertModelAlias('homepage/varien_data_form_element_homepageImage','FactoryX_Homepage_Model_Varien_Data_Form_Element_HomepageImage');
        $this->assertModelAlias('homepage/varien_data_form_element_layoutImage','FactoryX_Homepage_Model_Varien_Data_Form_Element_LayoutImage');
        $this->assertModelAlias('homepage/varien_data_form_element_radioImage','FactoryX_Homepage_Model_Varien_Data_Form_Element_RadioImage');

        // Resources Models
        $this->assertResourceModelAlias('homepage/homepage','FactoryX_Homepage_Model_Mysql4_Homepage');
        $this->assertResourceModelAlias('homepage/homepage_collection','FactoryX_Homepage_Model_Mysql4_Homepage_Collection');
        $this->assertResourceModelAlias('homepage/image','FactoryX_Homepage_Model_Mysql4_Image');
        $this->assertResourceModelAlias('homepage/image_collection','FactoryX_Homepage_Model_Mysql4_Image_Collection');

    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/homepage.xml');
        $this->assertLayoutFileExists('frontend','factoryx/homepage.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/homepage.xml','default','base');

    }
}