<?php

class Application_Model_UserCreatedQuestionMapper extends Application_Model_AbstractQuestionMapper
{
	public function getQAAuthentication($user) {
		
		$userTable = new Application_Model_DbTable_Users();
		$userRow = $userTable->find($user->getId())->current();
		
		$qaAuthentication = new Application_Model_QAAuthentication();
		$qaAuthentication->setQuestion($this->_decryptAuthenticationString($userRow->user_created_question));
		$qaAuthentication->setAnswer($this->_decryptAuthenticationString($userRow->user_created_answer));
				
		return $qaAuthentication;
	}
}

