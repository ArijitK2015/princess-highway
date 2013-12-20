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

	const XML_PATH_EMAIL_SENDER 	= 'trans_email/ident_general/email';		 
    const XML_PATH_EMAIL_TEMPLATE   = 'contacts/email/email_template';
    const XML_PATH_ENABLED          = 'contacts/contacts/enabled';

    /*
    General Contact     trans_email_ident_general_email
    Online Store        trans_email_ident_sales_email
    Retail              trans_email_ident_support_email
    Recruitment         trans_email_ident_custom1_email   
    PR and Media        trans_email_ident_custom2_email
    Marketing           trans_email_ident_marketing_email
    Feedback Email      trans_email_feedbackemail_email
    Admin Email         trans_email_adminemail_email
    */
    protected $directEmailTo = array(
        'what-to-wear'          => 'trans_email/ident_general/email',
        "product-availability"  => 'trans_email/ident_general/email',
        "press"                 => 'trans_email/ident_custom2/email',
        "faulty"                => 'trans_email/ident_general/email',
        "feedback"              => 'trans_email/feedbackemail/email',
        "other"                 => 'trans_email/ident_general/email',
        "website"               => 'trans_email/ident_general/email'
    );

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        Mage::log(sprintf("%s->var=%s", __METHOD__, print_r($post, true)) );        
        
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
				if (array_key_exists($post['enquiry'], $this->directEmailTo)) {				    
				    $enquiry = $post['enquiry'];
				    $recipient = Mage::getStoreConfig($this->directEmailTo[$enquiry]);
                }
                else {
                    $recipient = Mage::getStoreConfig('trans_email/ident_general/email');
				}
				
				$sender = array (
				    'name'	=>	$post['name'],
                    'email'	=>	Mage::getStoreConfig(self::XML_PATH_EMAIL_SENDER)
                );
                
                //Mage::log(sprintf("%s->recipient=%s,sender=%s", __METHOD__, $recipient, print_r($sender, true)) );
				
                $mailTemplate = Mage::getModel('core/email_template');
                
                /* @var $mailTemplate Mage_Core_Model_Email_Template */
                $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->setReplyTo($post['email'])
                    ->sendTransactional(
                        Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE),
                        $sender,
                        $recipient,
                        null,
                        array('data' => $postObject)
                    );

                if (!$mailTemplate->getSentSuccess()) {
                    throw new Exception();
                }

                $translate->setTranslateInline(true);

                // clear messages
                Mage::getSingleton('core/session')->getMessages(true);
                Mage::getSingleton('customer/session')->addSuccess(Mage::helper('contacts')->__('Your inquiry was submitted and will be responded to as soon as possible. Thank you for contacting us.'));
                $this->_redirect('*/*/');

                return;
            } catch (Exception $e) {
                $translate->setTranslateInline(true);

                Mage::getSingleton('customer/session')->addError(Mage::helper('contacts')->__('Unable to submit your request. Please, try again later'));
                $this->_redirect('*/*/');
                return;
            }

        } else {
            $this->_redirect('*/*/');
        }
    }

}
