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
     */
    public function confirmEmailAction()
    {
        // action body
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
        			$session = new Zend_Session_Namespace('forgot_password');
        			$session->confirmEmail = true;
        			$session->securityQuestionAttempt = 1;
        			
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
        	}
        }
        $this->view->form = $form;
    }

    public function confirmQuestionsAction()
    {
        // action body
    }


}





