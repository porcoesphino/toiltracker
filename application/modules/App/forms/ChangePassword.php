<?php

class App_Form_ChangePassword extends Zend_Form
{

    public function init()
    {
		//Password section
        $currentPassword = new Zend_Form_Element_Password('current_password');
        $currentPassword->renderPassword = true;
        $currentPassword->setRequired(true);
        $passwordStringLength = new Zend_Validate_StringLength(array('min' => 6, 'max' => 15));
        $passwordStringLength->setMessage(
        	'Password should be between 6 and 15 characters long.', 
        	Zend_Validate_StringLength::TOO_LONG
        );
        $currentPassword->addValidator($passwordStringLength, true);
        $currentPassword->setAttrib('maxlength', 15);
        
        $passwordAlnumValidator = new Zend_Validate_Alnum();
    	$passwordAlnumValidator->setMessage(
    		'Password should contain letters and digits only.',
    		Zend_Validate_Alnum::NOT_ALNUM
    	);
        $currentPassword->addValidator($passwordAlnumValidator, true);
        $this->addElement($currentPassword);
        
        $newPassword = clone $currentPassword;
        $newPassword->setName('new_password');
        $this->addElement($newPassword);
        
        $confirmNewPassword = clone $currentPassword;
        $confirmNewPassword->setName('confirm_new_password');
        $this->addElement($confirmNewPassword);
        
        $apply = new Zend_Form_Element_Submit('apply');
        $apply->setLabel('Apply');
        $apply->setIgnore(true);
        $apply->clearDecorators();
        $apply->addDecorator(new Zend_Form_Decorator_ViewHelper());
        $this->addElement($apply);
        
        foreach($this->getElements() as $currentElement) {
        	$currentElement->removeDecorator('HtmlTag');
        	$currentElement->removeDecorator('Label');
        	if(!($currentElement instanceof Zend_Form_Element_Submit)) {
        
        		$currentElement->addFilter('StripTags');
        	}
        }
    }

	public function isValid($formData) {
		
		//Check to ensure that new password is the same as old password.
		$returnVal = parent::isValid($formData);
		if($formData['new_password'] != $formData['confirm_new_password']) {
			
			//Only attach the error message if both form elements are currently valid
			//(this will ensure that multiple error messages are not displayed onscreen.)
			$newPassword = $this->getElement('new_password');
			$confirmNewPassword = $this->getElement('confirm_new_password');
			if(!($newPassword->hasErrors() || $confirmNewPassword->hasErrors())) {
				
				$returnVal = false;
				$confirmNewPassword->addError('Passwords do not match.');
			}
		}
		return $returnVal;
	}
}

