<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Quickshop_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.0.7");
    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/quickshop.xml');
        $this->assertLayoutFileExists('frontend','factoryx/quickshop.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/quickshop.xml','default','base');
    }
}