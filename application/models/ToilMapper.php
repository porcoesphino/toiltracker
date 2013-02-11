<?php

class Application_Model_ToilMapper
{
	protected $_table;
	
	public function __construct() {
		
		$this->_table = new Application_Model_DbTable_Toil();
	}
	
	public function fetchSummaries($employeeId) {
	
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
	
	public function getEmployeeIdFromToilId($id) {
		
		$rowset = $this->_table->find($id);
		$row = $rowset->current();
		return $row->employee_id;
	}
	
	public function insert($toilAction, $formValues) {
		
		$date = $formValues['years'] . '-' . $formValues['months'] . '-' . $formValues['days'];
		$duration = ($formValues['hours'] * 60) + $formValues['minutes'];
		
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
}

