<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_CustomerLinks_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.1.4");
    }

    public function testClassAliasDefinitions()
    {
        // Blocks rewrites
        $this->assertBlockAlias('customer/account_navigation', 'FactoryX_CustomerLinks_Block_Account_Navigation');
    }
}