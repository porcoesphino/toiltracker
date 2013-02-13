<?php

class Application_Model_UserMapper
{
	protected $_table;
	
	public function __construct() { 
		
		$this->_table = new Application_Model_DbTable_Users();
	}
	
	public function getDbAdapter() {
		
		return $this->_table->getAdapter();
	}
	
	/**
	 * Retrieve encrypted strings using the following:
	 * 
	 * $decrypted = openssl_decrypt($encryptedString, 'aes-128-cbc', $encryptionPassword, false, $initializationVector);
	 * 
	 * @param unknown $data
	 */
	public function insert($data) {
		
		//Encryption parameters.
		$salt = rand();
		$password = $password = hash('sha256', $data['password'] . $salt);
		
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/parameters.ini');
		$encryptionPassword = $config->encryption->encryptionPassword;
		$initializationVector = $config->encryption->initializationVector;
		
		$cannedAnswerEncrypted = openssl_encrypt($data['canned_answer'], 'aes-128-cbc', $encryptionPassword, false, $initializationVector);
		$userCreatedQuestionEncrypted = openssl_encrypt($data['user_created_question'], 'aes-128-cbc', $encryptionPassword, false, $initializationVector);
		$userCreatedAnswerEncrypted = openssl_encrypt($data['user_created_answer'], 'aes-128-cbc', $encryptionPassword, false, $initializationVector);
		
		$table = new Application_Model_DbTable_Users();
		$insertData = array(
			'email' => $data['email'],
			'password' => $password,
			'salt' => $salt,
			'name' => $data['name'],
			'role' => 4,
			'canned_question_id' => $data['canned_questions'],
			'canned_answer' => $cannedAnswerEncrypted,
			'user_created_question' => $userCreatedQuestionEncrypted,
			'user_created_answer' => $userCreatedAnswerEncrypted
		);
		$table->insert($insertData);
	}
	
	public function updatePassword($newPassword, $email) {
	
		$salt = rand();
		$newPassword = hash('sha256', $newPassword . $salt);
		
		$table = new Application_Model_DbTable_Users();
		$where = $table->getAdapter()->quoteInto('email = ?', $email);
		$insertData = array(
			'password' => $newPassword,
			'salt' => $salt
		);
		$table->update($insertData, $where);
	}
	
	public function isCorrectEmail($email) {
		
		//find email in the database. If row found, return true, else return false.
		$where = $this->_table->getAdapter()->quoteInto('email = ?', $email);
		$select = $this->_table->select()->where($where);
		$row = $this->_table->fetchRow($select);
		
		$returnVal = true;
		if(empty($row)) {
			
			$returnVal = false;
		}
		return $returnVal;
	}
}

