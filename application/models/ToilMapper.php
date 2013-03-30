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
	
	/**
	 * @todo Implement graceful handling of the empty toil record.
	 * @param unknown $toilId
	 * @return Application_Model_Toil
	 */
	public function get($toilId) {
		
		$select = $this->_table->select()->where('id = ?', $toilId);
		$row = $this->_table->fetchRow($select);
		
		if(empty($row)) {
			die();
		}
		
		$toil = new Application_Model_Toil();
		$toil->setId($row->id);
		$toil->setEmployeeId($row->employee_id);
		$toil->setToilAction($row->toil_action);
		$toil->setDuration($row->duration);
		
		$date = new Zend_Date($row->date);
		$toil->setDate($date);
		$toil->setNote($row->notes);
		
		return $toil;
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
	
	public function insert(Application_Model_Employee $employee, Application_Model_Toil $newToilRecord) {
		
		$date = $newToilRecord->getDate()->toString('YYYY-MM-dd');
		$data = array(
			'employee_id' => $newToilRecord->getEmployeeId(),
			'toil_action' => $newToilRecord->getToilAction(),
			'date' => $date,
			'duration' => $newToilRecord->getDuration(),
			'notes' => $newToilRecord->getNote()
		);
		
		$success = $this->_table->insert($data);
		if($success) {
			
			//Update the Toil balance for the current employee
			$durationInMinutes = $newToilRecord->getDuration();
			$hours = (int)($durationInMinutes / 60);
			$minutes = $durationInMinutes % 60;
			$toilBalance = $employee->getToilBalance();
			
			if($newToilRecord->getToilAction() == Application_Model_Toil::ACCRUETOIL) {
					
				$toilBalance->addHours($hours);
				$toilBalance->addMinutes($minutes);
			}
			else {
				
				$toilBalance->subtractHours($hours);
				$toilBalance->subtractMinutes($minutes);
			}
			
			$employeeMapper = new Application_Model_EmployeeMapper();
			$employeeMapper->update($employee);
		}
	}
	
	public function update(Application_Model_Employee $employee, Application_Model_Toil $modifiedToilRecord) {
		
		$toilId = $modifiedToilRecord->getId();
		$employeeId = $modifiedToilRecord->getEmployeeId();
		$toilAction = $modifiedToilRecord->getToilAction();
		$date = $modifiedToilRecord->getDate()->toString('YYYY-MM-dd');
		$durationInMinutes = $modifiedToilRecord->getDuration();
		$notes = $modifiedToilRecord->getNote();
		
		//Prepare the update data array based on the data provided.
		$data = array();
		if(!empty($employeeId)) {
			$data['employee_id'] = $employeeId;
		}
		
		if(!empty($date)) {
			$data['date'] = $date;
		}
		
		if(!empty($durationInMinutes)) {
			$data['duration'] = $durationInMinutes;
		}
		
		if(!empty($notes)) {
			$data['notes'] = $notes;
		}
		
		//Retrieve the Toil record as it currently exists in the dbase so that this can be used
		//to later modify the toil balance.
		$currentToilRecord = $this->get($toilId);
	
		//Now update the Toil record in the dbase.
		$where = $this->_table->getAdapter()->quoteInto("id = ?", $toilId);
		$success = $this->_table->update($data, $where);
		
		if($success) {
				
			//Update the Toil balance for the current employee. First remove from the balance
			//the Toil record as it previously was. Then update the balance with the Toil
			//record as it currently is. Then save the balance.
			$previousHours = (int)($currentToilRecord->getDuration() / 60);
			$previousMinutes = $currentToilRecord->getDuration() % 60;
			
			$newHours = (int)($durationInMinutes / 60);
			$newMinutes = $durationInMinutes % 60;
			
			$toilBalance = $employee->getToilBalance();
			
			if($modifiedToilRecord->getToilAction() == Application_Model_Toil::ACCRUETOIL) {
					
				//Remove the old Toil accrual from the Toil balance.
				$toilBalance->subtractHours($previousHours);
				$toilBalance->subtractMinutes($previousMinutes);
				
				//Add the new Toil accrual to the Toil balance.
				$toilBalance->addHours($newHours);
				$toilBalance->addMinutes($newMinutes);
			}
			else {
	
				//Add the old Toil usage back to the Toil balance.
				$toilBalance->addHours($previousHours);
				$toilBalance->addMinutes($previousMinutes);
				
				//Now deduct the new Toil usage from the Toil balance.
				$toilBalance->subtractHours($newHours);
				$toilBalance->subtractMinutes($newMinutes);
			}
				
			$employeeMapper = new Application_Model_EmployeeMapper();
			$employeeMapper->update($employee);
		}
	}
	
	public function delete($id, Application_Model_User $user) {
	
		//Get the toil record that is to be deleted.
		$toil = $this->get($id);
		
		//Attempt to delete the toil record.
		$where = $this->_table->getAdapter()->quoteInto('id = ?', $toil->getId());
		$isSuccessful = $this->_table->delete($where);
		
		if(!$isSuccessful) {
			
			//Something went wrong. Return to prevent further errors.
			return;
		}
		
		$durationInMinutes = $toil->getDuration();
		$hours = (int)($durationInMinutes / 60);
		$minutes = $durationInMinutes % 60;
		
		//Identify the employee to whom this toil record belongs.
		$employeeMapper = new Application_Model_EmployeeMapper();
		$employee = $employeeMapper->get($toil->getEmployeeId(), $user);
		$toilBalance = $employee->getToilBalance();
		
		//Update the toil balance associated with the employee to reflect deletion.
		if($toil->getToilAction() == Application_Model_Toil::ACCRUETOIL) {
				
			$toilBalance->subtractHours($hours);
			$toilBalance->subtractMinutes($minutes);
		}
		else {
				
			$toilBalance->addHours($hours);
			$toilBalance->addMinutes($minutes);
		}
		
		$employeeMapper->update($employee);
	}
}

