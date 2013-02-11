<?php

class App_Plugins_Auth extends Zend_Controller_Plugin_Abstract {
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		if($request->getModuleName() == 'App') {
			
			if(!Zend_Auth::getInstance()->hasIdentity()) {
				
				$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
				$redirector->gotoSimple('index', 'Login', 'Gateway');
			}
		}
	}
}