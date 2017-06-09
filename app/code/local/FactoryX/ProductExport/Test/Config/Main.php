<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ProductExport_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.3.2");
        $this->assertModuleDepends('Mage_Adminhtml');
    }

    public function testClassAliasDefinitions()
    {
        // Models
        $this->assertModelAlias('productexport/observer','FactoryX_ProductExport_Model_Observer');
    }
}