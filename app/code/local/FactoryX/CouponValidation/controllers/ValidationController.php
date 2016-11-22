<?php

/**
 * Class FactoryX_CouponValidation_ValidationController
 */
class FactoryX_CouponValidation_ValidationController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        if (Mage::helper('couponvalidation')->hashValidationEnabled()) {
            //Mage::helper('couponvalidation')->log(sprintf("%s->requestParans: %s", __METHOD__, print_r($this->getRequest()->getParams(), true)));            
            if ($data = $this->getRequest()->getParams()) {
                if (array_key_exists("hash",$data)) {
                    $todayHash = md5(date("Y-m-d"));
                    if ($todayHash == $data['hash']) {
                        $redirect = false;
                    }
                    else {
                        $redirect = true;
                    }
                }
                else {
                    $redirect = true;
                }
            }
            else {
                $redirect = true;
            }
        }

        // only test if first check passes
        if (!$redirect) {
            $remoteAddr = Mage::helper('couponvalidation')->getRemoteAddr();
            //Mage::helper('couponvalidation')->log(sprintf("%s->remoteAddr[%s]", __METHOD__, $remoteAddr), Zend_Log::NOTICE);
            if (Mage::helper('couponvalidation')->isAllowed($remoteAddr)) {
                $redirect = false;
            }
            else {
                $redirect = true;
            }
        }
        
        if ($redirect) {
            //Mage::helper('couponvalidation')->log(sprintf("%s->redirect: ip[%s]", __METHOD__, $remoteAddr), Zend_Log::NOTICE);
            $this->_redirect("/");
        }

        if (!Mage::getSingleton('core/session')->getWelcomeMessage()) {
            $collection = Mage::getResourceModel('ustorelocator/location_collection')
                ->addFieldToSelect(['store_code','ip_address'])
                ->addFieldToFilter('ip_address', array('like' => sprintf("%%%s%%", $remoteAddr)) );
            $msg = Mage::helper('couponvalidation')->__('Welcome');
            if ($collection->getSize()) {
                $store = $collection->getFirstItem();
                Mage::register('current_storelocation', $store);
                $msg = Mage::helper('couponvalidation')->__('Welcome %s', $store->getStoreCode());
            }
            Mage::getSingleton('core/session')->addSuccess($msg);
            Mage::getSingleton('core/session')->setWelcomeMessage($msg);
        }
        return;
    }

    public function indexAction()
    {
        $this->loadLayout();

        $this->getLayout()->getBlock('head')->setTitle('Coupon Validation');

        $this->renderLayout();
    }

    public function validateAction()
    {
        try {
            if ($data = $this->getRequest()->getParams()) {
                $code = filter_var($code, FILTER_SANITIZE_SPECIAL_CHARS);
                $code = trim($data['code']);
                Mage::getSingleton('core/session')->setCurrentCode($code);
                    
                // Get possible associated newsletter subscriber
                $collection = Mage::getResourceModel('newsletter/subscriber_collection')
                    ->addFieldToFilter('subscriber_coupon', $code);

                // Load the coupon to get the rule_id
                $coupon = Mage::getResourceModel('salesrule/coupon_collection')
                    ->addFieldToSelect('rule_id')
                    ->addFieldToFilter('code', $code)
                    ->setPageSize(1);

                // Load the rule to get the description
                $rule = Mage::getResourceModel('salesrule/rule_collection')
                    ->addFieldToSelect('description')
                    ->addFieldToFilter('rule_id', $coupon->getFirstItem()->getRuleId())
                    ->setPageSize(1);

                // Look for an email address in the description
                $ruleDesc = $rule->getFirstItem()->getDescription();
                $matches = [];
                if (preg_match('/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i',$ruleDesc,$matches)) {
                    $email = $matches[0];
                }
                else {
                    $email = false;
                }

                // Coupon validation
                if (array_key_exists('code', $data) && Mage::getModel('couponvalidation/validator')->validate(array('code' => $code))) {

                    Mage::getSingleton('core/session')->addSuccess(Mage::helper('couponvalidation')->__('This coupon is valid'));
                    Mage::getSingleton('core/session')->setValidCode($data['code']);

                    $params = [];
                    $params['hash'] = $data['hash'];

                    // Add the subscriber ID
                    if ($collection->getSize()) {
                        $subscriber = $collection->getFirstItem()->getSubscriberId();
                        $params['subscriber'] = $subscriber;
                    }

                    // Add the customer email
                    if ($email) {
                        $params['customer'] = $email;
                    }

                    $this->_redirect('*/*/redeem',$params);
                }
            }
        } catch (Exception $e) {

            Mage::getSingleton('core/session')->addError($e->getMessage());
            $params = [];
            $params['hash'] = $data['hash'];

            // Add the subscriber ID
            if ($collection->getSize()) {
                $subscriber = $collection->getFirstItem()->getSubscriberId();
                $params['subscriber'] = $subscriber;
            }

            // Add the customer email
            if ($email) {
                $params['customer'] = $email;
            }

            $this->_redirect('coupon/validation/index',$params);
        }
    }

    public function redeemAction()
    {
        $this->loadLayout();

        $this->getLayout()->getBlock('head')->setTitle('Redeem Coupon');

        $this->renderLayout();
    }

    public function redeemPostAction()
    {
        if ($data = $this->getRequest()->getParams()) {
            try {
                $coupon = Mage::getModel('salesrule/coupon')->load($data['code'], 'code');

                $rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
                //Mage::helper('couponvalidation')->log(sprintf("%s->rule[%s|%s]: %s", __METHOD__, $data['code'], $coupon->getRuleId(), get_class($rule)) );

                // If autogenerated coupon, delete
                if ($rule->getUseAutoGeneration()) {
                    $coupon->delete();
                    $comment = Mage::helper('couponvalidation')->__('Autogenerated coupon %s has been deleted',$data['code']);
                    Mage::getSingleton('core/session')->addSuccess($comment);
                }
                elseif($coupon->getRuleId()) {
                    // If normal coupon, disable the rule
                    $rule->setIsActive(0);
                    $rule->save();
                    $comment = Mage::helper('couponvalidation')->__('Rule of coupon %s is now inactive',$data['code']);
                    Mage::getSingleton('core/session')->addSuccess($comment);
                }
                else {
                    $comment = Mage::helper('couponvalidation')->__('Coupon "%s" does not exist!', $data['code']);
                    Mage::getSingleton('core/session')->addError($comment);
                }

                Mage::dispatchEvent('factoryx_couponvalidation_redeem',['rule' => $rule, 'coupon' => $data['code'], 'comment' => $comment, 'store' => Mage::registry('current_storelocation')]);

                Mage::getSingleton('core/session')->unsValidCode();
                $this->_redirect('coupon/validation/index',['hash' => $data['hash']]);

            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
                $this->_redirect('coupon/validation/redeem',['hash' => $data['hash']]);
            }
        }
    }
}