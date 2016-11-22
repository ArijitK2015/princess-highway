<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ProductPolice_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.1.11");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('factoryx_productpolice','FactoryX_ProductPolice_Helper_Data');

        // Blocks
        $this->assertBlockAlias('factoryx_productpolice_adminhtml/button','FactoryX_ProductPolice_Block_Adminhtml_Button');
        $this->assertBlockAlias('factoryx_productpolice_adminhtml/item','FactoryX_ProductPolice_Block_Adminhtml_Item');
        $this->assertBlockAlias('factoryx_productpolice_adminhtml/item_grid','FactoryX_ProductPolice_Block_Adminhtml_Item_Grid');

        // Models
        $this->assertModelAlias('factoryx_productpolice/item','FactoryX_ProductPolice_Model_Item');
        $this->assertModelAlias('factoryx_productpolice/observer','FactoryX_ProductPolice_Model_Observer');
        $this->assertModelAlias('factoryx_productpolice/adminhtml_system_config_backend_productpolice_cron','FactoryX_ProductPolice_Model_Adminhtml_System_Config_Backend_Productpolice_Cron');

        // Resources Models
        $this->assertResourceModelAlias('factoryx_productpolice/item','FactoryX_ProductPolice_Model_Resource_Item');
        $this->assertResourceModelAlias('factoryx_productpolice/item_collection','FactoryX_ProductPolice_Model_Resource_Item_Collection');

    }
}