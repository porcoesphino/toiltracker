<?php

class App_EmployeeController extends Zend_Controller_Action
{

    public function init()
    {
        //If the action is a put or delete action, then it relies on the employee id
        //of the employee being edited or deleted. Check here to ensure that the employee
        //id exists and belongs to the team managed by the user. If not, then simply display the
        //index page.
        $action = $this->getRequest()->getActionName();
        if(($action == 'put') || ($action == 'delete')) {
        	
        	$forceRedirect = false;
        	$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
	        $employeeId = $this->getRequest()->getParam('id');
	        
	        if(empty($employeeId)) {
	        	
	        	$forceRedirect = true;
	        }
	        else {
	        	
	        	$user = Zend_Auth::getInstance()->getStorage()->read();
	        	if(!$employeeHelper->isValidEmployee($employeeId, $user)) {
	        		
	        		$forceRedirect = true;
	        	}
	        }
	        
	        if($forceRedirect) {
	        		        	
	        	$redirector = $this->_helper->getHelper('Redirector');
	        	$redirector->gotoRoute(
	        		array(
	        			'action' => 'index',
	        			'controller' => 'Employee',
	        			'module' => 'App'
	        		),
	        		'module_partial_path',
	        		true
	        	);
	        }
        }
    }
    
    public function emptyTeamAction() {
    	
    }

    public function indexAction()
    {
    	$user = Zend_Auth::getInstance()->getStorage()->read();
    	$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
        $employeeArray = $employeeHelper->fetchSummaries($user->getTeamId());
        if(empty($employeeArray)) {

        	$redirector = $this->_helper->getHelper('Redirector');
        	$redirector->gotoRoute(
        		array(
        			'action' => 'empty-team',
        			'controller' => 'Employee',
        			'module' => 'App'
        		),
        		'module_full_path',
        		true
        	);
        }
        
        $this->view->employees = $employeeArray;
    }

    public function postAction()
    {
        // action body    	 
        $form = new App_Form_Employee();
        $form->setMethod(Zend_Form::METHOD_POST);
        $form->setAction('/App/Employee/post/');
    	$form->getElement('save')->setLabel('Add');
        
        $request = $this->getRequest();
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		//Write to the database.
        		$user = Zend_Auth::getInstance()->getStorage()->read();
        		$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
        		$employeeHelper->post($user->getTeamId(), $form->getValues());
        		
        		$redirector = $this->_helper->getHelper('Redirector');
        		$redirector->gotoRoute(
        			array(
        				'action' => 'index',
        				'controller' => 'Employee',
        				'module' => 'App'
        			),
        			'module_partial_path',
        			true
        		);
          	}
        	else {
        		
        		//Zend to auto display form errors and inject user-specified values.
        	}
        }
        else {
        	
        	//Zend to auto display an empty form.
        }
        $this->view->form = $form;
    }

    public function putAction()
    {
        //If no args, then redirect to index, requesting a 'put' selection.
    	$request = $this->getRequest();
    	
    	$form = new App_Form_Employee();
    	$form->setMethod(Zend_Form::METHOD_POST);
    	$form->setAction('/App/Employee/put/id/' . $request->getParam('id'));
    	$form->getElement('save')->setLabel('Update');
    	
        if($request->isPost()) {
        	
        	//Process the form.
        	if($form->isValid($request->getPost())) {
        		
        		$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
        		$employeeHelper->put($form->getValues());
        		
        		$redirector = $this->_helper->getHelper('Redirector');
				$redirector->gotoRoute(
					array(
						'action' => 'index',
						'controller' => 'Employee',
						'module' => 'App'
					),
					'module_partial_path',
					true
				);
        	}
        	else {
        		
        		//Zend_Form to auto display issues with the form, and to inject
        		//user submitted content.
        	}
        }
        else {
        	
        	//Display the form populated with the values for the current employee.
        	$employeeId = $this->getRequest()->getParam('id');
        	$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
        	
        	$user = Zend_Auth::getInstance()->getStorage()->read();
        	$values = $employeeHelper->get($employeeId, $user);
        	$form->populate($values);
        }
        $this->view->form = $form;
    }

    public function deleteAction()
    {
        // action body
        $id = $this->getRequest()->getParam('id');
        $employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
        $employeeHelper->delete($id);
        
        $redirector = $this->_helper->getHelper('Redirector');
        $redirector->gotoRoute(
        	array(
        		'action' => 'index', 
        		'controller' => 'Employee', 
        		'module' => 'App'
        	),
        	'module_partial_path',
        	true
        );
    }
}







