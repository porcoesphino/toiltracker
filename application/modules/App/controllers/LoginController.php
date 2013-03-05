<?php

class App_LoginController extends Zend_Controller_Action {

    public function init() {
    	
        //If action is index and user is not logged in, allow
        if($this->getRequest()->getActionName() == 'index') {
        	
        	if(Zend_Auth::getInstance()->hasIdentity()) {
        		
        		$session = new Zend_Session_Namespace('already_identified');
        		$session->isAuthorisedToShowAlreadyIdentified = true;
        		$redirector = $this->_helper->getHelper('Redirector');
        		$redirector->gotoRoute(
        			array(
        				'action' => 'already-identified',
        				'controller' => 'Login',
        				'module' => 'App'
        			),
        			'module_full_path',
        			true
        		);
        	}
        	
        	//If here then user is not logged in and has tried to access the login page.
        	//This is permissable.
        	return;
        }
        
    	
        //If action is log-out, clear session and logout, regardless of whether the user
    	//is logged in or not.
    	if($this->getRequest()->getActionName() == 'logout') {
    		
	        Zend_Auth::getInstance()->clearIdentity();
	        $redirector = $this->_helper->getHelper('Redirector');
	        $redirector->gotoRoute(
	        	array(
	        		'action'=> 'index',
	        		'controller' => 'Login',
	        		'module' => 'App'
	        	),
	        	'module_partial_path',
	        	true
	        );
    	}
        

    	if($this->getRequest()->getActionName() == 'already-identified') {
    	
    		$session = new Zend_Session_Namespace('already_identified');
    		$isAuthorisedToShowAlreadyIdentified = $session->isAuthorisedToShowAlreadyIdentified;
    		Zend_Session::namespaceUnset('already_identified');
    		
    		if(Zend_Auth::getInstance()->hasIdentity()) {
    			
    			if(empty($isAuthorisedToShowAlreadyIdentified)) {
    				
    				//Logged in but not previously authorised to see this page (i.e. the user
    				//has tried to access this page directly rather than indirectly through the
    				//Login page when already logged in. Send the user to the App Home rather
    				//than error page, as they may just have refreshed the screen.
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
    		else {
    			
    			//Not logged in so should not see this page. Redirect to login.
    			$redirector = $this->_helper->getHelper('Redirector');
		        $redirector->gotoRoute(
		        	array(
		        		'action'=> 'index',
		        		'controller' => 'Login',
		        		'module' => 'App'
		        	),
		        	'module_partial_path',
		        	true
		        );
    		}
    	}
    }

    /**
     * @todo
     * Track number of login attempts and lock account after several attempts. Possibly
     * block IP for a while.
     * 
     * @todo
     * Add security certificate
     */
    public function indexAction() {
        
        $form = new App_Form_Login();
		$request = $this->getRequest();
		if($request->isPost()) {
			if($form->isValid($request->getPost())) {
				
				$userData = $form->getValues();
				$auth = Zend_Auth::getInstance();
				$mapper = new Application_Model_UserMapper();
				$authAdapter = new Zend_Auth_Adapter_DbTable($mapper->getDbAdapter());
				$authAdapter
					->setTableName('users')
					->setIdentityColumn('email')
					->setIdentity($userData['email'])
					->setCredentialColumn('password')
					->setCredentialTreatment('SHA2(CONCAT(?, salt), 256)')
					->setCredential($userData['password']);
				
				$result = $auth->authenticate($authAdapter);
				if($result->isValid()) {
				
					$stdClass = $authAdapter->getResultRowObject();
					$user = new Application_Model_User($stdClass);
					$storage = $auth->getStorage();
					$storage->write($user);
										
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
				else {
					//Display custom error message to the user
					$form->addErrorMessage('Invalid credentials');
				}
			}
			else {
				//Zend_Form will display the form with errors and user
				//content injected into the elements.
			}
		}
		else {
			//Zend_Form will display a fresh form.
		}
		
        $this->view->form = $form;
    }

    public function alreadyIdentifiedAction() {
    }

    /**
     * @todo
     * Delete the corresponding view to this action, as it is not used.
     */
    public function logoutAction() {
    }
}











