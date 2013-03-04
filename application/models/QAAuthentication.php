<?php

/**
 * Encapsulates a question/answer set, comprising the question/answer given
 * by the user as a means of identifying the user during an account-authentication
 * activities.
 * 
 * @author benjaminjosephvickers
 */
class Application_Model_QAAuthentication
{
	protected $_question;
	protected $_answer;
	
	public function getQuestion() { return $this->_question; }
	public function getAnswer() { return $this->_answer; }
	
	public function setQuestion($question) { $this->_question = $question; }
	public function setAnswer($answer) { $this->_answer = $answer; }
}

