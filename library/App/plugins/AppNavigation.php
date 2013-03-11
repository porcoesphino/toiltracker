<?php

class App_Plugins_AppNavigation extends Zend_Controller_Plugin_Abstract {
		
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
	
		if($request->getModuleName() != 'App') {
			
			return;
		}
		
		$this->_configureGatewayLinks();
		$this->_configureMainLinks();
		$this->_configureSubLinks($request);
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
		Zend_Registry::set('app_gateway_nav', $container);
	}
	
	protected function _configureMainLinks() {
	
		$pages = array();
		if(Zend_Auth::getInstance()->hasIdentity()) {
		
			$pages[] = new Zend_Navigation_Page_Mvc(array(
				'label' => 'Employee summary',
				'module' => 'App',
				'controller' => 'Employee',
				'action' => 'index',
				'route' => 'module_partial_path'
			));
			
			$pages[] = new Zend_Navigation_Page_Mvc(array(
				'label' => 'Settings',
				'module' => 'App',
				'controller' => 'ChangePassword',
				'action' => 'change-password',
				'route' => 'module_full_path'
			));
		}
		
		$pages[] = new Zend_Navigation_Page_Mvc(array(
			'label' => 'Website',
			'module' => 'default',
			'controller' => 'Index',
			'action' => 'index',
			'route' => 'root'
		));
	
		$container = new Zend_Navigation();
		$container->addPages($pages);
		Zend_Registry::set('app_main_nav', $container);
	}
	
	protected function _configureSubLinks($request) {
	
		if(!Zend_Auth::getInstance()->hasIdentity()) {
	
			return;
		}
	
		$employeeId = $request->getParam('employeeid');
		$toilId = $request->getParam('id');
	
		if($request->getActionName() == 'index') {
				
			if($request->getControllerName() == 'Employee') {
					
				$this->_configureEmployeeSubLinks();
			}
			else if($request->getControllerName() == 'Toil') {
					
				$this->_configureToilSubLinks($employeeId, $toilId);
			}
		}
	}
	
	protected function _configureEmployeeSubLinks() {
		
		$pages = array();
		$pages[] = new Zend_Navigation_Page_Mvc(array(
			'label' => 'Add employee',
			'module' => 'App',
			'controller' => 'Employee',
			'action' => 'post',
			'route' => 'module_full_path',
		));
		
		$container = new Zend_Navigation();
		$container->addPages($pages);
		Zend_Registry::set('app_sub_nav', $container);
	}
	
	protected function _configureToilSubLinks($employeeId, $toilId) {
		
		$pages = array();
		$pages[] = new Zend_Navigation_Page_Mvc(array(
			'label' => 'Record toil accrued',
			'module' => 'App',
			'controller' => 'Toil',
			'action' => 'post',
			'params' => array(
				'employeeid' => $employeeId,
				'toilaction' => 'accrue'
			),
			'route' => 'module_full_path_employeeid_action'
		));
		
		$pages[] = new Zend_Navigation_Page_Mvc(array(
			'label' => 'Record toil used',
			'module' => 'App',
			'controller' => 'Toil',
			'action' => 'post',
			'params' => array(
				'employeeid' => $employeeId,
				'toilaction' => 'use'
			),
			'route' => 'module_full_path_employeeid_action'
		));
	
		$container = new Zend_Navigation();
		$container->addPages($pages);
		Zend_Registry::set('app_sub_nav', $container);
	}
}