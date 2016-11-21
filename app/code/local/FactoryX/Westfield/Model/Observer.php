<?php
/**
*/
class FactoryX_Westfield_Model_Observer {

    /**
     *
     * @param   Varien_Event_Observer $observer
     * @return  FactoryX_Westfield_Model_Observer
     */
    public function saveReferrer($observer) {
        
        // ignore admin requests
        if (Mage::helper('westfield')->isAdmin()) {
            return $this;
        }
        
        //Mage::helper('westfield')->log(sprintf("%s->%s", __METHOD__, get_class($observer)) );
        
        $enabled = (bool)Mage::helper('westfield')->isEnabled();
        
        $session = Mage::getSingleton("core/session",  array("name"=>"frontend"));        
        $from = $session->getData("from_westfield");
        //Mage::helper('westfield')->log(sprintf("%s->from_westfield[%s]=%d", __METHOD__, $session->getEncryptedSessionId(), $from) );

        if ($enabled && empty($from) ) {

            // METHOD 1
            $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
            //Mage::helper('westfield')->log(sprintf("%s->url=%s", __METHOD__, $url) );
            if ($domain = Mage::helper('westfield')->getTrackDomain()) {
                //Mage::helper('westfield')->log(sprintf("%s->track=%s", __METHOD__, $domain) );
                if (preg_match(sprintf("/%s/i", $domain), $url)) {
                    $session->setData("from_westfield", true);
                }
            }

            // METHOD 2
            $qs = Mage::helper('westfield')->getQueryString();
            //Mage::helper('westfield')->log(sprintf("%s->qs=%s", __METHOD__, print_r($qs, true)) );
            
            //$arrParams = Mage::app()->getRequest()->getParams();
            //Mage::helper('westfield')->log(sprintf("this->getRequest()->getParams()=%s", print_r($arrParams, true)));
            
            // matches the query string e.g. source=westfield
            if (preg_match(sprintf("/%s/i", $qs[1]), Mage::app()->getRequest()->getParam($qs[0])) ) {
                $session->setData("from_westfield", true);
            }
        }
        return $this;
    }

}