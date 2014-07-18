<?php
class Balanced_CustomerSurvey_IndexController extends Mage_Core_Controller_Front_Action
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
			Mage::getSingleton('customer/session')->addSuccess($this->__('You were successfully unsubscribed'));
		}
		
		$this->loadLayout(); 
		
		$this->renderLayout();

    }
	
	public function completeAction()
    {		
		$this->loadLayout(); 
		
		$this->renderLayout();
    }
	
	public function saveAction() {
		//first get the survey id that we are looking at.
		$surveyID = $this->getRequest()->getParam('survey_number');

		if (array_key_exists('rand',$_SESSION) && intval($this->getRequest()->getParam('rand')) != intval($_SESSION['rand'])){
			echo "<script>window.history.back();</script>";
			Mage::getSingleton('core/session')->addError('Answer for the calculation is wrong. Please try again.');
			return;
		}

		if($surveyID) {	
			//get each question that is in this survey
			$questions  = Mage::getModel('customersurvey/questions')->getCollection()->addFieldToFilter('customersurvey_id', $surveyID);

			//prepare content for email
			$email_content = "Hi, you have a new feedback from online store!<p/>";

			foreach($questions as $question) {
				$result = $this->getRequest()->getParam('question' . $question->question_id);

				if($result != '') {
					$NewResult = Mage::getModel('customersurvey/results');
					$NewResult->customersurvey_id = $surveyID;
					$NewResult->question_id = $question->question_id;
					$NewResult->answer = (string)$result;
					$NewResult->save();
				}
				$email_content .= "<b>".$question->question."</b><br/>".$result."<p/>";
			}

			$email_content .= "<b>HTTP AGENT</b><br/>".$_SERVER['HTTP_USER_AGENT']."<p/>";

		}
		
		//email
		$feedback_name = Mage::getStoreConfig('trans_email/feedbackemail/name'); 
		$feedback_email = Mage::getStoreConfig('trans_email/feedbackemail/email'); 
		$store_name = Mage::app()->getStore()->getName();
		$store_email = Mage::getStoreConfig('trans_email/ident_general/email');

		$mail = Mage::getModel('core/email');
		$mail->setToName($feedback_name);
		$mail->setToEmail($feedback_email);
		$mail->setBody($email_content);
		$mail->setSubject("Feedback From ".$store_name);
		
		if (Mage::app()->isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn()) 
		{
			$mail->setFromName(Mage::getSingleton('customer/session')->getCustomer()->getName());
		}
		else
		{
			$mail->setFromName($store_name);
		}
		$mail->setFromEmail($store_email);	
		$mail->setType('html');
		
		if (!empty($feedback_email)){
			try {
				$mail->send();
			}
			catch (Exception $e) {
				Mage::getSingleton('core/session')->addError('Unable to send.');
				$this->_redirect('');
			}
		}

		$mail->setToName($store_name);
		$mail->setToEmail($store_email);
		try {
			$mail->send();
		}
		catch (Exception $e) {
			Mage::getSingleton('core/session')->addError('Unable to send.');
			$this->_redirect('');
		}

		$this->_redirect('*/*/complete/', array('id' => $surveyID));
	}
}