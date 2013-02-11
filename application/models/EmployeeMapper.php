<?php

class Application_Model_EmployeeMapper
{
	public function fetchSummaries() {
		
		$employees = new Application_Model_DbTable_Employees();
		$employeeRowset = $employees->fetchAll();
		$employeeArray = array();
		foreach($employeeRowset as $currentEmployee) {
		
			$employee = new Application_Model_Employee();
			$employee->setId($currentEmployee->id);
			$employee->setName($currentEmployee->name);
			$employee->setEmail($currentEmployee->email);
			
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
				$employee->addToil($toil);
			}
			
			$employeeArray[] = $employee;
		}
				
		return $employeeArray;
	}
	
	public function get($employeeId) {
		
		$table = new Application_Model_DbTable_Employees();
		$row = $table->find($employeeId)->current();
		$employee = new Application_Model_Employee();
		$employee->setId($row->id);
		$employee->setName($row->name);
		$employee->setEmail($row->email);
		return $employee;
	}
	
	public function insert($data) {
		
		$table = new Application_Model_DbTable_Employees();
		$insertData = array(
			'name' => $data['name'],
			'email' => $data['email']
		);
		$table->insert($insertData);
	}
	
	public function update($data, $where) {
	
		$table = new Application_Model_DbTable_Employees();
		$insertData = array(
				'name' => $data['name'],
				'email' => $data['email']
		);
		$table->update($insertData, $where);
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

