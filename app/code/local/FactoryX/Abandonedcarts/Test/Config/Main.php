<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 10/12/2014
 * Time: 16:59
 */
class FactoryX_Abandonedcarts_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.2.5");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('abandonedcarts','FactoryX_Abandonedcarts_Helper_Data');

        // Models
        $this->assertModelAlias('abandonedcarts/notifier','FactoryX_Abandonedcarts_Model_Notifier');

        // Resource Models
        $this->assertResourceModelAlias('sales/quote','FactoryX_Abandonedcarts_Model_Sales_Resource_Quote');

        // Blocks
        $this->assertBlockAlias('abandonedcarts/adminhtml_system_config_form_button','FactoryX_Abandonedcarts_Block_Adminhtml_System_Config_Form_Button');
    }
}