<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_CacheSupport_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.1.5");
        $this->assertModuleDepends("Phoenix_VarnishCache");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('factoryx_cachesupport','FactoryX_CacheSupport_Helper_Data');

        // Blocks
        $this->assertBlockAlias('factoryx_cachesupport/recentlyviewed','FactoryX_CacheSupport_Block_Recentlyviewed');
    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/cachesupport.xml');
        $this->assertLayoutFileExists('frontend','factoryx/cachesupport.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/cachesupport.xml','default','base');

    }
}