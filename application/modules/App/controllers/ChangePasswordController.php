<?php

class App_ChangePasswordController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function changePasswordAction()
    {
        $form = new App_Form_ChangePassword();
        $request = $this->getRequest();
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		//Check to ensure that the password is correct.
        		$userData = $form->getValues();
        		
        		$auth = Zend_Auth::getInstance();
        		$user = $auth->getStorage()->read();
        		$email = $user->getEmail();
        		
        		$mapper = new Application_Model_UserMapper();
        		$authAdapter = new Zend_Auth_Adapter_DbTable($mapper->getDbAdapter());
        		$authAdapter
	        		->setTableName('users')
	        		->setIdentityColumn('email')
	        		->setIdentity($email)
	        		->setCredentialColumn('password')
	        		->setCredentialTreatment('SHA2(CONCAT(?, salt), 256)')
	        		->setCredential($userData['current_password']);
        		
        		$result = $auth->authenticate($authAdapter);
        		if($result->isValid()) {
        			
        			//Re-identify the user to persist the session.
        			$auth->getStorage()->write($user);
        			$mapper->updatePassword($userData['new_password'], $email);
        			$redirect = $this->_helper->getHelper('Redirector');
        			$redirect->gotoRoute(
        				array(
        					'action' => 'password-changed',
        					'controller' => 'ChangePassword',
        					'module' => 'App'
        				),
        				'module_full_path',
        				true	
        			);
        		}
        		else {
        			
        			//Password is invalid. Re-identify the user to persist the session.
        			$auth->getStorage()->write($user);
        			$form->getElement('current_password')->addError('Password is incorrect');
        		}
        	}
        	else {
        		
        		//Zend_Form will display the form pre-populated with
        		//user-provided content, and with error messages.
        	}
        }
        else {
        	
        	//Zend_Form will display a blank form.
        }
        $this->view->form = $form;
    }

    public function passwordChangedAction()
    {
    	//Display on successful password change
    }


}





