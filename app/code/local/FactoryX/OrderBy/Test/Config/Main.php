<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_OrderBy_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.2.8");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('orderby','FactoryX_OrderBy_Helper_Data');

        // Blocks
        $this->assertBlockAlias('orderby/adminhtml_info','FactoryX_OrderBy_Block_Adminhtml_Info');

        // Models
        $this->assertModelAlias('orderby/observer','FactoryX_OrderBy_Model_Observer');
    }

    public function testLayoutDefinitions()
    {
        // Backend
        $this->assertLayoutFileDefined('adminhtml','factoryx/orderby.xml');
        $this->assertLayoutFileExists('adminhtml','factoryx/orderby.xml');
        $this->assertLayoutFileExistsInTheme('adminhtml','factoryx/orderby.xml','default','default');
    }
}