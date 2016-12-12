<?php
/**
 * used for conditional agreements
 *
 * Checkout default helper
 */
class FactoryX_ConditionalAgreement_Helper_Data extends Mage_Checkout_Helper_Data {

    // used for getRequiredAgreements
    protected $_agreementsObjects = null;

    protected $logFileName = 'factoryx_conditionalagreement.log';

    /**
     * @return int agreement id
     */
    public function getConditionalAgreementAgreement()
    {
        return Mage::getStoreConfig('conditionalagreement/options/agreement');
    }

    /**
     * @return string condition
     */
    public function getConditionalAgreementCondition()
    {
        return Mage::getStoreConfig('conditionalagreement/options/condition');
    }

    /**
     * @return int 1 = enabled, 0 = disabled
     */
    public function getConditionalAgreementEnabled()
    {
        return Mage::getStoreConfig('conditionalagreement/options/enabled');
    }

    /**
     * Log data
     * @param string|object|array data to log
     */
    public function log($data)
    {
        Mage::log($data, null, $this->logFileName);
    }

    /**
     * uses getAgreements rather than get all agreements
     * @return array|null
     */
    public function getRequiredAgreements()
    {
        if (is_null($this->_agreementsObjects)) {
            if (!Mage::getStoreConfigFlag('checkout/options/enable_agreements') || !$this->getConditionalAgreementEnabled()) {
                return false;
            }
            else {
                return $this->_getAgreements();
            }
        }
    }

    /**
     * @return array agreements
     */
    protected function _getAgreements()
    {
        try {
            // Get picked agreement and condition
            $caAgreementId = $this->getConditionalAgreementAgreement();
            $caCondition = $this->getConditionalAgreementCondition();

            $collection = Mage::getResourceModel('salesrule/rule_collection')
                ->addFieldToSelect('conditions_serialized')
                ->addFieldToFilter('rule_id',$caCondition)
                ->setPageSize(1);

            if (!$collection->getSize()) {
                return Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1);
            }

            /** @var Mage_SalesRule_Model_Rule $rule */
            $rule = $collection->getFirstItem();

            // Validate the condition the hardway as I cant find quote->isValidCoupon(code)
            $showAgreement = false;

            // Search order for skus
            $allItems = Mage::getSingleton('checkout/session')
                ->getQuote()
                ->getAllItems();
            
            foreach ($allItems as $item) {;
                if ($rule->validate($item)) {
                    $showAgreement = true;
                    break;
                }
            }

            // Agreements without the conditional one
            if (!$showAgreement) {
                return Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1)
                    ->addFieldToFilter('agreement_id', array('neq'  => $caAgreementId));
            } else {
                return Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1);
            }
        }
        catch(Exception $ex) {
            // uses all active agreements
            $this->log(sprintf("%s->error: %s", __METHOD__, $ex->getMessage()) );
        }
    }

}
