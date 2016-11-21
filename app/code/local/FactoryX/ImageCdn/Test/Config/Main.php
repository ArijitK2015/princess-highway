<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ImageCdn_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.2.4");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('imagecdn','FactoryX_ImageCdn_Helper_Data');
        // Helpers Rewrites
        $this->assertHelperAlias('catalog/image','FactoryX_ImageCdn_Helper_Image');

        // Blocks
        $this->assertBlockAlias('factoryx_imagecdn_adminhtml/cachedb','FactoryX_ImageCdn_Block_Adminhtml_Cachedb');
        $this->assertBlockAlias('factoryx_imagecdn_adminhtml/cachedb_grid','FactoryX_ImageCdn_Block_Adminhtml_Cachedb_Grid');
        $this->assertBlockAlias('factoryx_imagecdn_adminhtml/template_grid_renderer_action','FactoryX_ImageCdn_Block_Adminhtml_Template_Grid_Renderer_Action');

        // Models Rewrites
        $this->assertModelAlias('catalog/product_image','FactoryX_ImageCdn_Model_Catalog_Product_Image');
        $this->assertModelAlias('catalog/category','FactoryX_ImageCdn_Model_Catalog_Category');

        // Models
        $this->assertModelAlias('imagecdn/cache','FactoryX_ImageCdn_Model_Cache');
        $this->assertModelAlias('imagecdn/cachedb','FactoryX_ImageCdn_Model_Cachedb');
        $this->assertModelAlias('imagecdn/observer','FactoryX_ImageCdn_Model_Observer');
        $this->assertModelAlias('imagecdn/varien_ftp','FactoryX_ImageCdn_Model_Varien_Ftp');
        $this->assertModelAlias('imagecdn/varien_gd2','FactoryX_ImageCdn_Model_Varien_Gd2');
        $this->assertModelAlias('imagecdn/varien_image','FactoryX_ImageCdn_Model_Varien_Image');
        $this->assertModelAlias('imagecdn/source_cachemethods','FactoryX_ImageCdn_Model_Source_Cachemethods');
        $this->assertModelAlias('imagecdn/source_cdstypes','FactoryX_ImageCdn_Model_Source_Cdstypes');
        $this->assertModelAlias('imagecdn/source_compression','FactoryX_ImageCdn_Model_Source_Compression');
        $this->assertModelAlias('imagecdn/adminhtml_system_config_backend_imagecdn_cron','FactoryX_ImageCdn_Model_Adminhtml_System_Config_Backend_Imagecdn_Cron');
        $this->assertModelAlias('imagecdn/adapter_abstract','FactoryX_ImageCdn_Model_Adapter_Abstract');
        $this->assertModelAlias('imagecdn/adapter_amazons3','FactoryX_ImageCdn_Model_Adapter_Amazons3');
        $this->assertModelAlias('imagecdn/adapter_amazons3_wrapper','FactoryX_ImageCdn_Model_Adapter_Amazons3_Wrapper');
        $this->assertModelAlias('imagecdn/adapter_coralcdn','FactoryX_ImageCdn_Model_Adapter_Coralcdn');
        $this->assertModelAlias('imagecdn/adapter_disabled','FactoryX_ImageCdn_Model_Adapter_Disabled');
        $this->assertModelAlias('imagecdn/adapter_ftp','FactoryX_ImageCdn_Model_Adapter_Ftp');
        $this->assertModelAlias('imagecdn/adapter_ftps','FactoryX_ImageCdn_Model_Adapter_Ftps');
        $this->assertModelAlias('imagecdn/adapter_highwinds','FactoryX_ImageCdn_Model_Adapter_Highwinds');
        $this->assertModelAlias('imagecdn/adapter_highwinds_api','FactoryX_ImageCdn_Model_Adapter_Highwinds_Api');
        $this->assertModelAlias('imagecdn/adapter_rackspace','FactoryX_ImageCdn_Model_Adapter_Rackspace');
        $this->assertModelAlias('imagecdn/adapter_sftp','FactoryX_ImageCdn_Model_Adapter_Sftp');

        // Resources Models
        $this->assertResourceModelAlias('imagecdn/cachedb','FactoryX_ImageCdn_Model_Mysql4_Cachedb');
        $this->assertResourceModelAlias('imagecdn/cachedb_collection','FactoryX_ImageCdn_Model_Mysql4_Cachedb_Collection');

    }

    public function testLayoutDefinitions()
    {
        // Frontend
        $this->assertLayoutFileDefined('frontend','factoryx/imagecdn.xml');
        $this->assertLayoutFileExists('frontend','factoryx/imagecdn.xml');
        $this->assertLayoutFileExistsInTheme('frontend','factoryx/imagecdn.xml','default','base');

    }
}