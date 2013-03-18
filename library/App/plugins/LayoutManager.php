<?php

class App_Plugins_LayoutManager extends Zend_Controller_Plugin_Abstract {
		
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
			
		if($request->getModuleName() == 'default') {
			
			if(($request->getControllerName() == 'Index') && ($request->getActionName() == 'terms')) {
				
				Zend_Layout::getMvcInstance()->setLayout('popup');
			}
			else {
				
				Zend_Layout::getMvcInstance()->setLayout('default');
			}
		}
		else {
			
			Zend_Layout::getMvcInstance()->setLayout('app');
		}
	}
}