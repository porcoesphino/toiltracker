<?php

class App_Form_Login extends Zend_Form
{

    public function init()
    {
    	$this->setName('login');
    	$this->setMethod('post');
    	$this->setAction('/App/Login');
    	
    	$email = new Zend_Form_Element_Text('email');
    	$email->setRequired(true);
    	$email->addValidator(new Zend_Validate_StringLength(array('max' => 45)), true);
    	$email->setAttrib('maxlength', 45);
    	$email->addValidator(new Zend_Validate_EmailAddress(), true);
    	$email->addErrorMessage('Email address is incorrect.'); //Will apply to any validation failures.
    	$email->class = 'input-block-level';
    	$this->addElement($email);
    	
    	
    	//Password configuration 	
    	$password = new Zend_Form_Element_Password('password');
    	$password->setRequired(true);
    	$password->addValidator(new Zend_Validate_StringLength(array('max' => 15)), true);
    	$password->setAttrib('maxlength', 15);
    	$password->addValidator(new Zend_Validate_Alnum(), true);
    	$password->addErrorMessage('Password is incorrect.'); //Will supercede all validation messages.
    	$password->class = 'input-block-level';
    	$this->addElement($password);
    	
    	
    	$login = new Zend_Form_Element_Submit('login');
    	$login->setLabel('Login');
    	$login->setIgnore(true);
    	$login->clearDecorators();
    	$login->addDecorator(new Zend_Form_Decorator_ViewHelper());
    	$login->class = 'btn btn-large btn-primary';
    	$this->addElement($login);
    	
    	foreach($this->getElements() as $currentElement) {
    		$currentElement->removeDecorator('HtmlTag');
    		$currentElement->removeDecorator('Label');
    		if(!($currentElement instanceof Zend_Form_Element_Submit)) {
    			
    			$currentElement->addFilter('StripTags');
    		}
    	}
    }
}

