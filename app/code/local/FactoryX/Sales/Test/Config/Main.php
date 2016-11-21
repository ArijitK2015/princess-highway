<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Sales_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.2.4");
        $this->assertModuleDepends('Mage_Sales');
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('fx_sales','FactoryX_Sales_Helper_Data');

        // Models Rewrites
        $this->assertModelAlias('sales/order','FactoryX_Sales_Model_Order');
        $this->assertModelAlias('sales/order_shipment','FactoryX_Sales_Model_Order_Shipment');
        $this->assertModelAlias('fx_sales/observer','FactoryX_Sames_Model_Observer');
    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/sales.xml');
        $this->assertLayoutFileExists('frontend','factoryx/sales.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/sales.xml','default','base');
    }
}