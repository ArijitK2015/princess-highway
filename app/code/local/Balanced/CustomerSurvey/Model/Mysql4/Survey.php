<?php

class Balanced_CustomerSurvey_Model_Mysql4_Survey extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('customersurvey/survey', 'customersurvey_id');
    }
}