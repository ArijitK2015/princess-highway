<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Merchandising_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.0.13");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('merchandising','FactoryX_Merchandising_Helper_Data');
    }

    public function testLayoutDefinitions()
    {
        // Backend
        $this->assertLayoutFileDefined('adminhtml','factoryx/merchandising.xml');
        $this->assertLayoutFileExists('adminhtml','factoryx/merchandising.xml');
        $this->assertLayoutFileExistsInTheme('adminhtml','factoryx/merchandising.xml','default','default');

    }
}