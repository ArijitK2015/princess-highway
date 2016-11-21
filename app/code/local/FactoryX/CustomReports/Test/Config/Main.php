<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_CustomReports_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.3.6");
    }

    public function testClassAliasDefinitions()
    {
        // Blocks
        $this->assertBlockAlias('customreports/customreport','FactoryX_CustomReports_Block_Customreport');
        $this->assertBlockAlias('customreports/worstsellersbycategory','FactoryX_CustomReports_Block_Worstsellersbycategory');
        $this->assertBlockAlias('customreports/worstsellersbycategory_grid','FactoryX_CustomReports_Block_Worstsellersbycategory_Grid');
        $this->assertBlockAlias('customreports/worstsellers','FactoryX_CustomReports_Block_Worstsellers');
        $this->assertBlockAlias('customreports/worstsellers_grid','FactoryX_CustomReports_Block_Worstsellers_Grid');
        $this->assertBlockAlias('customreports/wishlist','FactoryX_CustomReports_Block_Wishlist');
        $this->assertBlockAlias('customreports/wishlist_grid','FactoryX_CustomReports_Block_Wishlist_Grid');
        $this->assertBlockAlias('customreports/signedupnoorder','FactoryX_CustomReports_Block_Signedupnoorder');
        $this->assertBlockAlias('customreports/signedupnoorder_grid','FactoryX_CustomReports_Block_Signedupnoorder_Grid');
        $this->assertBlockAlias('customreports/shoppedonce','FactoryX_CustomReports_Block_Shoppedonce');
        $this->assertBlockAlias('customreports/shoppedonce_grid','FactoryX_CustomReports_Block_Shoppedonce_Grid');
        $this->assertBlockAlias('customreports/noupsells','FactoryX_CustomReports_Block_Noupsells');
        $this->assertBlockAlias('customreports/noupsells_grid','FactoryX_CustomReports_Block_Noupsells_Grid');
        $this->assertBlockAlias('customreports/lifetimesales','FactoryX_CustomReports_Block_Lifetimesales');
        $this->assertBlockAlias('customreports/lifetimesales_grid','FactoryX_CustomReports_Block_Lifetimesales_Grid');
        $this->assertBlockAlias('customreports/bestsellersbycategory_grid','FactoryX_CustomReports_Block_Bestsellersbycategory_Grid');
        $this->assertBlockAlias('customreports/bestsellersbycategory','FactoryX_CustomReports_Block_Bestsellersbycategory');

        // Models Rewrite
        $this->assertResourceModelAlias('reports/product_collection','FactoryX_CustomReports_Model_Reports_Resource_Product_Collection');

        // Models
        $this->assertModelAlias('customreports/reports_resource_product_collection_abstract','FactoryX_CustomReports_Model_Reports_Resource_Product_Collection_Abstract');
    }
}