<?php

class App_RegisterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    /**
     * @todo
     * Allow more non-alpha numbers on the security Q/As
     */
    public function indexAction()
    {
        // action body
        $form = new App_Form_Register();
        $request = $this->getRequest();
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		//Encrypt and store data.
        		$formValues = $form->getValues();
        		$userMapper = new Application_Model_UserMapper();
        		$userMapper->insert($formValues);
        		
        		//Login
        		$formValues['role'] = Application_Model_User::MASTER;
        		$user = new Application_Model_User($formValues);
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
        		
        		//Zend form auto-redisplays with error messages and user
        		//injected content.
        	}
        }
        else {
        	
        	//Zend form to display automatically with empty fields.
        }
        $this->view->form = $form;
    }

    public function confirmRegistrationAction()
    {
        // action body
    }


}



