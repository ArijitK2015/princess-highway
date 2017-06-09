<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_Promo_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.0.6");
    }

    public function testClassAliasDefinitions()
    {
        // Blocks Rewrites
        $this->assertBlockAlias('adminhtml/promo_catalog_grid','FactoryX_Promo_Block_Adminhtml_Catalog_Grid');
        $this->assertBlockAlias('adminhtml/promo_quote_grid','FactoryX_Promo_Block_Adminhtml_Quote_Grid');
    }
}