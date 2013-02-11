<?php

class Application_Form_Toil extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    	$this->setName('toil');
    	 
    	$id = new Zend_Form_Element_Hidden('id');
    	$this->addElement($id);
    	
    	$employeeId = new Zend_Form_Element_Hidden('employee_id');
    	$this->addElement($employeeId);
    	
    	//Date stuff
    	$dateArray = array('' => '');
    	for($i = 1; $i < 32; $i++) {
    	
    		$value = $i;
    		if($i < 10) {
    			
    			$value = '0' . $i;
    		}
    		$dateArray[$value] = $value;
    	}
    	$days = new Zend_Form_Element_Select('days');
    	$days->setRequired(false);
    	$days->addMultiOptions($dateArray);
    	$this->addElement($days);
    	
    	$months = new Zend_Form_Element_Select('months');
    	$months->setRequired(false);
    	$months->addMultiOptions(
    		array(
    			'' => '',
    			'01' => 'Jan',
    			'02' => 'Feb',
    			'03' => 'Mar',
    			'04' => 'Apr',
    			'05' => 'May',
    			'06' => 'Jun',
    			'07' => 'Jul',
    			'08' => 'Aug',
    			'09' => 'Sep',
    			'10' => 'Oct',
    			'11' => 'Nov',
    			'12' => 'Dec')
    	);
    	$this->addElement($months);
    	
    	$currentYear = Zend_Date::now()->get(Zend_Date::YEAR);
    	$years = new Zend_Form_Element_Select('years');
    	$years->setRequired(false);
    	$years->addMultiOptions(
    		array(
    			'' => '',
    			($currentYear-1) => ($currentYear-1),
    			$currentYear => $currentYear,
    			($currentYear+1) => ($currentYear+1)
    		)
    	);
    	$this->addElement($years);
    	
    	$hoursList = array();
    	for($i = 0; $i < 24; $i++) {
    		$hoursList[$i] = $i;
    	}
    	
    	$hours = new Zend_Form_Element_Select('hours');
    	$hours->setRequired(false);
    	$hours->setMultiOptions($hoursList);
    	$this->addElement($hours);
    	
    	$minutesList = array();
    	for($i = 0; $i < 60; $i++) {
    		$minutesList[$i] = $i;
    	}
    	
    	$minutes = new Zend_Form_Element_Select('minutes');
    	$minutes->setRequired(true);
    	$minutes->setMultiOptions($minutesList);
    	$this->addElement($minutes);
    	
    	$notes = new Zend_Form_Element_Textarea('notes');
    	$notes->setRequired(false);
    	$notes->addValidator(new Zend_Validate_StringLength(array('max' => 80)));
    	$notes->setAttrib('maxlength', 80);
    	$notes->setOptions(array('cols' => '20', 'rows' => '4'));
    	$this->addElement($notes);
    	 
    	$add = new Zend_Form_Element_Submit('save');
    	$add->setIgnore(true);
    	$add->clearDecorators();
    	$add->addDecorator('ViewHelper');
    	$this->addElement($add);
    	 
    	foreach($this->getElements() as $currentElement) {
    	
    		$currentElement->removeDecorator('HtmlTag');
    		$currentElement->removeDecorator('Label');
    		if(!($currentElement instanceof Zend_Form_Element_Submit)) {
    			
    			$currentElement->addFilter('StripTags');
    		}
    	}
    }
    
    public function isValid($data) {
    	
    	$returnVal = parent::isValid($data);
    	
    	//Valid date check.
    	$isDateValid = true;
    	$date = $data['years'] . '-' . $data['months'] . '-' . $data['days'];
    	if(($data['days'] == '') || ($data['months'] == '') || ($data['years'] == '')) {
    		
    		$isDateValid = false;
    	} 	
    	else if(!Zend_Date::isDate($date, 'YYYY-mm-dd')) {
    		
    		$isDateValid = false;
    	}
    	
    	if(!$isDateValid) {
    		
    		$this->getElement('years')->addError('Please provide a correct date');
    		$returnVal = false;
    	}
    	
    	
    	//Valid duration check
    	$isDurationValid = true;
    	if((!is_numeric($data['hours']) || (!is_numeric($data['minutes'])))) {
    		
    		$isDurationValid = false;
    	}
    	else if(($data['hours'] == '0') && ($data['minutes'] == '0')) {
    		
    		$isDurationValid = false;
    	}
    	else if(($data['hours'] > 23) || ($data['minutes'] > 59)) {
    		
    		$isDurationValid = false;
    	}
    	
    	if(!$isDurationValid) {
    		
    		$this->getElement('minutes')->addError('Please provide a duration');
    		$returnVal = false;
    	}
    	
    	return $returnVal;
    }

}

