<?php

/**
 * Class FactoryX_CustomerSurvey_Adminhtml_SurveyController
 */
class FactoryX_CustomerSurvey_Adminhtml_SurveyController extends Mage_Adminhtml_Controller_Action
{

	/**
	 * @return mixed
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('factoryx_menu/customersurvey');
	}

	/**
	 * @return $this
	 */
	protected function _initAction()
	{
		$this->loadLayout()->_setActiveMenu('factoryx_menu/customersurvey');

		return $this;
	}

	public function indexAction()
	{
		try {
			$this->_initAction();
			$this->_addContent($this->getLayout()->createBlock('customersurvey/adminhtml_survey'));
			$this->renderLayout();
		}
		catch(Exception $ex) {
			Mage::helper('customersurvey')->log(sprintf("%s->error=%s", __METHOD__, print_r($ex, true)), Zend_Log::DEBUG );
		}
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function editAction()
	{
		$customersurveyId     = $this->getRequest()->getParam('id');
		$customersurveyModel  = Mage::getModel('customersurvey/survey')->load($customersurveyId);

		if ($customersurveyModel->getId() || $customersurveyId == 0)
		{
			// Data from the form
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

			// If data from the form, we add it to the model
			if (!empty($data)) {
				$customersurveyModel->setData($data);
			}

			Mage::register('customersurvey_data', $customersurveyModel);
			$this->_initAction();
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('customersurvey/adminhtml_survey_edit'))
				->_addLeft($this->getLayout()->createBlock('customersurvey/adminhtml_survey_edit_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customersurvey')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function resultsAction()
	{
		$customersurveyId     = $this->getRequest()->getParam('id');
		$customersurveyModel  = Mage::getModel('customersurvey/survey')->load($customersurveyId);

		if ($customersurveyModel->getId() || $customersurveyId == 0)
		{
			Mage::register('customersurvey_data', $customersurveyModel);
			$this->_initAction();
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('customersurvey/adminhtml_survey_results'))->_addLeft($this->getLayout()->createBlock('customersurvey/adminhtml_survey_results_tabs'));
			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customersurvey')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost())
		{
			// Id
			$customersurveyId = $this->getRequest()->getParam('id');

			$model = Mage::getModel('customersurvey/survey');
			$model->setData($data)->setId($customersurveyId);
			$model->save();

			$questionList = $this->getRequest()->getParam('questionList');

			// Sort the posted numbers of questions into an array
			$questionList = substr($questionList, 0, -1);
			$questionList = substr($questionList, 1, strlen($questionList) - 1); //remove leading space
			$questionList = explode(", ", $questionList);

			// Delete all questions that have to do with this ID
			$questions = Mage::getModel('customersurvey/questions')->getCollection()->addFieldToFilter('customersurvey_id', $customersurveyId);

			$keepList = array();
			// Generate a list of questions to keep
			foreach ($questionList as $questionNumber) {
				$questionOldID = $this->getRequest()->getParam('question_oldid_' . $questionNumber);

				if ($questionOldID) {
					$keepList[] = $questionOldID;
				}

			}

			foreach ($questions as $question) {
				// Only delete questions that are not in the array
				if (!in_array($question->question_id, $keepList)) {
					$question->delete();
				}
			}

			foreach ($questionList as $questionNumber) {
				// Get the POSTed question information
				$questionTitle = $this->getRequest()->getParam('question_title_' . $questionNumber);
				$questionType = $this->getRequest()->getParam('question_type_' . $questionNumber);
				$questionSortOrder = $this->getRequest()->getParam('question_sortorder_' . $questionNumber);
				$questionOldID = $this->getRequest()->getParam('question_oldid_' . $questionNumber);
				$questionDescription = $this->getRequest()->getParam('question_description_' . $questionNumber);

				$totalAnswers = $this->getRequest()->getParam('question_answers_total_ids_' . $questionNumber);
				$answersString = '';

				for ($i = 1; $i <= $totalAnswers; $i++) {
					$nextAnswer = $this->getRequest()->getParam('question_answer_' . $questionNumber . '_' . $i);

					if ($nextAnswer) {
						if ($answersString == '') {
							$answersString = $nextAnswer;
						} else {
							$answersString .= "|||" . $nextAnswer;
						}

					}
				}

				if (!$questionOldID) {
					// If we don't have an old ID, then we are creating a new question
					$newQuestion = Mage::getModel('customersurvey/questions');

					$newQuestion->customersurvey_id = $customersurveyId;
					$newQuestion->question = $questionTitle;
					$newQuestion->answer_type = $questionType;
					$newQuestion->sort_order = $questionSortOrder;
					$newQuestion->possible_answers = $answersString;

					$newQuestion->save();
				} else {
					// If we have an old ID, then we are updating an old question
					$oldQuestion = Mage::getModel('customersurvey/questions')->load($questionOldID);

					$oldQuestion->question = $questionTitle;
					$oldQuestion->answer_type = $questionType;
					$oldQuestion->sort_order = $questionSortOrder;
					$oldQuestion->possible_answers = $answersString;

					$oldQuestion->save();
				}

			}
		}

		$this->_redirect('*/*/');
	}

	public function deleteAction()
	{
		// Delete the survey
		$customersurveyId = (int) $this->getRequest()->getParam('id');
		if ($customersurveyId)
		{
			try {
				$customersurveyModel = Mage::getModel('customersurvey/survey')->load($customersurveyId);
				$customersurveyModel->delete();

				// Delete all questions from this survey
				$questions = Mage::getModel('customersurvey/questions')->getCollection()->addFieldToFilter('customersurvey_id', $customersurveyId);

				foreach ($questions as $question) {
					$question->delete();
				}

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customersurvey')->__('Survey was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function exportAction()
	{
		$customersurveyId = $this->getRequest()->getParam('id');
		$customersurvey  = Mage::getModel('customersurvey/results')->getCollection();
		$customersurvey->getSelect()->join(array('ques'=>Mage::getSingleton("core/resource")->getTableName('customersurvey/questions')),'main_table.question_id = ques.question_id',array('question'));
		$customersurvey->addFieldToFilter('main_table.customersurvey_id',$customersurveyId);

		// Header
		$content = "Time,Question,Answer\n";
		foreach($customersurvey as $response) {
			$content .= "\"{$response->input_time}\",\"{$response->question}\",\"{$response->answer}\"\n";
		}
		$this->_prepareDownloadResponse('export.csv', $content, 'text/csv');
	}

}