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

