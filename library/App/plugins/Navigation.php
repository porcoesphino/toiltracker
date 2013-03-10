<?php

class App_Plugins_DefaultNavigation extends Zend_Controller_Plugin_Abstract {
		
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
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
	
		if($request->getModuleName() == 'default') {
		
			//Configure the gateway navigation facility.
			$this->_configureGatewayLinks();
			$this->_configureWebisiteLinks();
		}
		
		
		/*
		if($request->getModuleName() == 'default') {
				
			$this->_configureWebsiteLinks($request);
		}
		else if($request->getModuleName() == 'App') {
				
			if(($request->getControllerName() == 'Login')
				|| ($request->getControllerName() == 'Register')
				|| ($request->getControllerName() == 'ForgotPassword')) {
				
				//User is still external to the application, so show the website links.
				$this->_configureWebsiteLinks($request);
			}
			else if($request->getControllerName() == 'Employee') {
					
				$this->_configureEmployeeLinks($request);
			}
			else if($request->getControllerName() == 'Toil') {
	
				$this->_configureToilLinks($request);
			}
			else if($request->getControllerName() == 'ChangePassword') {
				
				$this->_configureChangePasswordLinks($request);
			}
		}
		*/
	}
	
	protected function _configureWebisiteLinks() {
	
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
	
	/*
	protected function _configureWebsiteLinks(Zend_Controller_Request_Abstract $request) {
		
		//Add the standard links (home/news/about etc)
		$this->_addDefaultLinks();
		
		//Add auth-dependent links
		if(Zend_Auth::getInstance()->hasIdentity()) {
		
			$this->_addGatewayLink('app_home');
			$this->_addGatewayLink('logout');
		}
		else {
		
			$this->_addGatewayLink('login');
			$this->_addGatewayLink('register');
			$this->_addGatewayLink('forgot_password');
		}
	}
	
	public function _configureEmployeeLinks(Zend_Controller_Request_Abstract $request) {
		
		switch($request->getActionName()) {
				
			case 'index':
				$this->_addEmployeeLink('add'); 
				break;
		
			case 'post':
				$this->_addEmployeeLink('summary');
				break;
		
			case 'put':
				$this->_addEmployeeLink('summary');
				break;
		}
		
		$this->_addGatewayLink('change_password');
		$this->_addGatewayLink('logout');
	}
	
	public function _configureToilLinks(Zend_Controller_Request_Abstract $request) {
		
		switch($request->getActionName()) {
			
			case 'index':
				$this->_addEmployeeLink('summary');
				$this->_addToilLink('post', $request->getParam('employeeid'), 'accrue');
				$this->_addToilLink('post', $request->getParam('employeeid'), 'use');
				break;
		
			case 'post':
				$this->_addToilLink('summary', $request->getParam('employeeid'));
				break;
		
			case 'put':
				//Calculate the employeeid from the toil id.
				$mapper = new Application_Model_ToilMapper();
				$employeeId = $mapper->getEmployeeIdFromToilId($request->getParam('id'));
				$this->_addToilLink('summary', $employeeId);
				break;
		}
		
		$this->_addGatewayLink('change_password');
		$this->_addGatewayLink('logout');
	}
	
	public function _configureChangePasswordLinks(Zend_Controller_Request_Abstract $request) {
	
		$this->_addEmployeeLink('summary');
		$this->_addGatewayLink('change_password');
		$this->_addGatewayLink('logout');
	}
	
	
	
	protected function _addGatewayLink($linkName) {
		
		switch($linkName) {
			
			case 'login':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Login',
					'module' => 'App',
					'controller' => 'Login',
					'action' => 'index',
					'route' => 'module_partial_path'
				));
				$this->_container->addPage($page);
				break;
				
			case 'app_home':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'App Home',
					'module' => 'App',
					'controller' => 'Employee',
					'action' => 'index',
					'route' => 'module_partial_path'
				));
				$this->_container->addPage($page);
				break;
				
			case 'logout':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Logout',
					'module' => 'App',
					'controller' => 'Login',
					'action' => 'logout',
					'route' => 'module_full_path'
				));
				$this->_container->addPage($page);
				break;
				
			case 'register':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Register',
					'module' => 'App',
					'controller' => 'Register',
					'action' => 'index',
					'route' => 'module_partial_path'
				));
				$this->_container->addPage($page);
				break;
			
			case 'change_password':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Change password',
					'module' => 'App',
					'controller' => 'ChangePassword',
					'action' => 'change-password',
					'route' => 'module_full_path'
				));
				$this->_container->addPage($page);
				break;
				
			case 'forgot_password':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Forgot password',
					'module' => 'App',
					'controller' => 'ForgotPassword',
					'action' => 'confirm-email',
					'route' => 'module_full_path'
				));
				$this->_container->addPage($page);
				break;
		}
	}
	
	protected function _addToilLink($linkName, $employeeId, $toilAction = null) {
	
		switch($linkName) {
				
			case 'summary':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Toil summary',
					'module' => 'App',
					'controller' => 'Toil',
					'action' => 'index',
					'params' => array(
						'employeeid' => $employeeId
					),
					'route' => 'module_full_path_employeeid'
				));
				$this->_container->addPage($page);
				break;
				
			case 'post':
				
				if($toilAction == 'accrue') { 	
					$label = 'Record toil accrued'; 
				}
				else { 
					$label = 'Record toil used'; 
				}
				
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => $label,
					'module' => 'App',
					'controller' => 'Toil',
					'action' => 'post',
					'params' => array(
						'employeeid' => $employeeId,
						'toilaction' => $toilAction
					),
					'route' => 'module_full_path_employeeid_action'
				));
				$this->_container->addPage($page);
				break;
				
			case 'put':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Record toil taken',
					'module' => 'App',
					'controller' => 'Toil',
					'action' => 'put',
					'params' => array(
						'employeeid' => $employeeId
					),
					'route' => 'module_full_path',
				));
				$this->_container->addPage($page);
				break;
		}
	}
	
	protected function _addEmployeeLink($linkName) {
		
		switch($linkName) {
			
			case 'summary':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Employee summary',
					'module' => 'App',
					'controller' => 'Employee',
					'action' => 'index',
					'route' => 'module_partial_path'
				));
				$this->_container->addPage($page);
				break;
				
			case 'cancel':
					$page = new Zend_Navigation_Page_Mvc(array(
						'label' => 'Cancel',
						'module' => 'App',
						'controller' => 'Employee',
						'action' => 'index',
						'route' => 'module_partial_path'
					));
					$this->_container->addPage($page);
					break;
					
			case 'add':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Add employee',
					'module' => 'App',
					'controller' => 'Employee',
					'action' => 'post',
					'route' => 'module_full_path',
				));
				$this->_container->addPage($page);
				break;
		}
	}
	*/
}