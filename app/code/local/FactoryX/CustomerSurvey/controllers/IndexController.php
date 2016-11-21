<?php

/**
 * Class FactoryX_CustomerSurvey_IndexController
 */
class FactoryX_CustomerSurvey_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {		
		$this->loadLayout(); 
		
		$this->renderLayout();
    }
	
	public function viewAction()
    {
		// Message when redirected from the Campaign Monitor unsubscription link
		if (strpos ($this->getRequest()->getServer('HTTP_REFERER'), "http://factoryx.cmail1.com") !== FALSE || strpos ($this->getRequest()->getServer('HTTP_REFERER'), "http://factoryx.cmail2.com") !== FALSE)
		{
			Mage::getSingleton('core/session')->addSuccess($this->__('You were successfully unsubscribed'));
		}
		
		$this->loadLayout(); 
		
		// Initiate the session messages
		$this->_initLayoutMessages('core/session');
		
		$this->renderLayout();

    }
	
	public function completeAction()
    {		
		$this->loadLayout(); 
		
		$this->renderLayout();
    }
	
	public function saveAction() {
		// First get the survey id that we are looking at.
		$surveyID = $this->getRequest()->getParam('survey_number');

		try
		{
			if (array_key_exists('rand',$_SESSION) && intval($this->getRequest()->getParam('rand')) != intval($_SESSION['rand']))
			{
				throw new Exception ('Answer for the calculation is wrong. Please try again.');
			}
		}
		catch (Exception $e)
		{
			Mage::getSingleton('customer/session')->addException($e, $e->getMessage());
			$this->_redirectReferer();
			return;
		}

		$emailTemplateVariables = array();

		if($surveyID)
		{
			// Get each question that is in this survey
			$questions  = Mage::getModel('customersurvey/questions')->getCollection()->addFieldToFilter('customersurvey_id', $surveyID);

			foreach($questions as $question)
			{
				$result = $this->getRequest()->getParam('question' . $question->question_id);

				if($result != '')
				{
					$NewResult = Mage::getModel('customersurvey/results');
					$NewResult->customersurvey_id = $surveyID;
					$NewResult->question_id = $question->question_id;
					$NewResult->answer = (string)$result;
					$NewResult->save();
				}
				$emailTemplateVariables['questions'][] = array('question' => $question->question, 'result' => $result);
			}

			$emailTemplateVariables['useragent'] = $_SERVER['HTTP_USER_AGENT'];

		}

		$emailTemplateVariables['storename'] = Mage::app()->getStore()->getName();

		// Get the transactional email template
		$templateId = Mage::getStoreConfig('customersurvey/feedbackemail/email_template');
		// Get the sender
		$sender = array();
		if (Mage::app()->isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn())
		{
			$sender['name'] = Mage::getSingleton('customer/session')->getCustomer()->getName();
		}
		else
		{
			$sender['name'] = $emailTemplateVariables['storename'];
		}
		$sender['email'] = Mage::getStoreConfig('customersurvey/ident_general/email');

		$recipientEmail = Mage::getStoreConfig('customersurvey/feedbackemail/email') ? Mage::getStoreConfig('customersurvey/feedbackemail/email') : Mage::getStoreConfig('trans_email/ident_general/email');
		$recipientName = Mage::getStoreConfig('customersurvey/feedbackemail/name') ? Mage::getStoreConfig('customersurvey/feedbackemail/name') : $emailTemplateVariables['storename'];

		try
		{
			// Send the test email
			Mage::getModel('core/email_template')
				->sendTransactional(
					$templateId,
					$sender,
					$recipientEmail,
					$recipientName,
					$emailTemplateVariables,
					null);
		}
		catch (Exception $e)
		{
			Mage::getSingleton('core/session')->addError('Unable to send.');
			$this->_redirect('');
		}

		$this->_redirect('*/*/complete/', array('id' => $surveyID));
	}
}