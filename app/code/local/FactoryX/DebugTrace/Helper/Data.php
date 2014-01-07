<?php

class FactoryX_DebugTrace_Helper_Data extends Mage_Core_Helper_Abstract {

	protected $logFileName = 'factoryx_debugtrace.log';
	
	private function get_callstack($delim="\n") 
	{
		$dt = debug_backtrace();
		$cs = '';
		foreach ($dt as $t) {
		$cs .= $t['file'] . ' line ' . $t['line'] . ' calls ' . $t['function'] . "()" . $delim;
		}

		return $cs;
    }
	
	public function toLog()
	{
		$this->log($this->get_callstack());
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