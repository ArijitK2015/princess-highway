<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_BugFixes_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("0.1.21");
    }

    public function testClassAliasDefinitions()
    {
        // Models
        $this->assertModelAlias('catalog/category_attribute_backend_image','FactoryX_BugFixes_Model_Catalog_Category_Attribute_Backend_Image');
        $this->assertModelAlias('importexport/export_entity_abstract','FactoryX_BugFixes_Model_ImportExport_Export_Entity_Abstract');
        $this->assertModelAlias('importexport/export_entity_product','FactoryX_BugFixes_Model_ImportExport_Export_Entity_Product');

        // Resource Models
        $this->assertResourceModelAlias('catalog/product_status','FactoryX_BugFixes_Model_Catalog_Resource_Product_Status');
        $this->assertResourceModelAlias('catalog/product_type_configurable','FactoryX_BugFixes_Model_Catalog_Resource_Product_Type_Configurable');
        $this->assertResourceModelAlias('catalog/product_collection','FactoryX_BugFixes_Model_Catalog_Resource_Product_Collection');

        // Blocks
        $this->assertBlockAlias('adminhtml/widget_grid','FactoryX_BugFixes_Block_Adminhtml_Widget_Grid');
    }
}