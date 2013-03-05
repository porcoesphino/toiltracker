<?php

class App_RegisterController extends Zend_Controller_Action
{

    public function init()
    {
        //If user is logged in then they should not access any actions in this controller.        	
        if(Zend_Auth::getInstance()->hasIdentity()) {
        		
        	$redirector = $this->_helper->getHelper('Redirector');
        	$redirector->gotoRoute(
	        	array(
	        		'action' => 'index',
	        		'controller' => 'Employee',
	        		'module' => 'App'
	        	),
        		'module_partial_path',
        		true
        	);
        }
    }

    /**
     * @todo
     * Allow more non-alpha numbers on the security Q/As
     */
    public function indexAction()
    {
        $form = new App_Form_Register();
        $request = $this->getRequest();
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		//Test for duplicate emails.
        		$formValues = $form->getValues();
        		$userMapper = new Application_Model_UserMapper();
        		if(!$userMapper->isEmailInDatabase($formValues['email'])) {
        			
        			$teamMapper = new Application_Model_TeamMapper();
        			$team = $teamMapper->insert($formValues);
        			$user = $userMapper->insert($team->getId(), $formValues);
        			
        			//Login
        			$auth = Zend_Auth::getInstance();
        			$auth->getStorage()->write($user);
        			
        			//Redirect to App/Home
        			$helper = $this->_helper->getHelper('Redirector');
        			$helper->gotoRoute(
        					array(
        							'action' => 'index',
        							'controller' => 'Employee',
        							'module' => 'App',
        					),
        					'module_partial_path',
        					true
        			);
        		}
        		else {
        			//Attach custom error message to the form advising user that the
        			//email already exists in the database.
        			$form->addErrorMessage('Email already exists in the system');
        		}
        	}
        	else {
        		
        		//Zend form auto-redisplays with error messages and user
        		//injected content.
        	}
        }
        else {
        	
        	//Zend form to display automatically with empty fields.
        }
        $this->view->form = $form;
    }

    public function confirmRegistrationAction() {
    }


}



