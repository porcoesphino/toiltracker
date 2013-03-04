<?php

class App_ForgotPasswordController extends Zend_Controller_Action
{

    public function init()
    {
        //Check to ensure the user is permitted to access the actions they are requesting.
    	$isStepAllowed = false;
    	if($this->getRequest()->getActionName() == 'confirm-email') {
    		
    		$stepNumber = Application_Model_ForgotPasswordProcessManager::STEP1;
    		$isStepAllowed = Application_Model_ForgotPasswordProcessManager::getInstance()->getIsStepAllowed($stepNumber);
    		if(!$isStepAllowed) {
    			
    			//Redirect to the home page.
    			$redirector = $this->_helper->getHelper('Redirector');
    			$redirector->gotoRoute(
    				array(
    					'module' => 'default',
    					'controller' => 'Index',
    					'action' => 'index'
    				),
    				'root',
    				true
    			);
    		}
    	}
    	
    	if($this->getRequest()->getActionName() == 'confirm-questions') {
    		
    		$stepNumber = Application_Model_ForgotPasswordProcessManager::STEP2;
    		$isStepAllowed = Application_Model_ForgotPasswordProcessManager::getInstance()->getIsStepAllowed($stepNumber);
    	}
    	else if($this->getRequest()->getActionName() == 'confirm-notification') {
    	
    		$stepNumber = Application_Model_ForgotPasswordProcessManager::STEP3;
    		$isStepAllowed = Application_Model_ForgotPasswordProcessManager::getInstance()->getIsStepAllowed($stepNumber);
    	}
    	
    	if(!$isStepAllowed) {
    		
    		//Redirect to Step1.
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
    }

    /**
     * @todo
     * Lock the account if the user fails five times at this stage?
     */
    public function confirmEmailAction()
    {    	
    	Application_Model_ForgotPasswordProcessManager::getInstance()->resetProcess();
    	
    	$form = new App_Form_ConfirmEmail();
        $request = $this->getRequest();
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		//Form is valid, now check to ensure the email is valid
        		$formValues = $form->getValues();
        		$mapper = new Application_Model_UserMapper();
        		if($mapper->isEmailInDatabase($formValues['email'])) {

        			//Email is correct, so make a note of this in the session and proceed to
        			//the next step.
        			$session = new Zend_Session_Namespace('forgot_password_container');
        			$session->email = $formValues['email'];
        			        			
        			Application_Model_ForgotPasswordProcessManager::getInstance()
        				->setStepComplete(Application_Model_ForgotPasswordProcessManager::STEP1);
        			        				
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
     */
    public function confirmQuestionsAction()
    {
    	$session = new Zend_Session_Namespace('forgot_password_container');
    	$form = new App_Form_ConfirmQuestions();
    	
    	$request = $this->getRequest();
    	if($request->isPost()) {
    		
    		//Check for correct answers.
    		if($form->isValid($request->getPost())) {
    			
    			//The answers are well formed, now check that they are
    			//correct.
    			$formValues = $form->getValues();
    			$userMapper = new Application_Model_UserMapper();
    			$user = $userMapper->getUserByEmail($session->email);
    			
    			$mapper = new Application_Model_CannedQuestionMapper();
    			$authentication = $mapper->getQAAuthentication($user);
    			
    			$isAuthenticated = true;
    			if($formValues['canned_answer'] != $authentication->getAnswer()) {

    				$isAuthenticated = false;
    			}
    			
    			if($isAuthenticated) {
    				
    				//Authenticated successfully so far. Next check the user created
    				//question / answer.
	    			$mapper = new Application_Model_UserCreatedQuestionMapper();
	    			$authentication = $mapper->getQAAuthentication($user);
	    			if($formValues['user_created_answer'] != $authentication->getAnswer()) {
	    					
	    				$isAuthenticated = false;
	    			}
    			}
    			
    			if($isAuthenticated) {
    				
					//Redirect to notification page.
					Application_Model_ForgotPasswordProcessManager::getInstance()
						->setStepComplete(Application_Model_ForgotPasswordProcessManager::STEP2);
    				
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
    			
    			//If here then the user failed to enter the correct authentication details.
    			$form->addErrorMessage('Incorrect answer provided.');
    		}
    		else {
    			
    			//Zend_Form to redisplay with the user-content automatically injected
    			//and the error messages automatically rendered.
    		}
    	}
    	else {
    		
    		//Blank Zend_Form to automatically display.
    	}
    	
    	Application_Model_ForgotPasswordProcessManager::getInstance()->incrementSecurityQuestionAttempt();
    	
        //Display one of the canned questions, and a text box for the answer
    	//Display the user question and a text box for the answer
    	$userMapper = new Application_Model_UserMapper();
    	$user = $userMapper->getUserByEmail($session->email);

    	$mapper = new Application_Model_CannedQuestionMapper();
    	$authentication = $mapper->getQAAuthentication($user);
    	$this->view->canned_question = $authentication->getQuestion();
    	
    	$mapper = new Application_Model_UserCreatedQuestionMapper();
    	$authentication = $mapper->getQAAuthentication($user);
    	$this->view->user_created_question = $authentication->getQuestion();
    	
    	$this->view->form = $form;
    }

    /**
     * @todo
     * Generate a random password.
     */
    public function confirmNotificationAction()
    {
    	//Retrieve the email address then clear the session.
    	$session = new Zend_Session_Namespace('forgot_password_container');
    	$email = $session->email;
    	
    	Application_Model_ForgotPasswordProcessManager::getInstance()
    		->setStepComplete(Application_Model_ForgotPasswordProcessManager::STEP3);
    	
		Application_Model_ForgotPasswordProcessManager::getInstance()->resetProcess();
    	
    	//Change the password to a random password.
    	$mapper = new Application_Model_UserMapper();
    	$newPassword = $mapper->updatePasswordWithRandom($email);
    	
    	//Email password to user.  
    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/parameters.ini');
    	$mail = new Zend_Mail();
    	$mail->setSubject('Password reset');
    	$mail->setFrom("noreply@{$config->system->url}", 'System');
    	$mail->addTo($email);
    	$mail->setBodyHtml("<p>Your new toiltracker password: $newPassword</p>");
    	$mail->send();
    }


}
