<?php

class App_Plugins_DefaultNavigation extends Zend_Controller_Plugin_Abstract {
		
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
	
		if($request->getModuleName() != 'default') {
			
			return;
		}

		$this->_configureGatewayLinks();
		$this->_configureMainLinks();
	}
	
	protected function _configureGatewayLinks() {
		
		$container = new Zend_Navigation();
		if(Zend_Auth::getInstance()->hasIdentity()) {
			
			$container->addPage(new Zend_Navigation_Page_Mvc(array(
				'label' => 'Logout',
				'module' => 'App',
				'controller' => 'Login',
				'action' => 'logout',
				'route' => 'module_full_path'
			)));
		}
		else {
			
			$container->addPage(new Zend_Navigation_Page_Mvc(array(
				'label' => 'Login',
				'module' => 'App',
				'controller' => 'Login',
				'action' => 'index',
				'route' => 'module_partial_path'
			)));
			$container->addPage(new Zend_Navigation_Page_Mvc(array(
				'label' => 'Register',
				'module' => 'App',
				'controller' => 'Register',
				'action' => 'index',
				'route' => 'module_partial_path'
			)));
		}
		Zend_Registry::set('default_gateway_nav', $container);
	}
	
	protected function _configureMainLinks() {
	
		$pages = array();
		$pages[] = new Zend_Navigation_Page_Mvc(array(
			'label' => 'Home',
			'module' => 'default',
			'controller' => 'Index',
			'action' => 'index',
			'route' => 'root'
		));
		
		if(Zend_Auth::getInstance()->hasIdentity()) {
			
			$pages[] = new Zend_Navigation_Page_Mvc(array(
				'label' => 'App Home',
				'module' => 'App',
				'controller' => 'Employee',
				'action' => 'index',
				'route' => 'module_partial_path'
			));
		}
	
		$pages[] = new Zend_Navigation_Page_Mvc(array(
			'label' => 'About',
			'module' => 'default',
			'controller' => 'Index',
			'action' => 'about',
			'route' => 'default'	
		));
	
		$pages[] = new Zend_Navigation_Page_Mvc(array(
			'label' => 'Contact',
			'module' => 'default',
			'controller' => 'Index',
			'action' => 'contact',
			'route' => 'default'
		));
	
		$container = new Zend_Navigation();
		$container->addPages($pages);
		Zend_Registry::set('default_main_nav', $container);
	}
}