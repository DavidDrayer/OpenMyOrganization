<?php
	namespace holacracy;


class ChecklistDate extends Holacracy
{
	protected $_date ;		// Date

		
	// Fonctions pour acc�der aux propri�t�s de l'objet (GET et SET)

	
	public function getDate() {
		return $this->_date;
	}
	
	public function setDate($date) {
		$this->_date=$date;
	}
	
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	public function checkForSave() {
		if ($this->getValue()=="") {
			return "D�finir la VALEUR pour cette HISTORIQUE DE CHECKLIST";
		}
		return false;
	}
}
?>
