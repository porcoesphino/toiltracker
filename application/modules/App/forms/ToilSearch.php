<?php

class App_Form_ToilSearch extends Zend_Form
{
    public function init()
    {
    	$this->setName('toil_search');
    	$this->setMethod('get');
    	$this->setAction('/App/Toil/index');
    	
    	//Retrieve the list of employees and their ids.
    	$user = Zend_Auth::getInstance()->getStorage()->read();
    	$employeeHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('Employee');
    	$employeeArray = $employeeHelper->fetchSummaries($user->getTeamId());
    	if(empty($employeeArray)) {
    		//BAD
    	}
    	
    	$modifiedArray = array();
    	foreach($employeeArray as $currentEmployee) {
    		$key = $currentEmployee->getId();
    		$value = $currentEmployee->getName();
    		$modifiedArray[$key] = $value;
    	}
    	
    	$employees = new Zend_Form_Element_Select('employeeid');
    	$employees->setRequired(true);
    	$employees->addMultiOptions($modifiedArray);
    	$this->addElement($employees);
    	 
    	$retrieve = new Zend_Form_Element_Submit('retrieve');
    	$retrieve->setLabel('Retrieve');
    	$retrieve->setIgnore(true);
    	$retrieve->clearDecorators();
    	$retrieve->addDecorator('ViewHelper');
    	$retrieve->class = 'btn btn-success';
    	$this->addElement($retrieve);
    	 
    	foreach($this->getElements() as $currentElement) {
    	
    		$currentElement->removeDecorator('HtmlTag');
    		$currentElement->removeDecorator('Label');
    		if(!($currentElement instanceof Zend_Form_Element_Submit)) {
    			
    			$currentElement->addFilter('StripTags');
    		}
    	}
    }
}

