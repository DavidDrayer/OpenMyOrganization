<?php
	namespace holacracy;


class Contact extends Holacracy
{
	protected $_value ;				// Valeur de l'entrée contact
	protected $_label ;				// Label (type ou autre)
	protected $_type_id;  			// ID du type de contact
	protected $_type;  				// Type de contact
	protected $_user_id;  			// ID du type de contact
	protected $_user;  				// Type de contact
	
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getValue () {
		return $this->_value;
	}
	public function getFormatedValue () {
		$val=$this->getValue();
		if (!is_null($this->getType())) {
			$format=$this->getType()->getFormat();
			if ($format!="")
				return str_replace("{value}",$val,$format);
		} 
		return $val;
	}
	public function getLabel () {
		if (isset($this->_label) && $this->_label!="")
			return $this->_label;
		else 
			if (!is_null($this->getType())) 
				return $this->getType()->getLabel();
			 else 
			 	return;
	}
	public function getType () {
		if (is_null($this->_type) && $this->_type_id>0) {
			$this->_type=$this->getManager()->loadContactType($this->_type_id);
		}
		return $this->_type;
	}	
	public function getTypeId () {
		return $this->_type_id;
	}
	
	public function getUser () {
		if (is_null($this->_user) && $this->_user_id>0) {
			$this->_user=$this->getManager()->loadUser($this->_user_id);
		}
		return $this->_user;
	}	
	public function getUserId () {
		return $this->_user_id;
	}
	
	public function setValue ($value) {
		if ($this->_value!=$value) $this->setModified(true);
		$this->_value=$value;
	}
	public function setLabel ($label) {
		if ($this->_label!=$label) $this->setModified(true);
		$this->_label=$label;
	}
	public function setType ($type) {
		if (is_numeric($type)) {
			if ($this->_type_id!=$type) {
				$this->setModified(true);
				$this->_type_id=$type;
				$this->_type=null;
			}		
		} else {
			if ($this->_type_id!=$type->getId()) {
				$this->setModified(true);
				$this->_type=$type;
				$this->_type_id=$type->getId();	
			}		
		}
	}	
	public function setUser ($user) {
		if (is_numeric($user)) {
			if ($this->_user_id!=$user) {
				$this->setModified(true);
				$this->_user_id=$user;
				$this->_user=null;
			}		
		} else {
			if ($this->_user_id!=$user->getId()) {
				$this->setModified(true);
				$this->_user=$user;
				$this->_user_id=$user->getId();	
			}		
		}
	}
}