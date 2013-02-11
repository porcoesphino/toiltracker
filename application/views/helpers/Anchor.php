<?php

class Application_Views_Helpers_Anchor extends Zend_View_Helper_Abstract {
	
	public function anchor($url, $label) {
		
		return '<a href="' . $url . '">' . $label . '</a>';
	}
}