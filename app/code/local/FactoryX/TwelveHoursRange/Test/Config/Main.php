<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_TwelveHoursRange_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.1.6");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers Rewrites
        $this->assertHelperAlias('adminhtml/dashboard_data','FactoryX_TwelveHoursRange_Helper_Adminhtml_Dashboard_Data');

        // Blocks Rewrites
        $this->assertBlockAlias('adminhtml/dashboard_graph','FactoryX_TwelveHoursRange_Block_Adminhtml_Dashboard_Graph');
        $this->assertBlockAlias('adminhtml/dashboard_totals','FactoryX_TwelveHoursRange_Block_Adminhtml_Dashboard_Totals');
        $this->assertBlockAlias('adminhtml/dashboard_tab_amounts','FactoryX_TwelveHoursRange_Block_Adminhtml_Dashboard_Tab_Amounts');
        $this->assertBlockAlias('adminhtml/dashboard_tab_orders','FactoryX_TwelveHoursRange_Block_Adminhtml_Dashboard_Tab_Orders');

        // Resource Models
        $this->assertResourceModelAlias('reports/order_collection','FactoryX_TwelveHoursRange_Model_Reports_Resource_Order_Collection');
    }
}