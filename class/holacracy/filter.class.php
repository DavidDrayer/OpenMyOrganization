<?php
	namespace holacracy;

class Filter
{
	protected $_criteria=Array();   // Liste des critères
	
	public function addCriteria($label, $value) {
		$this->_criteria[$label]=$value;
	} 	
	public function getCriteria($label) {
		if (isset($this->_criteria[$label])) {
			return $this->_criteria[$label];
		} else {
			return NULL;
		}
	} 	
}