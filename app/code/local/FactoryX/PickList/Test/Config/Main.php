<?php
/**
 * Created by PhpStorm.
 * User: Raph
 * Date: 28/11/2014
 * Time: 11:10
 */

class FactoryX_PickList_Test_Config_Main extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testModuleVersion()
    {
        // Testing configuration
        $this->assertModuleCodePool('local');
        $this->assertModuleVersion("1.4.19");
    }

    public function testClassAliasDefinitions()
    {
        // Helpers
        $this->assertHelperAlias('picklist','FactoryX_PickList_Helper_Data');
        $this->assertHelperAlias('picklist/date','FactoryX_PickList_Helper_Date');
        $this->assertHelperAlias('picklist/mysql4_install','FactoryX_PickList_Helper_Mysql4_Install');

        // Blocks
        $this->assertBlockAlias('picklist/adminhtml_generate','FactoryX_PickList_Block_Adminhtml_Generate');
        $this->assertBlockAlias('picklist/adminhtml_test','FactoryX_PickList_Block_Adminhtml_Test');
        $this->assertBlockAlias('picklist/adminhtml_cron_testCreate','FactoryX_PickList_Block_Adminhtml_Cron_TestCreate');
        $this->assertBlockAlias('picklist/adminhtml_cron_testPurge','FactoryX_PickList_Block_Adminhtml_Cron_TestPurge');
        $this->assertBlockAlias('picklist/adminhtml_log_job','FactoryX_PickList_Block_Adminhtml_Log_Job');
        $this->assertBlockAlias('picklist/adminhtml_log_job_view','FactoryX_PickList_Block_Adminhtml_Log_Job_View');
        $this->assertBlockAlias('picklist/adminhtml_log_job_grid','FactoryX_PickList_Block_Adminhtml_Log_Job_Grid');
        $this->assertBlockAlias('picklist/adminhtml_log_request','FactoryX_PickList_Block_Adminhtml_Log_Request');
        $this->assertBlockAlias('picklist/adminhtml_log_request_grid','FactoryX_PickList_Block_Adminhtml_Log_Request_Grid');
        $this->assertBlockAlias('picklist/adminhtml_log_request_view','FactoryX_PickList_Block_Adminhtml_Log_Request_View');
        $this->assertBlockAlias('picklist/adminhtml_output_download','FactoryX_PickList_Block_Adminhtml_Output_Download');
        $this->assertBlockAlias('picklist/adminhtml_output_error','FactoryX_PickList_Block_Adminhtml_Output_Error');
        $this->assertBlockAlias('picklist/adminhtml_output_view','FactoryX_PickList_Block_Adminhtml_Output_View');
        $this->assertBlockAlias('picklist/adminhtml_picklist_form','FactoryX_PickList_Block_Adminhtml_Picklist_Form');
        $this->assertBlockAlias('picklist/adminhtml_renderer_json','FactoryX_PickList_Block_Adminhtml_Renderer_Json');
        $this->assertBlockAlias('picklist/adminhtml_renderer_url','FactoryX_PickList_Block_Adminhtml_Renderer_Url');
        $this->assertBlockAlias('picklist/adminhtml_system_config_form_button','FactoryX_PickList_Block_Adminhtml_System_Config_Form_Button');
        $this->assertBlockAlias('picklist/adminhtml_system_config_form_button_testCreate','FactoryX_PickList_Block_Adminhtml_System_Config_Form_Button_TestCreate');
        $this->assertBlockAlias('picklist/adminhtml_system_config_form_button_testPurge','FactoryX_PickList_Block_Adminhtml_System_Config_Form_Button_TestPurge');

        // Models
        $this->assertModelAlias('picklist/picklist','FactoryX_PickList_Model_Picklist');
        $this->assertModelAlias('picklist/observer','FactoryX_PickList_Model_Observer');
        $this->assertModelAlias('picklist/output_download','FactoryX_PickList_Model_Output_Download');
        $this->assertModelAlias('picklist/output_ftp','FactoryX_PickList_Model_Output_Ftp');
        $this->assertModelAlias('picklist/output_view','FactoryX_PickList_Model_Output_View');
        $this->assertModelAlias('picklist/pdf_picklist','FactoryX_PickList_Model_Pdf_Picklist');
        $this->assertModelAlias('picklist/picklist_log_job','FactoryX_PickList_Model_Picklist_Log_Job');
        $this->assertModelAlias('picklist/picklist_log_request','FactoryX_PickList_Model_Picklist_Log_Request');
        $this->assertModelAlias('picklist/system_config_source_option','FactoryX_PickList_Model_System_Config_Source_Option');
        $this->assertModelAlias('picklist/system_config_source_storeEmails','FactoryX_PickList_Model_System_Config_Source_StoreEmails');
        $this->assertModelAlias('picklist/system_config_source_stores','FactoryX_PickList_Model_System_Config_Source_Stores');

        // Resources Models
        $this->assertResourceModelAlias('picklist/setup','FactoryX_PickList_Model_Mysql4_Setup');
        $this->assertResourceModelAlias('picklist/picklist_log_job','FactoryX_PickList_Model_Mysql4_Picklist_Log_Job');
        $this->assertResourceModelAlias('picklist/picklist_log_job_collection','FactoryX_PickList_Model_Mysql4_Picklist_Log_Job_Collection');
        $this->assertResourceModelAlias('picklist/picklist_log_request','FactoryX_PickList_Model_Mysql4_Picklist_Log_Request');
        $this->assertResourceModelAlias('picklist/picklist_log_request_collection','FactoryX_PickList_Model_Mysql4_Picklist_Log_Request_Collection');

    }
}
