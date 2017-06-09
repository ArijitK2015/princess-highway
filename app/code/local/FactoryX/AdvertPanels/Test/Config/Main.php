<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 10/12/2014
 * Time: 16:59
 */
class FactoryX_AdvertPanels_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.2.6");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('advertpanels','FactoryX_AdvertPanels_Helper_Data');

        // Models
        $this->assertModelAlias('advertpanels/product_type_panel','FactoryX_AdvertPanels_Model_Product_Type_Panel');
    }
}