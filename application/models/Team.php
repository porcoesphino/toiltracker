<?php

class Application_Model_Team
{
	protected $_id;
	protected $_name;

	public function __construct() { }
	
	public function getId() { return $this->_id; }
	public function getName() { return $this->_name; }
	
	public function setId($id) { $this->_id = $id; }
	public function setName($name) { $this->_name = $name; }
}

