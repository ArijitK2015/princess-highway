<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ExtendedCatalog_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.2.4");
        $this->assertModuleDepends("Mage_Catalog");
    }

    public function testClassAliasDefinitions()
    {
        // Blocks rewrites
        $this->assertBlockAlias('catalog/product_list','FactoryX_ExtendedCatalog_Block_Catalog_Product_List');
        $this->assertBlockAlias('catalog/product_view_type_configurable','FactoryX_ExtendedCatalog_Block_Catalog_Product_View_Type_Configurable');
        $this->assertBlockAlias('catalog/product_list_toolbar','FactoryX_ExtendedCatalog_Block_Catalog_Product_List_Toolbar');

        // Models rewrites
        $this->assertModelAlias('catalog/config','FactoryX_ExtendedCatalog_Model_Catalog_Config');
        $this->assertResourceModelAlias('catalogsearch/fulltext','FactoryX_ExtendedCatalog_Model_CatalogSearch_Resource_Fulltext');

        // Models
        $this->assertModelAlias('extendedcatalog/observer','FactoryX_ExtendedCatalog_Model_Observer');
    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/extendedcatalog.xml');
        $this->assertLayoutFileExists('frontend','factoryx/extendedcatalog.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/extendedcatalog.xml','default','base');

    }
}