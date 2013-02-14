<?php

class App_ForgotPasswordController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * @todo
     * Lock the account if the user fails five times at this stage?
     *
     *
     */
    public function confirmEmailAction()
    {
    	$session = new Zend_Session_Namespace('forgotten_password');
    	$session->isEmailConfirmed = false;
    	
        $form = new App_Form_ConfirmEmail();
        $request = $this->getRequest();
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		//Form is valid, now check to ensure the email is valid
        		$formValues = $form->getValues();
        		$mapper = new Application_Model_UserMapper();
        		if($mapper->isCorrectEmail($formValues['email'])) {

        			//Email is correct, so make a note of this in the session and proceed to
        			//step 2.
        			$session->email = $formValues['email'];
        			$session->isEmailConfirmed = true;
        			$session->securityQuestionAttempt = 0;
        			
        			$redirector = $this->_helper->getHelper('Redirector');
        			$redirector->gotoRoute(
        				array(
        					'module' => 'App',
        					'controller' => 'ForgotPassword',
        					'action' => 'confirm-questions'
        				),
        				'module_full_path',
        				true
        			);
        		}
        		else {
        			
        			//Email does not exist in the database. Attach error message to the form.
        			$form->addError('Email does not exist in the database.');
        		}
        	}
        }
        $this->view->form = $form;
    }

    /**
     * @todo
     * Make use of FlashMessenger to notify the user of the reason why they are
     * redirected
     * back to confirmEmail, if applicable.
     *
     */
    public function confirmQuestionsAction()
    {
    	//Ensure that the user can only reach this stage if they have successfully confirmed
    	//their email address.
    	$session = new Zend_Session_Namespace('forgotten_password');
    	$redirect = false;
    	if(!isset($session->isEmailConfirmed)) {
    		
    		$redirect = true;
    	}
    	
    	if(!$session->isEmailConfirmed) {
    		
    		$redirect = true;
    	}
    	
    	if(!isset($session->securityQuestionAttempt)) {
    		
    		$redirect = true;
    	}
    	
    	if($session->securityQuestionAttempt > 3) {
    		
    		$redirect = true;
    	}
    	
    	if($redirect) {
    		
    		$redirector = $this->_helper->getHelper('Redirector');
    		$redirector->gotoRoute(
    			array(
    				'module' => 'App',
    				'controller' => 'ForgotPassword',
    				'action' => 'confirm-email'
    			),
    			'module_full_path',
    			true
    		);
    	}
    	
    	$form = new App_Form_ConfirmQuestions();
    	$request = $this->getRequest();
    	if($request->isPost()) {
    		
    		//Check for correct answers.
    		if($form->isValid($request->getPost())) {
    			
    			//The answers are well form, now check that they are
    			//correct.
    			if(true) {
    				
    				//Redirect to notification page.
    				$session->isQuestionsConfirmed = true;
    				$redirector = $this->_helper->getHelper('Redirector');
    				$redirector->gotoRoute(
    					array(
    						'module' => 'App',
    						'controller' => 'ForgotPassword',
    						'action' => 'confirm-notification'
    					),
    					'module_full_path',
    					true
    				);
    			}
    		}
    		else {
    			
    			//Zend_Form to redisplay with the user-content automatically injected
    			//and the error messages automatically rendered.
    		}
    	}
    	else {
    		
    		//Blank Zend_Form to automatically display.
    	}
    	
        //Display one of the canned questions, and a text box for the answer
    	//Display the user question and a text box for the answer.
    	$session->securityQuestionAttempt++;
    	$this->view->canned_question = 'Question 1';
    	$this->view->user_created_question = 'Question 2';
    	$this->view->form = $form;
    }

    public function confirmNotificationAction()
    {
    	$session = new Zend_Session_Namespace('forgotten_password');
    	$redirect = false;
    	if(!isset($session->isQuestionsConfirmed)) {
    		
    		$redirect = true;
    	}
    	
    	if(!$session->isQuestionsConfirmed) {
    		
    		$redirect = true;
    	}
    	
    	if(!isset($session->email)) {
    		
    		$redirect = true;
    	}
    	
    	if($redirect) {
    		
    		$redirector = $this->_helper->getHelper('Redirector');
    		$redirector->gotoRoute(
    			array(
    				'module' => 'App',
    				'controller' => 'ForgotPassword',
    				'action' => 'confirm-email'
    			),
    			'module_full_path',
    			true
    		);
    	}
    	
    	//Retrieve the email address then clear the session.
    	$email = $session->email;
    	Zend_Session::namespaceUnset('forgotten_password');
    	
    	//Change the password to a random password.
    	$mapper = new Application_Model_UserMapper();
    	$newPassword = 'asfss';
    	$mapper->updatePassword($newPassword, $email);
    	
    	//Email password to user.    	
    	$mail = new Zend_Mail();
    	$mail->setSubject('Password reset');
    	$mail->setFrom("noreply@{$config->system->url}", 'System');
    	$mail->addTo($email);
    	$mail->setBodyHtml("<p>Your new toiltracker password: $newPassword</p>");
    	$mail->send();
    }


}
