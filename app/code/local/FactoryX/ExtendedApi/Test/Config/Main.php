<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ExtendedApi_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.0.11");
    }

    public function testClassAliasDefinitions()
    {
        // Models Overwrite
        $this->assertModelAlias('api/server_adapter_soap','FactoryX_ExtendedApi_Model_Api_Server_Adapter_Soap');
        $this->assertModelAlias('catalog/product_link_api','FactoryX_ExtendedApi_Model_Catalog_Product_Link_Api');
        $this->assertModelAlias('catalog/product_attribute_media_api','FactoryX_ExtendedApi_Model_Catalog_Product_Attribute_Media_Api');
    }
}