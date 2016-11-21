<?php
require_once 'Mage/Contacts/controllers/IndexController.php';

/**
 * Dynamic recipient email based on the enquiry
 */

/**
 * Contacts index controller
 *
 * @category   Mage
 * @package    Mage_Contacts
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FactoryX_AskStylist_IndexController extends Mage_Contacts_IndexController
{
    public function postAction()
    {
        // Skip if the module is not enabled
        if (!Mage::helper('askstylist')->isEnabled()) {
            return parent::postAction();
        }

        $post = $this->getRequest()->getPost();
        if ( $post ) {
            $translate = Mage::getSingleton('core/translate');
            /* @var $translate Mage_Core_Model_Translate */
            $translate->setTranslateInline(false);
            try {
                $postObject = new Varien_Object();
                $postObject->setData($post);

                $error = false;

                if (!Zend_Validate::is(trim($post['name']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['comment']) , 'NotEmpty')) {
                    $error = true;
                }

                if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
                    $error = true;
                }

                if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
                    $error = true;
                }

                if ($error) {
                    throw new Exception();
                }

                // Dynamically change the recipient based on the enquiry
                $recipient = Mage::helper('askstylist')->getCorrespondingRecipient($post['enquiry']);
                //Mage::log(sprintf("%s->recipient=%s", __METHOD__, $recipient) );
                
                $senderName = Mage::getStoreConfig('trans_email/ident_general/name');
	            $senderEmail = Mage::getStoreConfig('trans_email/ident_general/email');	
                
                // Make the poster the sender
                $sender = array (
                    'name'	=>	$post['name'],
                    'email'	=>	$senderEmail
                    //'email' =>	'general', // <!-- this does not work
                    //'email' =>	Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER)
                );
                // alternatively
                //$sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER);
   
                //Mage::log(sprintf("%s->sender=%s", __METHOD__, print_r($sender, true)) );

                $mailTemplate = Mage::getModel('core/email_template');
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $sentSuccess = $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        $sender,
                        $recepientEmail = Mage::getStoreConfig($recipient),
                        $recepientName = null,
                        array('data' => $postObject)
                    );

                //Mage::log(sprintf("mailTemplate: %s", print_r($mailTemplate->getData(), true)));
                //Mage::log(sprintf("sending to %s...", Mage::getStoreConfig($recipient)));
                
                // disable this check as sent_success is always empty
                //if (!$mailTemplate->getSentSuccess()) {
                if (!$sentSuccess) {
                    throw new Exception("not successful!");
                }

                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                //Mage::log(sprintf("Error: %s", $e->getMessage()));
                $translate->setTranslateInline(true);
                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Sorry we are unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        }
        else {
            $this->_redirect('*/*/');
        }
    }

}
