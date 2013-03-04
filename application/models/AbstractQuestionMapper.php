<?php

class Application_Model_AbstractQuestionMapper
{
	protected function _decryptAuthenticationString($answer) {
	
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/parameters.ini');
		$encryptionPassword = $config->encryption->encryptionPassword;
		$initializationVector = $config->encryption->initializationVector;
	
		$decryptedAnswer = openssl_decrypt($answer, 'aes-128-cbc', $encryptionPassword, false, $initializationVector);
		return $decryptedAnswer;
	}
}

