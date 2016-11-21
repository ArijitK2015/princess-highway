<?php
/**
get store emails from
select * from core_config_data where path like 'trans_email/ident_%/email';

not all have ident_
select * from core_config_data where path like 'trans_email/%/email';
 */

class FactoryX_PickList_Model_System_Config_Source_StoreEmails extends Varien_Object {

    protected $rCon;

    /**
     * @return array
     */
    public function toOptionArray() {

        $options = array('off' => 'No Email');

        $query = "select * from core_config_data where path like 'trans_email/%/email'";
        $results = $this->_getCon()->query($query);
        
        while ($row = $results->fetch())
        {
            Mage::log(sprintf("%s->result=%s", __METHOD__, print_r($row, true)) );
            $email = $row['value'];
            $name = $this->getTransEmailName($row['path']);
            $options[] = array('value' => $email, 'label' => sprintf("%s <%s>", $name, $email));
        }

        return $options;
    }

    /**
     * get email name
     * select value from core_config_data where path = 'trans_email/ident_marketing/name'
     * @param $path
     * @return string
     */
    protected function getTransEmailName($path) {
        // try to get the name
        $path = preg_replace("/\/email/", "/name", $path);
        $query = sprintf("select value from core_config_data where path = '%s'", $path);
        Mage::log(sprintf("%s->query=%s", __METHOD__, $query) );
        $value = $this->_getCon()->fetchOne($query);
        Mage::log(sprintf("%s->result=%s", __METHOD__, print_r($value, true)) );
        $name = "Unknown";
        if ($value) {
            $name = $value;
        }
        return $name;            
    }
    
    /**
    init conn
    */
    private function _getCon() {
        if (!$this->rCon) {
            $this->rCon = Mage::getSingleton('core/resource')->getConnection('core_read');
        }
        return $this->rCon;
    }
        
}
