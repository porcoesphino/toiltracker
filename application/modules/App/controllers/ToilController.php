<?php

class App_ToilController extends Zend_Controller_Action
{
	protected $_mapper;
	
    public function init()
    {
    	if(empty($this->_mapper)) {
    			
    		$this->_mapper = new Application_Model_ToilMapper();
    	}
    	
    	$forceRedirect = false;
    	$action = $this->getRequest()->getActionName();
    	if($action == 'index') {
    		
    		$user = Zend_Auth::getInstance()->getStorage()->read();
    		$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
    		
    		$employeeId = $this->getRequest()->getParam('employeeid');
    		if((empty($employeeId)) || ($employeeId == ':employeeid')) {
    			
    			//Check to ensure that there is at least one employee managed by the user,
    			//otherwise redirect
    			$employees = $employeeHelper->getAll($user->getTeamId());
    			if(empty($employees)) {

		    		$redirector = $this->_helper->getHelper('Redirector');
		   			$redirector->gotoRoute(
						array(
		    				'action' => 'no-employees',
		    				'controller' => 'Toil',
		    				'module' => 'App'
		    			),
		    			'module_full_path',
		    			true
		    		);
    			}
    		}
    		else {
    			
    			//Check to ensure that the employee exists and is managed by the user.
    			if(!$employeeHelper->isValidEmployee($employeeId, $user)) {
    				 
    				$forceRedirect = true;
    			}
    			
    		}
    	}
    	else if($action == 'post') {

    		$employeeId = $this->getRequest()->getParam('employeeid');
    		 
    		if((empty($employeeId)) || ($employeeId == ':employeeid')) {
    			
    			//No employee specified. Reroute.
    			$forceRedirect = true;
    		}
    		else {
    	
    			$user = Zend_Auth::getInstance()->getStorage()->read();
    			$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
    			if(!$employeeHelper->isValidEmployee($employeeId, $user)) {
    				
    				//Employee exists but is not managed by the user. Reroute.
    				$forceRedirect = true;
    			}
    		}
    	}
    	else if(($action == 'put') || ($action == 'delete')) {
    		
    		$toilId = $this->getRequest()->getParam('id');
    		$employeeId = $this->getRequest()->getParam('employeeid');
    		
    		if((empty($toilId)) || ($toilId == ':id')) {
    			
    			$forceRedirect = true;
    		}
    		else if((empty($employeeId)) || ($employeeId == ':employeeid')) {
    			
    			$forceRedirect = true;
    		}
    		else {
    			
    			//Test to ensure the toilid correctly corresponds to the employeeid.
    			$user = Zend_Auth::getInstance()->getStorage()->read();
    			$toilHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Toil');
    			if(!$toilHelper->isValidToil($toilId, $employeeId, $user)) {
    			    				
    				$forceRedirect = true;
    			}
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

    public function indexAction()
    {    	
    	$employeeId = $this->getRequest()->getParam('employeeid');
    	if(empty($employeeId)) {
    		
    		$this->view->isEmployeeSelected = false;
    	}
    	else {
    		
    		$this->view->isEmployeeSelected = true;
    		
	        $toilArray = $this->_mapper->getAll($employeeId);
	        if(empty($toilArray)) {
	        	
	        	$this->view->isToilHistoryAvailable = false;
	        }
	        else {
	        	
	        	$this->view->isToilHistoryAvailable = true;
	        }
	        $this->view->toilList = $toilArray;
	        
	        $user = Zend_Auth::getInstance()->getStorage()->read();
	        $employeeMapper = new Application_Model_EmployeeMapper();
	        $employee = $employeeMapper->get($employeeId, $user);
	        
	        $this->view->hours = $employee->getToilBalance()->getHours();
	        $this->view->minutes = $employee->getToilBalance()->getMinutes();
	        $this->view->isOwed = $employee->getToilBalance()->getIsOwed();
	        $this->view->employee = $employee;
    	}
        
        $toilSearch = new App_Form_ToilSearch($employeeId);
        $this->view->form = $toilSearch;
    }
    
    public function noEmployeesAction() {
    	
    }

    public function postAction()
    {
    	$request = $this->getRequest();
    	$employeeId = $request->getParam('employeeid');
    	$toilAction = $request->getParam('toilaction');
    	
    	$form = new App_Form_Toil();
        $form->setMethod(Zend_Form::METHOD_POST);
        $form->setAction('/App/Toil/post/employeeid/' . $employeeId . '/toilaction/' . $toilAction);
        $form->getElement('save')->setLabel('Record');
        
        if($toilAction == 'accrue') {
        	$this->view->toil_action = 'accrued';
        }
        else {
        	$this->view->toil_action = 'used';
        }
        
        if($request->isPost()) {
        	
        	if($form->isValid($request->getPost())) {
        		
        		$user = Zend_Auth::getInstance()->getStorage()->read();
        		$employeeMapper = new Application_Model_EmployeeMapper();
        		$employee = $employeeMapper->get($employeeId, $user);
        		
        		//Populate the values into a Toil object.
        		$formValues = $form->getValues();
        		
        		$newToilRecord = new Application_Model_Toil();
        		$newToilRecord->setEmployeeId($employeeId);
        		$newToilRecord->setNote($formValues['notes']);
        		
        		if($toilAction == 'accrue') {
        			$newToilRecord->setToilAction(Application_Model_Toil::ACCRUETOIL);
        		}
        		else {
        			$newToilRecord->setToilAction(Application_Model_Toil::USETOIL);
        		}
        		
        		$durationInMinutes = ($formValues['hours'] * 60) + $formValues['minutes'];
        		$newToilRecord->setDuration($durationInMinutes);
        		
        		$dateString = $formValues['years'] . '-' . $formValues['months'] . '-' . $formValues['days'];
        		$newToilRecord->setDate(new Zend_Date($dateString));
        		
        		//Write to the database.
        		$this->_mapper->insert($employee, $newToilRecord);
        			
        		$redirector = $this->_helper->getHelper('Redirector');
        		$redirector->gotoRoute(
        			array(
        				'action' => 'index',
        				'controller' => 'Toil',
        				'module' => 'App',
        				'employeeid' => $employeeId
        			),
        			'module_full_path_employeeid',
        			true
        		);
          	}
        	else {
        		
        		//Zend to auto display form errors and inject user-specified values.
        	}
        }
        else {
        	
        	//Zend to auto display an empty form, with the date elements populated with todays date.
        	$now = Zend_Date::now();
        	$day = $now->get(Zend_Date::DAY);
        	$month = $now->get(Zend_Date::MONTH);
        	$year = $now->get(Zend_Date::YEAR_8601);
        	$form->populate(array(
        		'days' => $day,
        		'months' => $month,
        		'years' => $year
        	));
        }
        $this->view->employee_id = $employeeId;
        $this->view->form = $form;
    }

    public function putAction()
    {
    	$request = $this->getRequest();
    	$toilId = $request->getParam('id');
    	$employeeId = $request->getParam('employeeid');
    	
    	$form = new App_Form_Toil();
    	$form->setMethod(Zend_Form::METHOD_POST);
    	
    	$action = '/App/Toil/put/id/' . $toilId;
    	$action .= '/employeeid/' . $employeeId;
    	$form->setAction($action);
    	$form->getElement('save')->setLabel('Update');
    	
    	if($request->isPost()) {
    		 
    		//Process the form.
    		if($form->isValid($request->getPost())) {
    			
    			//Retrieve the user so that we can retrieve the employee.
    			$user = Zend_Auth::getInstance()->getStorage()->read();
    			$employeeMapper = new Application_Model_EmployeeMapper();
    			$employee = $employeeMapper->get($employeeId, $user);
    			
    			//Populate the values into a Toil object.
    			$formValues = $form->getValues();
    			$updatedToilRecord = new Application_Model_Toil();
    			$updatedToilRecord->setId($toilId);
    			$updatedToilRecord->setEmployeeId($employeeId);
    			$updatedToilRecord->setNote($formValues['notes']);

    			$currentToilRecord = $this->_mapper->get($toilId);
    			if($currentToilRecord->getToilAction() == Application_Model_Toil::ACCRUETOIL) {
    				$updatedToilRecord->setToilAction(Application_Model_Toil::ACCRUETOIL);
    			}
    			else {
    				$updatedToilRecord->setToilAction(Application_Model_Toil::USETOIL);
    			}
    			
    			$durationInMinutes = ($formValues['hours'] * 60) + $formValues['minutes'];
    			$updatedToilRecord->setDuration($durationInMinutes);
    			
    			$dateString = $formValues['years'] . '-' . $formValues['months'] . '-' . $formValues['days'];
    			$updatedToilRecord->setDate(new Zend_Date($dateString));
    			
    			//Write to the database.
    			$this->_mapper->update($employee, $updatedToilRecord);
    			    	       			
    			$redirector = $this->_helper->getHelper('Redirector');
    			$redirector->gotoRoute(
    				array(
    					'action' => 'index',
    					'controller' => 'Toil',
    					'module' => 'App',
    					'employeeid' => $employeeId
    				),
    				'module_full_path_employeeid',
    				true
    			);
    		}
    		else {
    	
    			//Zend_Form to auto display issues with the form, and to inject
    			//user submitted content.
    		}
    	}
    	else {

    		//Display the form populated with the values for the current toil record.    		
    		$values = $this->_mapper->getConfiguredFormContent($this->getRequest()->getParam('id'));
    		$form->populate($values);
    	}
    	$this->view->employee_id = $employeeId;
    	$this->view->form = $form;
    	$this->render('post');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $employeeId = $this->getRequest()->getParam('employeeid');
      	
        $user = Zend_Auth::getInstance()->getStorage()->read();
        $this->_mapper->delete($id, $user);
        
        $redirector = $this->_helper->getHelper('Redirector');
        $redirector->gotoRoute(
        	array(
        		'action' => 'index',
        		'controller' => 'Toil',
        		'module' => 'App',
        		'employeeid' => $employeeId
        	),
        	'module_full_path_employeeid',
        	true
        );
    }


}







