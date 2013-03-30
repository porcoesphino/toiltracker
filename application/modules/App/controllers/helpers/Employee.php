<?php

class App_Controllers_Helpers_Employee extends Zend_Controller_Action_Helper_Abstract {
	
	public function isValidEmployee($employeeId, $user) {
		
		$returnVal = true;
		try {
			
			$employeeMapper = new Application_Model_EmployeeMapper();
			$employeeMapper->get($employeeId, $user);
		}
		catch(Zend_Exception $e) {
			
			$returnVal = false;
		}
		return $returnVal;
	}
	
	public function get($user, $employeeId) {
	
		$employeeMapper = new Application_Model_EmployeeMapper();
		return $employeeMapper->get($user, $employeeId);
	}
	
	public function getAll($teamId) {
		
		$employeeMapper = new Application_Model_EmployeeMapper();
		return $employeeMapper->getAll($teamId);
	}
	
	public function post($teamId, $data) {
		
		$employeeMapper = new Application_Model_EmployeeMapper();
		return $employeeMapper->insert($teamId, $data);
	}
	
	public function put($data) {
	
		$employee = new Application_Model_Employee();
		$employee->setId($data['id']);
		$employee->setName($data['name']);
		$employee->setEmail($data['email']);

		$employeeMapper = new Application_Model_EmployeeMapper();
		$employeeMapper->update($employee);
	}
	
	public function delete($id) {

		$employeeMapper = new Application_Model_EmployeeMapper();
		$employeeMapper->delete($id);
	}
}