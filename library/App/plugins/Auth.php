<?php

class App_Plugins_Auth extends Zend_Controller_Plugin_Abstract {
	
	/**
	 * Ensure that the current user has access only to the resources they are
	 * authenticated for.
	 * 
	 * @see Zend_Controller_Plugin_Abstract::preDispatch()
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		if($request->getModuleName() == 'App') {
			
			if(Zend_Auth::getInstance()->hasIdentity()) {
				
				return;
			}
			
			//User is not logged in, however, they are still allowed to access the 'login' and
			//'register' resources.
			if(($request->getControllerName() == 'Login') || ($request->getControllerName() == 'Register')) {

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