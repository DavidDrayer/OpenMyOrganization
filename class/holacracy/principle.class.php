<?php
	namespace holacracy;


class Principle extends Holacracy
{
	protected $_value;  		// organisation
	protected $_value_id;  	// organisation sous la forme d'un id 
	protected $_description;  		// texte
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getDescription () {
		return $this->_description;
	}

	public function getValue () {
		if (is_null($this->_value) && $this->_value_id>0) {
			$this->_value=self::getManager()->loadValue($this->_value_id);
		}
		return $this->_value;
	}
	public function getValueId () {
		return $this->_value_id;
	}
	public function setDescription ($description) {
		$this->_description=$description;
	}

	public function setValue ($value) {
		if ($this->_value_id!=$value->getId()) $this->setModified(true);
		$this->_value=$value;
		$this->_value_id=$value->getId();
	}
	public function setValueId ($valueId) {
		if ($this->_value_id!=$valueId) $this->setModified(true);
		$this->_value_id=$valueId;
		$this->_value=null;
	}
	
}
?>
