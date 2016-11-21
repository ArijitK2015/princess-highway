<?php

/**
 * Class FactoryX_CustomerSurvey_Model_Mysql4_Results
 */
class FactoryX_CustomerSurvey_Model_Mysql4_Results extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('customersurvey/results', 'result_id');
    }

}