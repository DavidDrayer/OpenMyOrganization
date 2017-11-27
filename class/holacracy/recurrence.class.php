<?php
	namespace holacracy;


class Recurrence extends Holacracy
{
	protected $_label ;		// Description
	protected $_nbdays ;	// Time laps en jours

	// Fonctions statiques
	public static function getAllRecurrence () {
		return \holacracy\Holacracy::getManager()->loadRecurrenceList();
	}
		
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)

	public function getLabel () {
		return $this->_label;
	}
	
	public function getTimeLaps() {
		return $this->_timelaps;
	}
	
	public function setTimeLaps($nbdays) {
		$this->_timelaps=$nbdays;
	}
	
	public function setLabel ($label) {
		if ($this->_label!=$label) $this->setModified(true);
		$this->_label=$label;
	}
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	public function checkForSave() {
		if ($this->getLabel()=="") {
			return "Définir le LABEL pour cette RECURRENCE [".$this->getLabel()."]";
		}
		return false;
	}
}
?>
