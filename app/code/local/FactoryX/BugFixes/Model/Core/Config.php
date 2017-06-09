<?php
/**
 * Fix the bug in SUPEE 7405 and 1.9.2.3
 * http://magento.stackexchange.com/questions/98786/security-patch-supee-7405-issue-with-api-v2-soap-wsdl/98790#98790
 */
class FactoryX_BugFixes_Model_Core_Config extends Mage_Core_Model_Config
{
    public function loadModulesConfiguration($fileName, $mergeToObject = null, $mergeModel=null)
    {
        if (version_compare(Mage::getVersion(),"1.9.2.3","<"))
        {
            return parent::loadModulesConfiguration($fileName,$mergeToObject,$mergeModel);
        }
        else
        {
            $disableLocalModules = !$this->_canUseLocalModules();

            if ($mergeToObject === null) {
                $mergeToObject = clone $this->_prototype;
                $mergeToObject->loadString('<config/>');
            }
            if ($mergeModel === null) {
                $mergeModel = clone $this->_prototype;
            }
            $modules = $this->getNode('modules')->children();
            foreach ($modules as $modName=>$module) {
                if ($module->is('active')) {
                    if ($disableLocalModules && ('local' === (string)$module->codePool)) {
                        continue;
                    }
                    if (!is_array($fileName)) {
                        $fileName = array($fileName);
                    }

                    foreach ($fileName as $configFile) {
                        $configFile = $this->getModuleDir('etc', $modName).DS.$configFile;
                        if ($mergeModel->loadFile($configFile)) {

                            if ($mergeModel instanceof Mage_Core_Model_Config_Base)
                            {
                                $this->_makeEventsLowerCase(Mage_Core_Model_App_Area::AREA_GLOBAL, $mergeModel);
                                $this->_makeEventsLowerCase(Mage_Core_Model_App_Area::AREA_FRONTEND, $mergeModel);
                                $this->_makeEventsLowerCase(Mage_Core_Model_App_Area::AREA_ADMIN, $mergeModel);
                                $this->_makeEventsLowerCase(Mage_Core_Model_App_Area::AREA_ADMINHTML, $mergeModel);
                            }

                            $mergeToObject->extend($mergeModel, true);
                        }
                    }
                }
            }
            return $mergeToObject;
        }
    }
}