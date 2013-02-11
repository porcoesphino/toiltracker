<?php

class App_Plugins_Navigation extends Zend_Controller_Plugin_Abstract {
	
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		
		$container = new Zend_Navigation();
		
		if(preg_match("/default|gateway/i", $request->getModuleName())) {
			
			//Add the standard links (home/news/about etc)
			$container->addPages($this->_addDefaultLinks());
			
			if(Zend_Auth::getInstance()->hasIdentity()) {
				
				$container->addPage($this->_addGenericLink('app_home'));
				$container->addPage($this->_addGenericLink('logout'));
			}
			else {
				
				$container->addPage($this->_addGenericLink('login'));
				$container->addPage($this->_addGenericLink('register'));
			}
		}
		else if($request->getModuleName() == 'App') {
			
			if($request->getControllerName() == 'Employee') {
			
				switch($request->getActionName()) {
					
					case 'index':
						$container->addPage($this->_addEmployeeLink('add'));
						break;
						
					case 'post':
						$container->addPage($this->_addEmployeeLink('summary'));
						break;
						
					case 'put':
						$container->addPage($this->_addEmployeeLink('summary'));
						break;
				}
			}
			else if($request->getControllerName() == 'Toil') {
				
				switch($request->getActionName()) {
					case 'index':						
						$container->addPage($this->_addEmployeeLink('summary'));
						$container->addPage($this->_addToilLink('post', $request->getParam('employeeid'), 'accrue'));
						$container->addPage($this->_addToilLink('post', $request->getParam('employeeid'), 'use'));
						break;
						
					case 'post':
						$container->addPage($this->_addToilLink('summary', $request->getParam('employeeid')));
						break;
						
					case 'put':
						//Calculate the employeeid from the toil id.
						$mapper = new Application_Model_ToilMapper();
						$employeeId = $mapper->getEmployeeIdFromToilId($request->getParam('id'));
						$container->addPage($this->_addToilLink('summary', $employeeId));
						break;
				}
			}
			$container->addPage($this->_addGenericLink('change_password'));
			$container->addPage($this->_addGenericLink('logout'));
		}
		
		Zend_Layout::getMvcInstance()->getView()->getHelper('Navigation')->navigation($container);
	}
	
	protected function _addDefaultLinks() {
		
		$pages = array();
		$pages[] = new Zend_Navigation_Page_Mvc(array(
			'label' => 'Home',
			'module' => 'default',
			'controller' => 'Index',
			'action' => 'index',
			'route' => 'root'
		));
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
		return $pages;
	}
	
	protected function _addGenericLink($linkName) {
		
		$page = null;
		switch($linkName) {
			
			case 'login':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Login',
					'module' => 'Gateway',
					'controller' => 'Login',
					'action' => 'index',
					'route' => 'module_partial_path'
				));
				break;
				
			case 'app_home':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'App Home',
					'module' => 'App',
					'controller' => 'Employee',
					'action' => 'index',
					'route' => 'module_partial_path'
				));
				break;
				
			case 'logout':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Logout',
					'module' => 'Gateway',
					'controller' => 'Login',
					'action' => 'logout',
					'route' => 'module_full_path'
				));
				break;
				
			case 'register':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Register',
					'module' => 'Gateway',
					'controller' => 'Register',
					'action' => 'index',
					'route' => 'module_partial_path'
				));
				break;
			
			case 'change_password':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Change password',
					'module' => 'Gateway',
					'controller' => 'Login',
					'action' => 'change-password',
					'route' => 'module_full_path'
				));
				break;
		}
		return $page;
	}
	
	protected function _addToilLink($linkName, $employeeId, $toilAction = null) {
	
		$page = null;
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
				break;
				
			case 'post':
				
				if($toilAction == 'accrue') { $label = 'Record toil accrued'; }
				else { $label = 'Record toil used'; }
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
				break;
		}
		return $page;
	}
	
	protected function _addEmployeeLink($linkName) {
		
		$page = null;
		switch($linkName) {
			
			case 'summary':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Employee summary',
					'module' => 'App',
					'controller' => 'Employee',
					'action' => 'index',
					'route' => 'module_partial_path'
				));
				break;
				
			case 'cancel':
					$page = new Zend_Navigation_Page_Mvc(array(
						'label' => 'Cancel',
						'module' => 'App',
						'controller' => 'Employee',
						'action' => 'index',
						'route' => 'module_partial_path'
					));
					break;
					
			case 'add':
				$page = new Zend_Navigation_Page_Mvc(array(
					'label' => 'Add employee',
					'module' => 'App',
					'controller' => 'Employee',
					'action' => 'post',
					'route' => 'module_full_path',
				));
				break;
		}
		return $page;
	}
}