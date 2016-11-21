<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_HtmlInject_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.1.14");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('htmlinject','FactoryX_HtmlInject_Helper_Data');
    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/htmlinject.xml');
        $this->assertLayoutFileExists('frontend','factoryx/htmlinject.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/htmlinject.xml','default','base');

    }
}