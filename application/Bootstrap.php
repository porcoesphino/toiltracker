<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initErrorLogging() {
		
		if(APPLICATION_ENV != 'production') {
			
			ini_set('html_errors', true);
			ini_set('display_errors', true);
		}
	}
	
	protected function _initRoutes() {
		
		$router = Zend_Controller_Front::getInstance()->getRouter();
		
		$route = new Zend_Controller_Router_Route(
			':module/:controller/:action/id/:id',
			array(
				'module' => ':module',
				'controller' => ':controller',
				'action' => ':action',
				'id' => ':id'
			)
		);
		$router->addRoute('module_full_path_id', $route);
		
		$route = new Zend_Controller_Router_Route(
			':module/:controller/:action/employeeid/:employeeid',
			array(
				'module' => ':module',
				'controller' => ':controller',
				'action' => ':action',
				'employeeid' => ':employeeid'
			)
		);
		$router->addRoute('module_full_path_employeeid', $route);
		
		$route = new Zend_Controller_Router_Route(
			':module/:controller/:action/employeeid/:employeeid/toilaction/:toilaction',
			array(
				'module' => ':module',
				'controller' => ':controller',
				'action' => ':action',
				'employeeid' => ':employeeid',
				'toilaction' => ':toilaction'
			)
		);
		$router->addRoute('module_full_path_employeeid_action', $route);
		
		$route = new Zend_Controller_Router_Route(
			':module/:controller/:action/id/:id/employeeid/:employeeid',
			array(
				'module' => ':module',
				'controller' => ':controller',
				'action' => ':action',
				'id' => ':id',
				'employeeid' => ':employeeid'
			)
		);
		$router->addRoute('module_full_path_id_employeeid', $route);
		
		$route = new Zend_Controller_Router_Route(
			':module/:controller/:action',
			array(
				'module' => ':module',
				'controller' => ':controller',
				'action' => ':action'
			)
		);
		$router->addRoute('module_full_path', $route);
		
		$route = new Zend_Controller_Router_Route(
			':module/:controller',
			array(
				'module' => ':module',
				'controller' => ':controller',
				'action' => 'index'
			)
		);
		$router->addRoute('module_partial_path', $route);
		
		$route = new Zend_Controller_Router_Route(
			':action',
			array(
				'module' => 'default',
				'controller' => 'Index',
				'action' => ':action'
			)
		);
		$router->addRoute('default', $route);
		
		$route = new Zend_Controller_Router_Route(
			'/',
			array(
				'module' => 'default',
				'controller' => 'Index',
				'action' => 'index'
			)
		);
		$router->addRoute('root', $route);
	}
	
	protected function _initActionHelpers() {
	
		$resource = new Zend_Loader_Autoloader_Resource(array('basePath' => APPLICATION_PATH, 'namespace' => ''));
		$resource->addResourceType('helper', 'modules/App/controllers/helpers/', 'App_Controllers_Helpers_');
	
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->pushAutoloader($resource);
	
		Zend_Controller_Action_HelperBroker::addHelper(new App_Controllers_Helpers_Employee());
		Zend_Controller_Action_HelperBroker::addHelper(new App_Controllers_Helpers_Toil());
	}
	
	protected function _initPlugins() {
	
		$resource = new Zend_Loader_Autoloader_Resource(array('basePath' => APPLICATION_PATH, 'namespace' => ''));
		$resource->addResourceType('plugin', '../library/App/plugins/', 'App_Plugins_');
	
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->pushAutoloader($resource);
	
		Zend_Controller_Front::getInstance()->registerPlugin(new App_Plugins_Navigation());
		Zend_Controller_Front::getInstance()->registerPlugin(new App_Plugins_Auth());
	}
	
	
	protected function _initValidators() {
		
		$resource = new Zend_Loader_Autoloader_Resource(array('basePath' => APPLICATION_PATH, 'namespace' => ''));
		$resource->addResourceType('validator', 'forms/validators/', 'Application_Form_Validators_');
		Zend_Loader_Autoloader::getInstance()->pushAutoloader($resource);
	}
	
	protected function _initViewHelpers() {
		
		$view = $this->bootstrap('Layout')->getResource('Layout')->getView();
		$view->addHelperPath(APPLICATION_PATH . '/views/helpers/', 'Application_Views_Helpers');
	}
}

