<?php

class Application_Model_ForgotPasswordProcessManager
{
	const STEP1 = 1;
	const STEP2 = 2;
	const STEP3 = 3;
	
	protected static $_self;
	protected $_session;
	
	private function __construct() {
		
		$this->_session = new Zend_Session_Namespace('forgot_password');
		$noOfAttempts = $this->_session->securityQuestionAttempt;
		
		if(!isset($noOfAttempts)) {
			
			$this->_session->securityQuestionAttempt = 0;
		}
	}
	
	public static function getInstance() {
		
		if(empty(Application_Model_ForgotPasswordProcessManager::$_self)) {
			
			Application_Model_ForgotPasswordProcessManager::$_self = new Application_Model_ForgotPasswordProcessManager();
		}
		return Application_Model_ForgotPasswordProcessManager::$_self;
	}
	
	public function getIsStepAllowed($stepNumber) {

		$isAllowed = false;
		if($stepNumber == Application_Model_ForgotPasswordProcessManager::STEP1) {
			
			$isAllowed = $this->_isStep1Allowed();
		}
		else if($stepNumber == Application_Model_ForgotPasswordProcessManager::STEP2) {
			
			$isAllowed = $this->_isStep2Allowed();
		}
		else if($stepNumber == Application_Model_ForgotPasswordProcessManager::STEP3) {

			$isAllowed = $this->_isStep3Allowed();
		}
		
		return $isAllowed;
	}
	
	/**
	 * @todo
	 * Take into account the number of attempts made by the ip address and
	 * block after a set number of attempts for 2 minutes.
	 * @return boolean
	 */
	protected function _isStep1Allowed() {
		
		//Step 1 is always allowed.
		return true;
	}
	
	protected function _isStep2Allowed() {

		if(($this->_session->isStep1Complete) && (!$this->_session->isStep2Complete)) {

			if($this->_session->securityQuestionAttempt < 4) {

				return true;
			}
		}
		return false;
	}
	
	protected function _isStep3Allowed() {
		
		if(($this->_session->isStep1Complete) && ($this->_session->isStep2Complete)
			&& (!$this->_session->isStep3Complete)) {
				
			return true;
		}
		
		return false;
	}
	
	public function setStepComplete($stepNumber) {
		
		switch($stepNumber) {
			
			case Application_Model_ForgotPasswordProcessManager::STEP1:
				$this->_session->isStep1Complete = true;
				break;
				
			case Application_Model_ForgotPasswordProcessManager::STEP2:
				$this->_session->isStep2Complete = true;
				break;
			case Application_Model_ForgotPasswordProcessManager::STEP3:
				$this->_session->isStep3Complete = true;
				break;
			default:
				throw new Zend_Exception('Invalid forgotten password step');
		}
	}
	
	public function incrementSecurityQuestionAttempt() {
	
		$this->_session->securityQuestionAttempt++;
	}
	
	public function resetProcess() {
	
		$this->_session = new Zend_Session_Namespace('forgot_password');
		
		$this->_session->isStep1Complete = false;
		$this->_session->isStep2Complete = false;
		$this->_session->isStep3Complete = false;
		$this->_session->securityQuestionAttempt = 0;
	}
}

