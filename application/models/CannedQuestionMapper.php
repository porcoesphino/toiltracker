<?php

class Application_Model_CannedQuestionMapper extends Application_Model_AbstractQuestionMapper
{
	protected $_table;
	
	public function __construct() {
		$this->_table = new Application_Model_DbTable_CannedQuestions();
	}
	public function getQAAuthentication($user) {
		
		$userTable = new Application_Model_DbTable_Users();
		$userRow = $userTable->find($user->getId())->current();
		
		$qaAuthentication = new Application_Model_QAAuthentication();
		$qaAuthentication->setAnswer($this->_decryptAuthenticationString($userRow->canned_answer));
		
		$cannedQuestionRow = $this->_table->find($userRow->canned_question_id)->current();
		$qaAuthentication->setQuestion($cannedQuestionRow->canned_question);
				
		return $qaAuthentication;
	}
}

