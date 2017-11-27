<?php
	namespace holacracy;


class ChecklistDate extends Holacracy
{
	protected $_date ;		// Date

		
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)

	
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
			return "Définir la VALEUR pour cette HISTORIQUE DE CHECKLIST";
		}
		return false;
	}
}
?>
