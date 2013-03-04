<?php

class Application_Model_TeamMapper
{
	protected $_table;
	
	public function __construct() { 
		
		$this->_table = new Application_Model_DbTable_Teams();
	}
	
	public function getDbAdapter() {
		
		return $this->_table->getAdapter();
	}
	
	public function insert($data) {
		
		$insertData = array(
			'team_name' => 'not yet implemented',
		);
		$primaryKey = $this->_table->insert($insertData);
		
		$team = new Application_Model_Team();
		$team->setId($primaryKey);
		return $team;
	}
}

