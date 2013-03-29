<?php

class Application_Model_ToilMapper
{
	protected $_table;
	
	public function __construct() {
		
		$this->_table = new Application_Model_DbTable_Toil();
	}
	
	/**
	 * @todo Rename this as 'get' and change so that it retuns content only. A
	 * view helper can populate this data into the Toil table.
	 * @param unknown $id
	 * @return multitype:unknown number NULL Ambigous <>
	 */
	public function getConfiguredFormContent($id) {
	
		$row = $this->_table->find($id)->current();
	
		$date = explode('-', $row->date);
	
		if($row->duration < 60) {
			$hours = 0;
			$minutes = $row->duration;
		}
		else {
			$hours = (int)($row->duration / 60);
			$minutes = $row->duration - ($hours * 60);
		}
			
		//Populate employee into form.
		$employeeId = $row->employee_id;
		$values = array(
				'id' => $row->id,
				'employee_id' => $row->employee_id,
				'days' => $date[2],
				'months' => $date[1],
				'years' => $date[0],
				'hours' => $hours,
				'minutes' => $minutes,
				'notes' => $row->notes
		);
		return $values;
	}
	
	public function getAll($employeeId) {
	
		$select = $this->_table->select()->where('employee_id = ?', $employeeId);
		$rowset = $this->_table->fetchAll($select);
	
		$toilArray = array();
		foreach($rowset as $currentRow) {
	
			$currentToil = new Application_Model_Toil();
			$currentToil->setId($currentRow->id);
			$currentToil->setEmployeeId($currentRow->employee_id);
			$currentToil->setToilAction($currentRow->toil_action);
			$currentToil->setDuration($currentRow->duration);
	
			$date = new Zend_Date($currentRow->date);
			$currentToil->setDate($date);
			$currentToil->setNote($currentRow->notes);
	
			$toilArray[] = $currentToil;
		}
		return $toilArray;
	}
	
	public function isValidToil($toilId, $employeeId, $user) {
		
		//First test. Test to see if the toil exists in the database.
		$actualEmployeeId = null;
		try {
		
			$actualEmployeeId = $this->getEmployeeIdFromToilId($toilId);
		}
		catch(Exception $e) {
			
			return false;
		}
		
		
		//Second test. Test to see if the employee IDs are the same.
		if($actualEmployeeId != $employeeId) {
			
			return false;
		}
		
		
		//Third test. Test to see if the toil is correctly associated with 
		//an employee managed by the user.
		$employee = null;
		try {
		
			$employeeMapper = new Application_Model_EmployeeMapper();
			$employee = $employeeMapper->get($employeeId, $user);
		}
		catch(Zend_Exception $e) {
			
			Zend_Debug::dump('HERE7');die();
			return false;
		}
		
		return true;
	}
	
	public function getEmployeeIdFromToilId($id) {
		
		$rowset = $this->_table->find($id);
		if($rowset->count() == 0) {
			
			throw new Zend_Exception('Toil record not found.');
		}
		
		$row = $rowset->current();
		return $row->employee_id;
	}
	
	public function insert($toilAction, $formValues) {
		
		$date = $formValues['years'] . '-' . $formValues['months'] . '-' . $formValues['days'];
		$duration = ($formValues['hours'] * 60) + $formValues['minutes'];
		
		//Retrieve employee
		//If is ACCRUE
		//toilBalance->addhours() $toilBalance->addMinutes(); $employee->save()
		//else
		//toilBalance->subtractHours() $toilBalance->subtractMinutes()
		//employeeMapper->update(employee)
		
		if($toilAction == 'accrue') { $toilAction = 1; }
		else { $toilAction = 2; }
		
		$data = array(
				'employee_id' => $formValues['employee_id'],
				'toil_action' => $toilAction,
				'date' => $date,
				'duration' => $duration,
				'notes' => $formValues['notes']
		);
		
		$this->_table->insert($data);
	}
	
	public function update($formValues) {
	
		$date = $formValues['years'] . '-' . $formValues['months'] . '-' . $formValues['days'];
		$duration = ($formValues['hours'] * 60) + $formValues['minutes'];
		$data = array(
				'date' => $date,
				'duration' => $duration,
				'notes' => $formValues['notes']
		);
		$where = $this->_table->getAdapter()->quoteInto("id = ?", $formValues['id']);
		$this->_table->update($data, $where);
	}
	
	public function delete($id) {
	
		$where = $this->_table->getAdapter()->quoteInto('id = ?', $id);
		$this->_table->delete($where);
	}
}

