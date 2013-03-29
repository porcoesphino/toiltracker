<?php

class Application_Model_Employee
{
	protected $_id;
	protected $_teamId;
	protected $_name;
	protected $_email;
	protected $_toilHistory;
	protected $_toilBalance;

	public function __construct() { $this->_toilHistory = array(); }
	
	public function getId() { return $this->_id; }
	public function getTeamId() { return $this->_teamId; }
	public function getName() { return $this->_name; }
	public function getEmail() { return $this->_email; }
	public function getToilHistory() { return $this->_toilHistory; }
	
	public function getToilBalance() { 
		
		/*
		$summary = 0;
		foreach($this->_toilHistory as $currentToil) {
				
			if($currentToil->getToilAction() == Application_Model_Toil::ACCRUETOIL) {
				$summary += $currentToil->getDuration();
			}
			else {
				$summary -= $currentToil->getDuration();
			}
		}
		return $summary;
		 */
		return $this->_toilBalance; 
	}

	
	public function setId($id) { $this->_id = $id; }
	public function setTeamId($teamId) { $this->_teamId = $teamId; }
	public function setName($name) { $this->_name = $name; }
	public function setEmail($email) { $this->_email = $email; }
	public function addToilRecord(Application_Model_Toil $toilRecord) { $this->_toilHistory[] = $toilRecord; }
	public function setToilBalance($toilBalance) { $this->_toilBalance = $toilBalance; }
}

