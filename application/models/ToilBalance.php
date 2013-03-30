<?php

class Application_Model_ToilBalance
{	
	protected $_employeeId;
	protected $_balanceInMinutes;
	
	public function __construct($employeeId = null, $initialBalanceInMinutes = 0) {
	
		$this->_employeeId = $employeeId;
		$this->_balanceInMinutes = $initialBalanceInMinutes;
	}
	
	public function getEmployeeId() { 
		
		return $this->_employeeId; 
	}
	
	public function getHours() {
		
		$balance = $this->_balanceInMinutes;
		if($balance > 0) {
			
			$hours = (int)($balance / 60);
		}
		else if($balance == 0) {
			
			$hours = 0;
		}
		else {
		
			//If here then there is a negative duration. This means that the user owes more TOIL than they have.
			//Easy to work with - simply multiply by -1 to remove the negative number, then treat as before.
			$balance = ($balance * -1);
			$hours = (int)($balance / 60);
		}
		
		return $hours;
	}
	
	public function getMinutes() {
		
		$balance = $this->_balanceInMinutes;
		if($balance > 0) {
			
			$minutes = $balance % 60;
		}
		else if($balance == 0) {
			
			$minutes = 0;
		}
		else {
		
			//If here then there is a negative duration. This means that the user owes more TOIL than they have.
			//Easy to work with; simply multiply by -1 to remove the negative number, then treat as before.
			$balance = ($balance * -1);
			$minutes = $balance % 60;
		}
		
		return $minutes;
	}
	
	public function getIsOwed() { 
		
		if($this->_balanceInMinutes < 0) {
			
			return true;
		}
		return false; 
	}
	
	public function addHours($hours) { 

		$this->_balanceInMinutes += ($hours * 60);
	}
	
	public function addMinutes($minutes) { 
		
		$this->_balanceInMinutes += $minutes;
	}
	
	public function subtractHours($hours) { 

		$this->_balanceInMinutes -= ($hours * 60);
	}
	
	public function subtractMinutes($minutes) { 
		
		$this->_balanceInMinutes -= $minutes;
	}
	
	public function getAmountInMinutes() {
		
		return $this->_balanceInMinutes;
	}
}

