<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_CampaignMonitor_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.1.24");
        $this->assertModuleDepends('Mage_Checkout');
        $this->assertModuleDepends('FactoryX_ChooserWidget');
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('campaignmonitor','FactoryX_CampaignMonitor_Helper_Data');

        // Models
        $this->assertModelAlias('campaignmonitor/auth','FactoryX_CampaignMonitor_Model_Auth');
        $this->assertModelAlias('campaignmonitor/subscriber','FactoryX_CampaignMonitor_Model_Subscriber');
        $this->assertModelAlias('campaignmonitor/system_config_source_authtype','FactoryX_CampaignMonitor_Model_System_Config_Source_Authtype');
        $this->assertModelAlias('campaignmonitor/customer_observer','FactoryX_CampaignMonitor_Model_Customer_Observer');
        $this->assertModelAlias('campaignmonitor/checkout_observer','FactoryX_CampaignMonitor_Model_Checkout_Observer');
        // Rewrites
        $this->assertModelAlias('newsletter/subscriber','FactoryX_CampaignMonitor_Model_Subscriber');

        // Blocks
        $this->assertBlockAlias('campaignmonitor/checkoutnewsletter','FactoryX_CampaignMonitor_Block_Checkoutnewsletter');
        $this->assertBlockAlias('campaignmonitor/adminhtml_newsletter_subscriber_renderer_dob','FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_Dob');
        $this->assertBlockAlias('campaignmonitor/adminhtml_newsletter_subscriber_renderer_firstName','FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_FirstName');
        $this->assertBlockAlias('campaignmonitor/adminhtml_newsletter_subscriber_renderer_lastName','FactoryX_CampaignMonitor_Block_Adminhtml_Newsletter_Subscriber_Renderer_LastName');
        $this->assertBlockAlias('campaignmonitor/adminhtml_system_config_form_field_linkedattributes','FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Linkedattributes');
        $this->assertBlockAlias('campaignmonitor/adminhtml_system_config_form_field_auth','FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Auth');
        $this->assertBlockAlias('campaignmonitor/adminhtml_system_config_form_field_refreshtoken','FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Form_Field_Refreshtoken');
        $this->assertBlockAlias('campaignmonitor/adminhtml_system_config_source_authtype','FactoryX_CampaignMonitor_Block_Adminhtml_System_Config_Source_Authtype');

    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/campaignmonitor.xml');
        $this->assertLayoutFileExists('frontend','factoryx/campaignmonitor.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/campaignmonitor.xml','default','base');

    }
}