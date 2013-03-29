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
	
		$employeeMapper = new Application_Model_EmployeeMapper();
		$where = $employeeMapper->getAdapter()->quoteInto('id = ?', $data['id']);
		$employeeMapper->update($data, $where);
	}
	
	public function delete($id) {

		$employeeMapper = new Application_Model_EmployeeMapper();
		$employeeMapper->delete($id);
	}
}