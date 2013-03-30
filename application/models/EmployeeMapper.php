<?php

class Application_Model_EmployeeMapper
{
	protected $_table;
	
	public function __construct() {
		
		$this->_table = new Application_Model_DbTable_Employees();
	}
	
	public function get($employeeId, $user) {
		
		$where = $this->_table->getAdapter()->quoteInto('id = ?', $employeeId);
		$where .= ' AND ';
		$where .= $this->_table->getAdapter()->quoteInto('team_id = ?', $user->getTeamId());
		
		$select = $this->_table->select()->where($where);
		$row = $this->_table->fetchRow($select);		
		if($row == null) {
			
			throw new Zend_Exception('Invalid employee specified.');
		}
		
		return $this->_getPopulatedEmployee($row);
	}
	
	public function getAll($teamId) {
	
		$employees = new Application_Model_DbTable_Employees();
		$where = $employees->getAdapter()->quoteInto('team_id = ?', $teamId);
		$select = $employees->select()->where($where);
		$employeeRowset = $employees->fetchAll($select);
	
		$employeeArray = array();
		foreach($employeeRowset as $currentEmployee) {
				
			$employeeArray[] = $this->_getPopulatedEmployee($currentEmployee);
		}
	
		return $employeeArray;
	}
	
	protected function _getPopulatedEmployee(Zend_Db_Table_Row_Abstract $currentEmployee) {
		
		$employee = new Application_Model_Employee();
		$employee->setId($currentEmployee->id);
		$employee->setTeamId($currentEmployee->team_id);
		$employee->setName($currentEmployee->name);
		$employee->setEmail($currentEmployee->email);
			
		$toilBalance = new Application_Model_ToilBalance(
			$currentEmployee->id,
			$currentEmployee->toil_balance);
		$employee->setToilBalance($toilBalance);
			
		$toil = new Application_Model_DbTable_Toil();
		$where = $toil->getAdapter()->quoteInto('employee_id = ?', $currentEmployee->id);
		$select = $toil->select()->where($where);
		$toilRowset = $toil->fetchAll($select);
			
		foreach($toilRowset as $toilRow) {
		
			$toil = new Application_Model_Toil();
			$toil->setId($toilRow->id);
			$toil->setEmployeeId($toilRow->employee_id);
			$toil->setToilAction($toilRow->toil_action);
			$toil->setDate(new Zend_Date($toilRow->date));
			$toil->setDuration($toilRow->duration);
			$toil->setNote($toilRow->notes);
			$employee->addToilRecord($toil);
		}
		
		return $employee;
	}
	
	public function insert($teamId, $data) {
		
		$table = new Application_Model_DbTable_Employees();
		$insertData = array(
			'team_id' => $teamId,
			'name' => $data['name'],
			'email' => $data['email']
		);
		$primaryKey = $table->insert($insertData);
		return $primaryKey;
	}
	
	public function update(Application_Model_Employee $employee) {
		
		//Assemble the data and update the table where applicable.
		$teamId = $employee->getTeamId();
		$name = $employee->getName();
		$email = $employee->getEmail();
		$balance = $employee->getToilBalance();
		
		$data = array();
		if(!empty($teamId)) {
			$data['team_id'] = $teamId;
		}
		
		if(!empty($name)) {
			$data['name'] = $name;
		}
		
		if(!empty($email)) {
			$data['email'] = $email;
		}
		
		if(!empty($balance)) {
			$data['toil_balance'] = $balance->getAmountInMinutes();
		}
		
		$table = new Application_Model_DbTable_Employees();
		$where = $table->getAdapter()->quoteInto('id = ?', $employee->getId());
		$table->update($data, $where);
	}
	
	public function delete($id) {
	
		$table = new Application_Model_DbTable_Employees();
		$where = $table->getAdapter()->quoteInto('id = ?', $id);
		$table->delete($where);
	}
	
	public function getAdapter() {
		
		$table = new Application_Model_DbTable_Employees();
		return $table->getAdapter();
	}
}

