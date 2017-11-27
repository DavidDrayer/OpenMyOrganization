<?php
	namespace holacracy;


class TypeContact extends Holacracy
{
	protected $_label ;				// Valeur de l'entr�e contact
	protected $_format ;			// Valeur de l'entr�e contact
	
	// Fonctions statiques
	public static function getAllTypeContact () {
		return \holacracy\Holacracy::getManager()->loadContactTypeList();
	}
	
	// Fonctions pour acc�der aux propri�t�s de l'objet (GET et SET)

	public function getLabel () {
		return $this->_label;
	}

	public function setLabel ($label) {
		if ($this->_label!=$label) $this->setModified(true);
		$this->_label=$label;
	}
	public function getFormat () {
		return $this->_format;
	}

	public function setFormat ($format) {
		if ($this->_format!=$format) $this->setModified(true);
		$this->_format=$format;
	}
	
	
}