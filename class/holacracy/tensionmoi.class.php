<?php
	namespace holacracy;


class TensionMoi extends Holacracy
{
	protected $_name ;		// Nom de la tension,
	protected $_id;  		// ID de la tension
	protected $_user_id;  		// User de la tension
	protected $_user;  		// User de la tension
	protected $_role_id;  		// Role associé à la tension
	protected $_circle_id;  		// Cercle associé à la tension
	protected $_orga_id;  			// Org associé à la tension
	protected $_type;	// Type de tension 	
	protected $_description;		// Description pour la tension
	protected $_role_name;			// Role name

	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)

	public function getDescription () {
		return $this->_description;
	}
	
	public function getRoleName () {
		return $this->_role_name;
	}

	public function getRoleId () {
		return $this->_role_id;
	}
	
	public function getOrgId () {
		return $this->_orga_id;
	}
	public function getUserId () {
		return $this->_user_id;
	}
	public function getUser () {
		if (is_null($this->_user) && $this->_user_id>0) {
			$this->_user=self::getManager()->loadUser($this->_user_id);
		}
		return $this->_user;
	}
	public function setUser ($user) {
		$this->_user=$user;
		$this->_user_id=$user->getId();
	}
	public function getType () {
		return $this->_type;
	}
	public function getName () {
		return $this->_name;
	}
	
	public function getCircleId () {
		return $this->_circle_id;
	}
		
	public function setOrgId ($org) {
	if ($this->_orga_id!=$org) $this->setModified(true);
		$this->_orga_id=$org;
	}
	
	public function setDescription ($description) {
		if ($this->_description!=$description) $this->setModified(true);
		$this->_description=$description;
	}
	
	public function setCircleId ($circle) {
		if ($this->_circle_id!=$circle) $this->setModified(true);
		$this->_circle_id=$circle;
	}

	public function setRoleId ($role) {
	if ($this->_role_id!=$role) $this->setModified(true);
		$this->_role_id=$role;
	}
	
	public function setRoleName ($rolename) {
	if ($this->_role_name!=$rolename) $this->setModified(true);
		$this->_role_name=$rolename;
	}
	
	public function setUserId ($user) {
	if ($this->_user_id!=$user) $this->setModified(true);
		$this->_user_id=$user;
	}

	public function setType ($type) {
		if ($this->_type!=$type) $this->setModified(true);
		$this->_type=$type;
	}
	public function setName ($name) {
	if ($this->_name!=$name) $this->setModified(true);
		$this->_name=$name;
	}
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	
	/*
	public function checkForSave() {
		if (!($this->getCircleId()>0)) {
			return "Définir le CIRCLE pour ce METRIC [".$this->getDescription()."]";
		}
		if (!($this->getRecurrenceId()>0)) {
			return "Définir la RECURRENCE pour ce METRIC [".$this->getDescription()."]";
		}
		if ($this->getDescription()=="") {
			return "Définir la DESCRIPTION pour ce METRIC [".$this->getDescription()."]";
		}
		
		return false;
	}
	*/
}
?>
