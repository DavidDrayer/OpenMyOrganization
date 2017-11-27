<?php
	namespace holacracy;

class Help extends Holacracy
{
	private $_text;	// Text HTML
	private $_title;	// Text HTML
	private $_key;	// RegExp d'identification
	
	function getText() {
		return $this->_text;
	}
	function getTitle() {
		return $this->_title;
	}
	
	function getKey() {
		return $this->_key;
	}
	
	function setText($text) {
		$this->_text=$text;
	}
	function setTitle($text) {
		$this->_title=$text;
	}
	
	function setKey($key) {
		$this->_key=$key;
	}
}