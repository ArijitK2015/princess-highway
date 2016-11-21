<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ReviewCaptcha_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.1.2");
    }

    public function testClassAliasDefinitions()
    {
        // Blocks Rewrites
        $this->assertBlockAlias('reviewcaptcha/observer','FactoryX_ReviewCaptcha_Model_Observer');
    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/reviewcaptcha.xml');
        $this->assertLayoutFileExists('frontend','factoryx/reviewcaptcha.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/reviewcaptcha.xml','default','base');
    }
}