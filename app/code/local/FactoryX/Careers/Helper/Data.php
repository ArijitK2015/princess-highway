<?php

class FactoryX_Careers_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $logFileName = 'factoryx_careers.log';
    
	/**
	 * Log data
	 * @param string|object|array data to log
	 */
	public function log($data) {
		Mage::log($data, null, $this->logFileName);
	}
	
    protected $states = array(
        'ACT', 'NSW', 'VIC', 'QLD', 'SA', 'WA', 'TAS', 'NT', 'NZ', 'HQ'
    );
    protected $work_types = array(
        'Part Time', 'Full Time', 'Casual', 'Contract'
    );

    public function getStates()
    {
        return $this->states;
    }

    public function getWorkTypes()
    {
        return $this->work_types;
    }

    public function isColumnEnum($conn, $table,$column)
    {
        $sql = "SHOW COLUMNS FROM {$table} WHERE FIELD='$column'";
        if ($result = $conn->fetchROW($sql)) { // If the query's successful
            if (strpos($result['Type'], 'enum') !== false) {
                return true;
            }
        } else {
            Mage::getSingleton('core/session')->addError('Unable to fetch enum values: ' . $result);
        }
        return false;
    }
}
