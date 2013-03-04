<?php

class App_Plugins_Auth extends Zend_Controller_Plugin_Abstract {
	
	/**
	 * Ensure that the current user has access only to the resources they are
	 * authenticated for. High-level checking only. Individual resources should
	 * apply more fine-grained control over their access.
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		if($request->getModuleName() == 'default') {
		
			return;
		}
		
		if($request->getModuleName() == 'App') {
			
			if(Zend_Auth::getInstance()->hasIdentity()) {
			
				return;
			}
			
			//User is not logged in, however, they are still allowed to some App resources.
			if($request->getControllerName() == 'Login') {
				
				return;
			}
			
			if($request->getControllerName() == 'Register') {
			
				return;
			}
			
			if($request->getControllerName() == 'ForgotPassword') {
					
				return;
			}
			
			//User is not allowed access to the resource. Redirect user to the login.
			$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
			$redirector->gotoRoute(
				array(
					'action' => 'index', 
					'controller' => 'Login', 
					'module' => 'App'
				),
				'module_full_path',
				true
			);
		}
	}
}