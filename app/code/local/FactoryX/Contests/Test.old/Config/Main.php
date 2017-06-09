<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Contests_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.3.13");
        $this->assertModuleDepends('FactoryX_CampaignMonitor');
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('contests','FactoryX_Contests_Helper_Data');

        // Models
        $this->assertModelAlias('contests/contest','FactoryX_Contests_Model_Contest');
        $this->assertModelAlias('contests/referrer','FactoryX_Contests_Model_Referrer');
        $this->assertModelAlias('contests/referee','FactoryX_Contests_Model_Referee');
        $this->assertModelAlias('contests/status','FactoryX_Contests_Model_Status');
        $this->assertModelAlias('contests/contest_media_config','FactoryX_Contests_Model_Contest_Media_Config');
        $this->assertModelAlias('contests/varien_data_form_element_contestImage','FactoryX_Contests_Model_Varien_Data_Form_Element_ContestImage');

        // Resource Models
        $this->assertResourceModelAlias('contests/contest','FactoryX_Contests_Model_Mysql4_Contest');
        $this->assertResourceModelAlias('contests/referrer','FactoryX_Contests_Model_Mysql4_Referrer');
        $this->assertResourceModelAlias('contests/referee','FactoryX_Contests_Model_Mysql4_Referee');
        $this->assertResourceModelAlias('contests/contest_collection','FactoryX_Contests_Model_Mysql4_Contest_Collection');
        $this->assertResourceModelAlias('contests/referrer_collection','FactoryX_Contests_Model_Mysql4_Referrer_Collection');
        $this->assertResourceModelAlias('contests/referee_collection','FactoryX_Contests_Model_Mysql4_Referee_Collection');

        // Blocks
        $this->assertBlockAlias('contests/contest','FactoryX_Contests_Block_Contest');
        $this->assertBlockAlias('contests/contest_list','FactoryX_Contests_Block_Contest_List');
        $this->assertBlockAlias('contests/contest_popup','FactoryX_Contests_Block_Contest_Popup');
        $this->assertBlockAlias('contests/contest_terms','FactoryX_Contests_Block_Contest_Terms');
        $this->assertBlockAlias('contests/contest_thankyou','FactoryX_Contests_Block_Contest_Thankyou');
        $this->assertBlockAlias('contests/adminhtml_system_config_form_button','FactoryX_Contests_Block_Adminhtml_System_Config_Form_Button');
        $this->assertBlockAlias('contests/adminhtml_template_grid_renderer_action','FactoryX_Contests_Block_Adminhtml_Template_Grid_Renderer_Action');
        $this->assertBlockAlias('contests/adminhtml_contests','FactoryX_Contests_Block_Adminhtml_Contests');
        $this->assertBlockAlias('contests/adminhtml_linkedattributes','FactoryX_Contests_Block_Adminhtml_Linkedattributes');
        $this->assertBlockAlias('contests/adminhtml_referees','FactoryX_Contests_Block_Adminhtml_Referees');
        $this->assertBlockAlias('contests/adminhtml_referees_grid','FactoryX_Contests_Block_Adminhtml_Referees_Grid');
        $this->assertBlockAlias('contests/adminhtml_referrers','FactoryX_Contests_Block_Adminhtml_Referrers');
        $this->assertBlockAlias('contests/adminhtml_referrers_grid','FactoryX_Contests_Block_Adminhtml_Referrers_Grid');
        $this->assertBlockAlias('contests/adminhtml_contests_draw','FactoryX_Contests_Block_Adminhtml_Contests_Draw');
        $this->assertBlockAlias('contests/adminhtml_contests_edit','FactoryX_Contests_Block_Adminhtml_Contests_Edit');
        $this->assertBlockAlias('contests/adminhtml_contests_grid','FactoryX_Contests_Block_Adminhtml_Contests_Grid');
        $this->assertBlockAlias('contests/adminhtml_contests_draw_form','FactoryX_Contests_Block_Adminhtml_Contests_Draw_Form');
        $this->assertBlockAlias('contests/adminhtml_contests_draw_tabs','FactoryX_Contests_Block_Adminhtml_Contests_Draw_Tabs');
        $this->assertBlockAlias('contests/adminhtml_contests_draw_tab_general','FactoryX_Contests_Block_Adminhtml_Contests_Draw_Tab_General');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_form','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Form');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tabs','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tabs');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_colour','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Colour');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_competition','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Competition');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_general','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_General');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_list','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_List');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_media','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Media');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_popup','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Popup');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_store','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Store');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_terms','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Terms');
        $this->assertBlockAlias('contests/adminhtml_contests_edit_tab_winners','FactoryX_Contests_Block_Adminhtml_Contests_Edit_Tab_Winners');
        $this->assertBlockAlias('contests/adminhtml_contests_grid_renderer_listImageUrl','FactoryX_Contests_Block_Adminhtml_Contests_Grid_Renderer_ListImageUrl');
    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/contests.xml');
        $this->assertLayoutFileExists('frontend','factoryx/contests.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/contests.xml','default','base');
    }
}