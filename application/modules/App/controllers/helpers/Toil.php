<?php

class App_Controllers_Helpers_Toil extends Zend_Controller_Action_Helper_Abstract {
	
	/**
	 * Test to ensure the toilid correctly corresponds to the employeeid of an
	 * employee managed by the user.
	 * 
	 * @param integer $toilId
	 * @param integer $employeeId
	 * @param Applicatoin_Model_User $user
	 * @return boolean
	 */
	public function isValidToil($toilId, $employeeId, $user) {
	
		try {

			$toilMapper = new Application_Model_ToilMapper();
			$returnVal = $toilMapper->isValidToil($toilId, $employeeId, $user);
		}
		catch(Exception $e) {
				
			$returnVal = false;
		}
		return $returnVal;
	}
}