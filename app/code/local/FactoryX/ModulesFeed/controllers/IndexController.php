<?php
/**
 * Ajax controller to update the available sizes and the price
 */

class FactoryX_ModulesFeed_IndexController extends Mage_Core_Controller_Front_Action {

    private $xml = '
	<module>
		<name><![CDATA[%MOD_NAME%]]></name>
		<code_pool><![CDATA[%MOD_CODE_POOL%]]></code_pool>
		<version><![CDATA[%MOD_VERSION%]]></version>
		<data_entry><![CDATA[%MOD_DATA_ENTRY%]]></data_entry>
		<data_version><![CDATA[%MOD_DATA_VERSION%]]></data_version>
		<output_enable><![CDATA[%MOD_OUTPUT_ENABLE%]]></output_enable>
		<file_enable><![CDATA[%MOD_FILE_ENABLE%]]></file_enable>
		<config_file_exists><![CDATA[%MOD_CONFIG_FILE_EXISTS%]]></config_file_exists>
		<config_file_path><![CDATA[%MOD_CONFIG_FILE_PATH%]]></config_file_path>
		<folder_exists><![CDATA[%MOD_FOLDER_EXISTS%]]></folder_exists>
		<folder_path><![CDATA[%MOD_FOLDER_PATH%]]></folder_path>
		<composer><![CDATA[%MOD_COMPOSER%]]></composer>
		<git><![CDATA[%MOD_GIT%]]></git>
	</module>';

    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');

        header("Content-Type: text/xml; charset=UTF-8");

        $xml =  "";
        $xml .= sprintf("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
        $xml .= sprintf("<module_list created='%s' magever='%s'>", date('c'), Mage::getVersion());

        $config = Mage::getConfig();

        foreach ($config->getNode('modules')->children() as $module) {
            $node = $this->xml;

            $node = str_replace("%MOD_NAME%", $module->getName(), $node);
            $node = str_replace("%MOD_FILE_ENABLE%", (string)$module->active, $node);
            $node = str_replace("%MOD_CODE_POOL%", (string)$module->codePool, $node);

            $version = Mage::getConfig()->getModuleConfig($module->getName())->version;
            if (!$version) {
                $version = 'undefined';
            }

            $node = str_replace("%MOD_VERSION%", $version, $node);

            // Folder Exists
            $dir = $config->getOptions()->getCodeDir().DS.$module->codePool.DS.uc_words($module->getName(),DS);

            $node = str_replace("%MOD_FOLDER_PATH%", $dir, $node);

            $pathExists = file_exists($dir);
            $pathExists = $pathExists ? 'true' : 'false';
            $node = str_replace("%MOD_FOLDER_EXISTS%", $pathExists, $node);

            // Config File Exists
            $file = $config->getModuleDir('etc', $module->getName()).DS."config.xml";

            $node = str_replace("%MOD_CONFIG_FILE_PATH%", $file, $node);

            $exists = file_exists($file);

            if ($exists)
            {
                // Get the config.xml file
                $configXml = simplexml_load_string(file_get_contents($file),'Varien_Simplexml_Element');
                // Get the resources tag
                if ($nodes = $configXml->global->resources)
                {
                    // Reset the pointer to the beginning of the array
                    reset($nodes);
                    // Get the resource name (first key in the array)
                    $resourceName = key($nodes);
                }
                else
                {
                    $resourceName = '';
                }
                // Assign it to our XML
                $node = str_replace("%MOD_DATA_ENTRY%", $resourceName, $node);

                if (!$resourceName) $dataVersion = '';
                else
                {
                    // Get the data version based on the resource name
                    $dataVersion = Mage::getResourceSingleton('core/resource')->getDataVersion($resourceName);
                }

                $node = str_replace("%MOD_DATA_VERSION%", $dataVersion, $node);

                // Please note that the 0 value means Enabled and 1 means Disabled
                $disableOutput = Mage::getStoreConfig('advanced/modules_disable_output/'.$module->getName()) ? 'false' : 'true';

                $node = str_replace("%MOD_OUTPUT_ENABLE%", $disableOutput, $node);
            }
            else
            {
                $node = str_replace("%MOD_DATA_ENTRY%", '', $node);
                $node = str_replace("%MOD_DATA_VERSION%", '', $node);
                $node = str_replace("%MOD_OUTPUT_ENABLE%", '', $node);
            }

            $exists = $exists ? 'true' : 'false';
            $node = str_replace("%MOD_CONFIG_FILE_EXISTS%", $exists, $node);

            // Composer Check
            $composerFile = Mage::getBaseDir() . "/composer.lock";
            $installedModule = "";
            // Read file
            $file_handle = fopen($composerFile, "r");
            while (!feof($file_handle))
            {
                $installedModule .= fgets($file_handle);
            }
            fclose($file_handle);

            // Decode JSON
            $arrayComposer = Mage::helper('core')->jsonDecode($installedModule);

            $composerFound = false;

            // Loop through the installed modules
            foreach ($arrayComposer['packages'] as $moduleInstalled)
            {
                // Break if installed via composer is found
                if ($composerFound) break;

                // Name of the composer package of the module
                $moduleName = $moduleInstalled['name'];
                // Remove the prefix
                $moduleName = str_replace('factoryx-developers/','',$moduleName);
                // Replace possible dash with underscore
                $moduleName = str_replace('-','_',$moduleName);
                // Check if lowercase Magento module equals composer package
                if (strtolower($module->getName()) == $moduleName)
                {
                    $node = str_replace("%MOD_COMPOSER%", 'true', $node);
                    $node = str_replace("%MOD_GIT%", $moduleInstalled['source']['url'], $node);
                    $composerFound = true;
                }
            }

            // If not installed via composer set to false
            if (!$composerFound)
            {
                $node = str_replace("%MOD_COMPOSER%", 'false', $node);
                $node = str_replace("%MOD_GIT%", '', $node);
            }

            $xml .= sprintf("%s", $node);
        }

        $xml .= sprintf("</module_list>");

        $this->getResponse()->setBody($xml);
    }
}