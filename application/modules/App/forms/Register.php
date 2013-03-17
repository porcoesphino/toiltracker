<?php

class App_Form_Register extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $this->setName('register');
        $this->setAction('/App/Register');
        $this->setMethod('post');
        
        //Name section        	
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true);
        $name->addValidator(new Application_Form_Validators_RestrictedChars(), true);
        $nameLengthValidator = new Zend_Validate_StringLength(array('max' => 35));
        $nameLengthValidator->setMessage('Name is too long to store', Zend_Validate_StringLength::TOO_LONG);
        $name->addValidator($nameLengthValidator, true);
        $name->setAttrib('maxlength', 35);
        $this->addElement($name);
        
        //Email section
        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true);
        $email->addValidator(new Zend_Validate_StringLength(array('max' => 45)), true);
        $email->addValidator(new Zend_Validate_EmailAddress(), true);
        $email->setAttrib('maxlength', 45);
        $email->addErrorMessage('Email address is invalid'); //Displayed for all errors
        $this->addElement($email);
        
        $confirmEmail = new Zend_Form_Element_Text('confirm_email');
        $confirmEmail->setRequired(true);
        $confirmEmail->addValidator(new Zend_Validate_StringLength(array('max' => 45)), true);
        $confirmEmail->addValidator(new Zend_Validate_EmailAddress(), true);
        $confirmEmail->setAttrib('maxlength', 45);
        $confirmEmail->addErrorMessage('Email address is invalid'); //Displayed for all errors
        $this->addElement($confirmEmail);
        
        
        //Password section
        $password = new Zend_Form_Element_Password('password');
        $password->renderPassword = true;
        $password->setRequired(true);
        $passwordStringLength = new Zend_Validate_StringLength(array('min' => 6, 'max' => 15));
        $passwordStringLength->setMessage(
        	'Password should be between 6 and 15 characters long.', 
        	Zend_Validate_StringLength::TOO_LONG
        );
        $password->addValidator($passwordStringLength, true);
        $password->setAttrib('maxlength', 15);
        
        $passwordAlnumValidator = new Zend_Validate_Alnum();
    	$passwordAlnumValidator->setMessage(
    		'Password should contain letters and digits only.',
    		Zend_Validate_Alnum::NOT_ALNUM
    	);
        $password->addValidator($passwordAlnumValidator, true);
        $this->addElement($password);
        
        $confirmPassword = clone $password;
        $confirmPassword->setName('confirm_password');
        $this->addElement($confirmPassword);
        
        
        //Security Q/A 1
        $cannedQuestions = new Zend_Form_Element_Select('canned_questions');
        $cannedQuestions->setMultiOptions(
        	array(
        		'' => 'Please select',
        		'1' => 'Favourite historical person',
        		'2' => 'Favorite childhood friend',
        		'3' => 'Name of your first school',
        		'4' => "Mother's maiden name",
        		'5' => 'Dream job as a child'
        	)
        );
        $cannedQuestions->setRequired(true);
        $this->addElement($cannedQuestions);
        
        $cannedAnswer = new Zend_Form_Element_Text('canned_answer');
        $cannedAnswer->setRequired(true);
        $validator = new Zend_Validate_StringLength(array('max' => 50));
        $validator->setMessage('Answer should be 50 characters max.', Zend_Validate_StringLength::TOO_LONG);
        $cannedAnswer->addValidator($validator, true);
        $cannedAnswer->setAttrib('maxlength', 50);
        $this->addElement($cannedAnswer);
        
        
        //Security Q/A 2
        $userCreatedQuestion = clone $cannedAnswer;
        $userCreatedQuestion->setName('user_created_question');
        $this->addElement($userCreatedQuestion);
        
        $userCreatedAnswer = clone $cannedAnswer;
        $userCreatedAnswer->setName('user_created_answer');
        $this->addElement($userCreatedAnswer);
        
        $terms = new Zend_Form_Element_Checkbox('terms');
        $terms->setRequired(true);
        $terms->setUncheckedValue(null);
        $terms->removeDecorator('Errors');
        $terms->removeDecorator('FormErrors');
        $this->addElement($terms);
        
        
        //Captcha
        $captcha = new Zend_Form_Element_Captcha(
        	'captcha',
        	array(
        		'captcha' => array(
					'captcha' => 'Image',
					'font' => APPLICATION_PATH . '/../public/captcha/font/Verdana.ttf',
					'fontSize' => '26',
					'wordLen' => 5,
					'height' => '60',
					'width' => '180',
					'imgDir' => APPLICATION_PATH.'/../public/captcha/images/',
					'imgUrl' => '/captcha/images/',
					'dotNoiseLevel' => 50,
					'lineNoiseLevel' => 5
				)
        	)
        );
        $captcha->setRequired(true);
        $this->addElement($captcha);
        
        $register = new Zend_Form_Element_Submit('register');
        $register->setLabel('Register');
        $register->setIgnore(true);
        $register->clearDecorators();
        $register->addDecorator(new Zend_Form_Decorator_ViewHelper());
        $register->class = 'btn btn-success';
        $this->addElement($register);
        
        foreach($this->getElements() as $currentElement) {
        	$currentElement->removeDecorator('HtmlTag');
        	$currentElement->removeDecorator('Label');
        	if(!($currentElement instanceof Zend_Form_Element_Submit)) {
        		
        		$currentElement->addFilter('StripTags');
        	}
        }
    }

    public function isValid($formData) {
    
    	//Check to ensure that the email addresses match
    	$returnVal = parent::isValid($formData);
    	if($formData['email'] != $formData['confirm_email']) {
    		
    		//Attach error to confirm_email and toggle error.
    		$this->getElement('confirm_email')->clearErrorMessages();
    		$this->getElement('confirm_email')->addErrorMessage('The email addresses do not match');
    		$this->getElement('confirm_email')->markAsError();
    		$returnVal = false;
    	}
    	
    	//Check to ensure the passwords match
    	if($formData['password'] != $formData['confirm_password']) {
    		
    		$this->getElement('confirm_password')->clearErrorMessages();
    		$this->getElement('confirm_password')->addErrorMessage('The passwords do not match');
    		$this->getElement('confirm_password')->markAsError();
    		$returnVal = false;
    	}
    	
    	return $returnVal;
    }
}

