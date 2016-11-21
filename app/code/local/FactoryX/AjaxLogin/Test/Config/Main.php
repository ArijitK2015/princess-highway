<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_AjaxLogin_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.3.2");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('ajaxlogin','FactoryX_AjaxLogin_Helper_Data');
    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/ajaxlogin.xml');
        $this->assertLayoutFileExists('frontend','factoryx/ajaxlogin.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/ajaxlogin.xml','default','base');

    }
}