<?php
	namespace holacracy;


class MetricValue extends Holacracy
{
	protected $_value;		// Valeur
	protected $_date ;		// Date

		
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)

	public function getValue () {
		return $this->_value;
	}
	
	public function getDate() {
		return $this->_date;
	}
	
	public function setDate($date) {
		$this->_date=$date;
	}
	
	public function setValue ($value) {
		if ($this->_value!=$value) $this->setModified(true);
		$this->_value=$value;
	}
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	public function checkForSave() {
		if ($this->getValue()=="") {
			return "Définir la VALEUR pour cette HISTORIQUE DE METRIC";
		}
		return false;
	}
}
?>
