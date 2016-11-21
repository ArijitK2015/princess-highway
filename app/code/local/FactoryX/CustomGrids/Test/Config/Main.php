<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_CustomGrids_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.4.13");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('customgrids','FactoryX_CustomGrids_Helper_Data');

        // Models
        $this->assertModelAlias('customgrids/observer', 'FactoryX_CustomGrids_Model_Observer');

        // Blocks
        $this->assertBlockAlias('customgrids/adminhtml_catalog_category_tab_renderer_thumbnail','FactoryX_CustomGrids_Block_Adminhtml_Catalog_Category_Tab_Renderer_Thumbnail');
        $this->assertBlockAlias('customgrids/adminhtml_catalog_product_edit_tab_super_config_grid','FactoryX_CustomGrids_Block_Adminhtml_Catalog_Product_Edit_Tab_Super_Config_Grid');
        $this->assertBlockAlias('customgrids/adminhtml_report_product_sold_renderer_colour','FactoryX_CustomGrids_Block_Adminhtml_Report_Product_Sold_Renderer_Colour');
        $this->assertBlockAlias('customgrids/adminhtml_report_product_sold_renderer_size','FactoryX_CustomGrids_Block_Adminhtml_Report_Product_Sold_Renderer_Size');
        $this->assertBlockAlias('customgrids/adminhtml_sales_order_grid_renderer_customerGroup','FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid_Renderer_CustomerGroup');
        $this->assertBlockAlias('customgrids/adminhtml_sales_order_grid_renderer_shippingAddress','FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid_Renderer_ShippingAddress');
        $this->assertBlockAlias('customgrids/adminhtml_sales_order_grid_renderer_storeGroup','FactoryX_CustomGrids_Block_Adminhtml_Sales_Order_Grid_Renderer_StoreGroup');
    }
}