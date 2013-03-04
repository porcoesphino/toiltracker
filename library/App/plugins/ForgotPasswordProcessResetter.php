<?php

class App_Plugins_ForgotPasswordProcessResetter extends Zend_Controller_Plugin_Abstract {
	
	/**
	 * Ensure that the forgot-password process is carefully managed, being reset if the
	 * user leaves the process, or disabled if the user is logged in.
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		//The only action that is not reset is when the user accesses the forgot-password
		//reset process.
		if($request->getModuleName() == 'default') {
		
			Application_Model_ForgotPasswordProcessManager::resetProcess();
		}
		if($request->getModuleName() == 'App') {
			
			if(Zend_Auth::getInstance()->hasIdentity()) {
			
				Application_Model_ForgotPasswordProcessManager::resetProcess();
				return;
			}
			
			if($request->getControllerName() == 'Login') {
				
				Application_Model_ForgotPasswordProcessManager::resetProcess();
				return;
			}
			
			if($request->getControllerName() == 'Register') {
			
				Application_Model_ForgotPasswordProcessManager::resetProcess();
				return;
			}
		}
	}
}