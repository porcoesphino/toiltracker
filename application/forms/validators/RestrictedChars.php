<?php

class Application_Form_Validators_RestrictedChars extends Zend_Validate_Abstract {
	
	const INVALID_CHARS = 'invalid_chars';
	
	protected $_messageTemplates = array(
			self::INVALID_CHARS => "Contains unexpected characters"
	);
	
	public function isValid($value) {
		
		$this->_setValue($value);
		if(preg_match("/[^A-Za-z\s-']/", $value)) {
			
			$this->_error(self::INVALID_CHARS);
			return false;
		}
		return true;
	}
}