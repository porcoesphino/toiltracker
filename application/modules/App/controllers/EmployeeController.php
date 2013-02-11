<?php

class App_EmployeeController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
        $this->view->employees = $employeeHelper->fetchSummaries();
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
        		$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
        		$employeeHelper->post($form->getValues());
        		
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
        	$values = $employeeHelper->get($employeeId);
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







