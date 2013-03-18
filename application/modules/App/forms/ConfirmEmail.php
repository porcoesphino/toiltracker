<?php

class App_Form_ConfirmEmail extends Zend_Form
{

    public function init()
    {
        //Email, capthca, submit
    	$this->setName('confirm-email');
    	$this->setMethod('post');
    	$this->setAction('/App/ForgotPassword/confirm-email');
    	 
    	$email = new Zend_Form_Element_Text('email');
    	$email->setRequired(true);
    	$email->addValidator(new Zend_Validate_StringLength(array('max' => 45)), true);
    	$email->setAttrib('maxlength', 45);
    	$email->addValidator(new Zend_Validate_EmailAddress(), true);
    	$email->addErrorMessage('Email address is invalid.'); //Will apply to any validation failures.
    	$this->addElement($email);
    	
    	//Captcha. Note this relies on the vowels being modified in Zend_Captcha_Word, until
        //a better solution can be found.
        $captcha = new Zend_Form_Element_Captcha(
        	'captcha',
        	array(
        		'captcha' => array(
					'captcha' => 'Image',
					'font' => APPLICATION_PATH . '/../public/captcha/font/Verdana.ttf',
					'fontSize' => '30',
					'wordLen' => 5,
					'height' => '90',
					'width' => '180',
					'imgDir' => APPLICATION_PATH.'/../public/captcha/images/',
					'imgUrl' => '/captcha/images/',
					'dotNoiseLevel' => 25,
					'lineNoiseLevel' => 2,
        			'useNumbers' => false
				)
        	)
        );
    	$captcha->setRequired(true);
    	$captcha->setLabel('Verify');
    	$this->addElement($captcha);
    	
    	$next = new Zend_Form_Element_Submit('next');
    	$next->setLabel('Next');
    	$next->setIgnore(true);
    	$next->clearDecorators();
    	$next->addDecorator(new Zend_Form_Decorator_ViewHelper());
    	$next->class = 'btn btn-success';
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

