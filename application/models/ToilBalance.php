<?php

class Application_Model_ToilBalance
{	
	protected $_employeeId;
	protected $_amountInMinutes;
	
	public function __construct($employeeId = null, $initialAmountInMinutes = 0) {
	
		$this->_employeeId = $employeeId;
		$this->_amountInMinutes = $initialAmountInMinutes;
	}
	
	public function getEmployeeId() { return $this->_employeeId; }
	public function getHours() { return 0; }
	public function getMinutes() { return 0; }
	public function getIsOwed() { return false; }
	
	public function addHours($hours) { }
	public function addMinutes($minutes) { }
	
	public function subtractHours($hours) { }
	public function subtractMinutes($minutes) { }
	
}

