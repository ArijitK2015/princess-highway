<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_ShippedFrom_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.3.7");
        $this->assertModuleDepends('Mage_Sales');
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('shippedfrom','FactoryX_ShippedFrom_Helper_Data');
        $this->assertHelperAlias('shippedfrom/clickandsend','FactoryX_ShippedFrom_Helper_Clickandsend');
        $this->assertHelperAlias('shippedfrom/report','FactoryX_ShippedFrom_Helper_Report');

        // Blocks
        $this->assertBlockAlias('shippedfrom/adminhtml_system_config_form_button','FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button');
        $this->assertBlockAlias('shippedfrom/adminhtml_system_config_form_button_testReport','FactoryX_ShippedFrom_Block_Adminhtml_System_Config_Form_Button_TestReport');

        // Blocks Rewrites
        $this->assertBlockAlias('adminhtml/sales_order_shipment_view','FactoryX_ShippedFrom_Block_Adminhtml_Sales_Order_Shipment_View');

        // Models
        $this->assertModelAlias('shippedfrom/observer','FactoryX_ShippedFrom_Model_Observer');
        $this->assertModelAlias('shippedfrom/system_config_source_articleTypes','FactoryX_ShippedFrom_Model_System_Config_Source_ArticleTypes');
        $this->assertModelAlias('shippedfrom/system_config_source_daysOfTheWeek','FactoryX_ShippedFrom_Model_System_Config_Source_DaysOfTheWeek');
        $this->assertModelAlias('shippedfrom/system_config_source_serviceCodes','FactoryX_ShippedFrom_Model_System_Config_Source_ServiceCodes');
        $this->assertModelAlias('shippedfrom/system_config_source_stores','FactoryX_ShippedFrom_Model_System_Config_Source_Stores');
        $this->assertModelAlias('shippedfrom/system_config_source_users','FactoryX_ShippedFrom_Model_System_Config_Source_Users');
        $this->assertModelAlias('shippedfrom/shipping_carrier_clickandsend','FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend');
        $this->assertModelAlias('shippedfrom/shipping_carrier_clickandsend_export_csv','FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Csv');
        $this->assertModelAlias('shippedfrom/shipping_carrier_clickandsend_export_exception','FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Export_Exception');
        $this->assertModelAlias('shippedfrom/shipping_carrier_clickandsend_source_categories_item','FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Source_Categories_Item');
        $this->assertModelAlias('shippedfrom/shipping_carrier_clickandsend_source_instructions_nondelivery','FactoryX_ShippedFrom_Model_Shipping_Carrier_Clickandsend_Source_Instructions_Nondelivery');
        $this->assertModelAlias('shippedfrom/shipping_carrier_common_export_csv_abstract','FactoryX_ShippedFrom_Model_Shipping_Carrier_Common_Export_Csv_Abstract');
    }

    public function testLayoutDefinitions()
    {
        // Backend
        $this->assertLayoutFileDefined('adminhtml','factoryx/shippedfrom.xml');
        $this->assertLayoutFileExists('adminhtml','factoryx/shippedfrom.xml');
        $this->assertLayoutFileExistsInTheme('adminhtml','factoryx/shippedfrom.xml','default','default');
    }
}