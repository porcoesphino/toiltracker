<?php

class Application_Model_Employee
{
	protected $_id;
	protected $_teamId;
	protected $_name;
	protected $_email;
	protected $_toil;

	public function __construct() { $this->_toil = array(); }
	
	public function getId() { return $this->_id; }
	public function getTeamId() { return $this->_teamId; }
	public function getName() { return $this->_name; }
	public function getEmail() { return $this->_email; }
	public function getToil() { return $this->_toil; }

	public function getToilAvailable() {
	
		$summary = 0;
		foreach($this->_toil as $currentToil) {
				
			if($currentToil->getToilAction() == Application_Model_Toil::ACCRUETOIL) {
				$summary += $currentToil->getDuration();
			}
			else {
				$summary -= $currentToil->getDuration();
			}
		}
		return $summary;
	}
	
	public function setId($id) { $this->_id = $id; }
	public function setTeamId($teamId) { $this->_teamId = $teamId; }
	public function setName($name) { $this->_name = $name; }
	public function setEmail($email) { $this->_email = $email; }
	public function addToil(Application_Model_Toil $toil) { $this->_toil[] = $toil; }
}

