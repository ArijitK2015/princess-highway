<?php
class FactoryX_ExtendedApi_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $logFileName = 'factoryx_extendedapi.log';
	
	public function setIndex($state, $codes = null) {
	    //$this->log(sprintf("%s->indexer: %d", __METHOD__, print_r($codes, true)) );
        $indexer = Mage::getSingleton('index/indexer')->getProcessesCollection(); 
        if (!empty($codes)) {
            // handle comma delimited string
            if (!is_array($codes)) {
                $codes = explode(',', $codes);
                $codes = array_map('trim', $codes);                
            }
            $indexer = Mage::getSingleton('index/indexer')->getProcessesCollectionByCodes($codes);
        }
        foreach ($indexer as $process) {
            if ($state == 'off') {
                $this->log(sprintf("%s->turn index '%s' off", __METHOD__, $process->getIndexerCode()));
                $process->setMode(Mage_Index_Model_Process::MODE_MANUAL)->save();
            }
            if ($state == 'on') {
                $this->log(sprintf("%s->turn index '%s' on", __METHOD__, $process->getIndexerCode()));
                $process->setMode(Mage_Index_Model_Process::MODE_REAL_TIME)->save();
            }
        }    	
	}
	
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) 
	{
		Mage::log($data, null, $this->logFileName);
	}
}

