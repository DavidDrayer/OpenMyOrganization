<?php
	namespace holacracy;


class Checklist extends Holacracy
{
	protected $_description ;		// Description
	protected $_title ;				// Titre
	protected $_circle_id;  		// Cercle sous la forme d'un id cercle
	protected $_circle;  			// Cercle sous la forme d'un lien sur le cercle
	protected $_role_id;  		// Role concerné sous la forme d'un id cercle
	protected $_role;  			// Role concerné sous la forme d'un lien sur le cercle
	protected $_user_id;  			// Role concerné sous la forme d'un id cercle
	protected $_user;  				// Role concerné sous la forme d'un lien sur le cercle
	protected $_recurrence_id;	// Recurrence sous forme d'ID 	
	protected $_recurrence;		// Recurrence sous la forme d'objet
	protected $_dates = array();	// Liste historique des valeurs

	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)

	public function getDescription () {
		return $this->_description;
	}
	public function getTitle () {
		return $this->_title;
	}
	public function getRole () {
		if (is_null($this->_role) && $this->_role_id>0) {
			$this->_role=self::getManager()->loadRole($this->_role_id);
		}
		return $this->_role;
	}
	public function getRoleId () {
		return $this->_role_id;
	}
	public function getCircle () {
		if (is_null($this->_circle) && $this->_circle_id>0) {
			$this->_circle=self::getManager()->loadCircle($this->_circle_id);
		}
		return $this->_circle;
	}
	public function getCircleId () {
		if (isset($this->_circle)) {
			$this->_circle_id=$this->_circle->getId();
		}
		return $this->_circle_id;
	}
	public function getUser () {
		if (is_null($this->_user) && $this->_user_id>0) {
			$this->_user=self::getManager()->loadUser($this->_user_id);
		}
		return $this->_user;
	}
	public function getUserId () {
		return $this->_user_id;
	}
	public function getRecurrence () {
		if (is_null($this->_recurrence) && $this->_recurrence_id>0) {
			$this->_recurrence=self::getManager()->loadRecurrence($this->_recurrence_id);
		}
		return $this->_recurrence;
	}
	public function getRecurrenceId () {
		if (isset($this->_recurrence)) {
			$this->_recurrence_id=$this->_recurrence->getId();
		}
		return $this->_recurrence_id;
	}
	
	public function setDescription ($description) {
		if ($this->_description!=$description) $this->setModified(true);
		$this->_description=$description;
	}
	public function setTitle ($title) {
		if ($this->_title!=$title) $this->setModified(true);
		$this->_title=$title;
	}
	public function setRole ($role) {
		if ($this->_role_id!=$role->getId()) $this->setModified(true);
		$this->_role=$role;
		$this->_role_id=$role->getId();
	}
	public function setRoleId ($roleId) {
		if ($this->_role_id!=$roleId) $this->setModified(true);
		$this->_role_id=$roleId;
		$this->_role=null;
	}
	public function setCircle ($circle) {
		if ($this->_circle_id!=$circle->getId()) $this->setModified(true);
		$this->_circle=$circle;
		$this->_circle_id=$circle->getId();
	}
	public function setCircleId ($circleId) {
		if ($this->_circle_id!=$circleId) $this->setModified(true);
		$this->_circle_id=$circleId;
		$this->_circle=null;
	}
	public function setUser ($user) {
		if ($this->_user_id!=$user->getId()) $this->setModified(true);
		$this->_user=$user;
		$this->_user_id=$user->getId();
	}
	public function setUserId ($userId) {
		if ($this->_user_id!=$userId) $this->setModified(true);
		$this->_user_id=$userId;
		$this->_user=null;
	}
	public function setRecurrence ($recurrence) {
		if ($this->_recurrence_id!=$recurrence->getId()) $this->setModified(true);
		$this->_recurrence=$recurrence;
		$this->_recurrence_id=$recurrence->getId();
	}
	public function setRecurrenceId ($recurrenceId) {
		if ($this->_recurrence_id!=$recurrenceId) $this->setModified(true);
		$this->_recurrence_id=$recurrenceId;
		$this->_recurrence=null;
	}

	public function getDates() {
		// Chargement de la liste des valeurs que si nécessaire (pour éviter la création itérative de grosses structures)
		if (count($this->_dates)==0) {
			$this->_dates=self::getManager()->loadChecklistDates($this);	
		}
		$return_array=array();
		foreach($this->_dates as $date) {
				$return_array[] = $date;
		}
		return $return_array;		
	}
	
	public function getDate() {
		if (count($this->_dates)==0) {
			$this->_dates=self::getManager()->loadChecklistDates($this);	
		}		
		if (count($this->_dates)>0) {
			return end($this->_dates);
		}
	}
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	public function checkForSave() {

		if ($this->getTitle()=="") {
			if ($this->getDescription()!="") {
				$this->setTitle($this->getDescription());
				$this->setDescription("");
			} else
			return "Définir le TITRE pour cette CHECKLIST [".$this->getDescription()."]";
		}
		
		return false;
	}
}
?>
