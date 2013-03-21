<?php

class Application_Model_ToilBalance
{	
	protected $_employeeId;
	protected $_hours;
	protected $_minutes;
	protected $_isOwed;
	
	public function getEmployeeId() { return $this->_employeeId; }
	public function getHours() { return $this->_hours; }
	public function getMinutes() { return $this->_minutes; }
	public function getIsOwed() { return $this->_isOwed; }
	
	public function setEmployeeId($employeeId) { $this->_employeeId = $employeeId; }
	public function setHours($hours) { $this->_hours = $hours; }
	public function setMinutes($minutes) { $this->_minutes = $minutes; }
	public function setIsOwed($isOwed) { $this->_isOwed = $isOwed; }
}

