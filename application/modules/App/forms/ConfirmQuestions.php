<?php

class App_Form_ConfirmQuestions extends Zend_Form
{

    public function init()
    {
    	$this->setName('confirm-questions');
    	$this->setMethod('post');
    	$this->setAction('/App/ForgotPassword/confirm-questions');
    	    	
    	$cannedAnswer = new Zend_Form_Element_Text('canned_answer');
        $cannedAnswer->setRequired(true);
        $validator = new Zend_Validate_StringLength(array('max' => 50));
        $validator->setMessage('Answer should be 50 characters max.', Zend_Validate_StringLength::TOO_LONG);
        $cannedAnswer->addValidator($validator, true);
        $cannedAnswer->setAttrib('maxlength', 50);
        $cannedAnswer->addValidator(new Application_Form_Validators_RestrictedChars(), true);
        $this->addElement($cannedAnswer);
        
        $userCreatedAnswer = clone $cannedAnswer;
        $userCreatedAnswer->setName('user_created_answer');
    	$this->addElement($userCreatedAnswer);
        
        $next = new Zend_Form_Element_Submit('next');
        $next->setLabel('Next');
        $next->setIgnore(true);
        $next->clearDecorators();
        $next->addDecorator(new Zend_Form_Decorator_ViewHelper());
        $this->addElement($next);
        
        foreach($this->getElements() as $currentElement) {
        	$currentElement->removeDecorator('HtmlTag');
        	$currentElement->removeDecorator('Label');
        	if(!($currentElement instanceof Zend_Form_Element_Submit)) {
        
        		$currentElement->addFilter('StripTags');
        	}
        }
    }


}

