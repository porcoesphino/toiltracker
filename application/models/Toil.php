<?php

class Application_Model_Toil
{
	const ACCRUETOIL = 1;
	const USETOIL = 2;
	
	protected $_id;
	protected $_employeeId;
	protected $_toilAction;
	protected $_date;
	protected $_duration;
	protected $_note;
	
	public function getId() { return $this->_id; }
	public function getEmployeeId() { return $this->_employeeId; }
	public function getToilAction() { return $this->_toilAction; }
	public function getDate() { return $this->_date; }
	public function getDuration() { return $this->_duration; }
	public function getNote() { return $this->_note; }
	
	public function setId($id) { $this->_id = $id; }
	public function setEmployeeId($employeeId) { $this->_employeeId = $employeeId; }
	public function setToilAction($toilAction) { $this->_toilAction = $toilAction; }
	public function setDate(Zend_Date $date) { $this->_date = $date; }
	public function setDuration($duration) { $this->_duration = $duration; }
	public function setNote($note) { $this->_note = $note; }
}

