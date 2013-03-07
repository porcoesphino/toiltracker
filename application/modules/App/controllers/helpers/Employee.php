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
	
	public function fetchSummaries($teamId) {
		
		$employeeMapper = new Application_Model_EmployeeMapper();
		return $employeeMapper->fetchSummaries($teamId);
	}
	
	public function get($user, $employeeId) {
		
		$employeeMapper = new Application_Model_EmployeeMapper();
		$employee = $employeeMapper->get($user, $employeeId);
		return array('id' => $employee->getId(), 'name' => $employee->getName(), 'email' => $employee->getEmail());
	}
	
	public function post($teamId, $data) {
		
		$employeeMapper = new Application_Model_EmployeeMapper();
		return $employeeMapper->insert($teamId, $data);
	}
	
	public function put($data) {
	
		$employeeMapper = new Application_Model_EmployeeMapper();
		$where = $employeeMapper->getAdapter()->quoteInto('id = ?', $data['id']);
		$employeeMapper->update($data, $where);
	}
	
	public function delete($id) {

		$employeeMapper = new Application_Model_EmployeeMapper();
		$employeeMapper->delete($id);
	}
}