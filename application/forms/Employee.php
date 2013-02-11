<?php

class Application_Form_Employee extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
   		$this->setName('employee');
   		
   		$id = new Zend_Form_Element_Hidden('id');
   		$this->addElement($id);
    	
   		$nameLengthValidator = new Zend_Validate_StringLength(array('max' => 35));
   		$nameLengthValidator->setMessage('Name is too long to store', Zend_Validate_StringLength::TOO_LONG);
   		   	
    	$name = new Zend_Form_Element_Text('name');
    	$name->setRequired(true);
    	$name->addValidator(new Application_Form_Validators_RestrictedChars(), true);
    	$name->addValidator($nameLengthValidator, true);
		$name->setAttrib('maxlength', 35);
    	$this->addElement($name);
		
    	//Email form element
    	$emailLengthValidator = new Zend_Validate_StringLength(array('max' => 45));
    	$emailLengthValidator->setMessage('Email is too long to store', Zend_Validate_StringLength::TOO_LONG);
    	
    	$emailAddressValidator = new Zend_Validate_EmailAddress();
    	
    	$email = new Zend_Form_Element_Text('email');
    	$email->setRequired(false);
    	$email->addValidator($emailLengthValidator, true);
    	$email->setAttrib('maxlength', 45);
    	$email->addValidator($emailAddressValidator, true);
    	$email->addErrorMessage('Email address is invalid');
    	$this->addElement($email);
    	
    	$add = new Zend_Form_Element_Submit('save');
    	$add->setIgnore(true);
    	$add->clearDecorators();
    	$add->addDecorator('ViewHelper');
    	$this->addElement($add);
    	
    	foreach($this->getElements() as $currentElement) {
    		
    		$currentElement->removeDecorator('HtmlTag');
    		$currentElement->removeDecorator('Label');
    		if(!($currentElement instanceof Zend_Form_Element_Submit)) {
    			
    			$currentElement->addFilter('StripTags');
    		}
    	}
    }
}

