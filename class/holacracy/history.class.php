<?php
	namespace holacracy;


class History extends Holacracy
{
	protected $_title ;				// Titre de l'entrée d'historique
	protected $_description ;		// Description
	protected $_date;				// Date de création
	protected $_author_id;  		// Auteur sous la forme d'un id user
	protected $_author;  			// Auteur sous la forme d'un lien sur le user
	protected $_circle_id;  		// Cercle sous la forme d'un id cercle
	protected $_circle;  			// Cercle sous la forme d'un lien sur le cercle
	protected $_role_id;  		// Cercle sous la forme d'un id cercle
	protected $_role;  			// Cercle sous la forme d'un lien sur le cercle
	protected $_meeting_id;  		// Meeting sous la forme d'un ID
	protected $_meeting;  			// Meeting
	protected $_tension_id;  		// Tension sous la forme d'un ID
	protected $_tension;  			// Tension
	protected $_link;  				// Liens vers une page de complément
	protected $_parent_id; 			// Structure en arbre
	protected $_parent; 			// Structure en arbre, élément parent
	protected $_childs= array(); 	// Structure en arbre, enfants
	private $_childs_loaded=false;   // Les enfants ont-ils été chargés dans la DB?
	
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getTitle () {
		return $this->_title;
	}
	public function getDescription () {
		return $this->_description;
	}
	public function getDate () {
		return $this->_date;
	}
	public function getUser () {
		if (is_null($this->_author) && $this->_author_id>0) {
			$this->_author=$this->getManager()->loadUser($this->_author_id);
		}
		return $this->_author;
	}
	public function getUserId () {
		return $this->_author_id;
	}
	public function getCircle () {
		if (is_null($this->_circle) && $this->_circle_id>0) {
			$this->_circle=$this->getManager()->loadCircle($this->_circle_id);
		}
		return $this->_circle;
	}
	public function getCircleId () {
		if (isset($this->_circle)) {
			$this->_circle_id=$this->_circle->getId();
		}
		return $this->_circle_id;
	}
	public function getRole () {
		if (is_null($this->_role) && $this->_role_id>0) {
			$this->_role=$this->getManager()->loadRole($this->_role_id);
		}
		return $this->_role;
	}
	public function getRoleId () {
		if (isset($this->_role)) {
			$this->_role_id=$this->_role->getId();
		}
		return $this->_role_id;
	}
	public function getLink () {
		return $this->_link;
	}
	
	public function getMeeting () {
		if (is_null($this->_meeting) && $this->_meeting_id>0) {
			$this->_meeting=$this->getManager()->loadMeeting($this->_meeting_id);
		}
		return $this->_meeting;
	}
	public function getMeetingId () {
		if (isset($this->_meeting)) {
			$this->_meeting_id=$this->_meeting->getId();
		}
		return $this->_meeting_id;
	}

	public function getTension () {
		if (is_null($this->_tension) && $this->_tension_id>0) {
			$this->_tension=$this->getManager()->loadTension($this->_tension_id);
		}
		return $this->_tension;
	}
	public function getTensionId () {
		if (isset($this->_tension)) {
			$this->_tension_id=$this->_tension->getId();
		}
		return $this->_tension_id;
	}

	public function setTitle ($title) {
		if ($this->_title!=$title) $this->setModified(true);
		$this->_title=$title;
	}
	public function setDescription ($description) {
		if ($this->_description!=$description) $this->setModified(true);
		$this->_description=$description;
	}
	public function setDate ($date) {
		$this->_date=$date;
	}
	public function setUser ($user) {
		if ($this->_author_id!=$user->getId()) $this->setModified(true);
		$this->_author=$user;
		$this->_author_id=$user->getId();
	}
	public function setUserId ($userId) {
		if ($this->_author_id!=$userId) $this->setModified(true);
		$this->_author_id=$userId;
		$this->_author=null;
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
	
	// Permet d'associer un historique a une réunion particulière
	public function setMeetingId ($meetingId) {
		if ($this->_meeting_id!=$meetingId) $this->setModified(true);
		$this->_meeting_id=$meetingId;
	}
	
	public function setMeeting ($meeting) {
		if (is_numeric($meeting)) {
			$this->_setMeetingId($meeting);
		} else {
			if ($this->_meeting!=$meeting) $this->setModified(true);
			$this->_meeting=$meeting;
		}
	}
	
	// Permet d'associer un historique a une rtension particulière
	public function setTensionId ($tensionId) {
		if ($this->_tension_id!=$tensionId) $this->setModified(true);
		$this->_tension_id=$tensionId;
	}
	
	public function setTension ($tension) {
		if (is_numeric($tension)) {
			$this->_setTensionId($tension);
		} else {
			if ($this->_tension!=$tension) $this->setModified(true);
			$this->_tension=$tension;
		}
	}
	
	public function setLink ($link) {
		if ($this->_link!=$link) $this->setModified(true);
		$this->_link=$link;
	}
	public function setParentId ($parentId) {
		if ($this->_parent_id!=$parentId) $this->setModified(true);
		$this->_parent_id=$parentId;
		$this->_parent=null;
	}
	public function setParent ($history) {
		if (is_null($this->_parent) || $this->_parent_id!=$history->getId()) {
			$this->setModified(true);
			$this->_parent=$history;
			$this->_parent_id=$history->getId();
			$history->attachChild($this);
		}
	}
	
	// Fonctions de navigation dans la hiérarchie 
	public function getParentId () {
		if (!($this->_parent_id>0) && !is_null($this->_parent)) {
			$this->_parent_id=$this->_parent->getId();
		} 
		return $this->_parent_id;
	}
	public function getParent () {
		if (is_null($this->_parent) && $this->_parent_id>0) {
			$this->_parent=self::getManager()->loadHistory($this->_parent_id);
		}
		return $this->_parent;
	}
	// Retourne la liste des enfants
	public function getChilds($donotloadDB = 0) {
		if (!$this->_childs_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadHistoryList($this->_id);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_childs)) {
					$this->_childs[]=$tmp_array[$i];
				}
			}
			$this->_childs_loaded=true;
		}
		return $this->_childs;	
	}
	// Attache un enfant à l'arborescence.
	// Si aucun objet n'est passé en paramètre, le crée et le retourne dans la fonction
	public function attachChild($history=null) {

		if (is_null($history)) { 
			$history=new \holacracy\History ($this->getManager());
			if (!in_array($history,$this->_childs, true)) $this->_childs[]=$history;
			$history->setParent($this);
			return $history;
		} else {
			if (!in_array($history,$this->_childs)) {
				$this->_childs[]=$history;
				if ($history->getParent()!=$this)
					$history->setParent($this);
			}
		}
	}
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
		// Retourne la liste des enfants à supprimer
		return $this->getChilds();
	}
	public function checkForSave() {
		if (!($this->getParentId()>0)) {
			if (!($this->getUserId()>0) ) {
				if (isset($_SESSION["currentUser"])) {
					$this->setUser($_SESSION["currentUser"]);	
					return false;
				} else {
					return "Définir le USER auteur de cette HISTORY";
				}
			}
			if (!($this->getCircleId()>0)) {
				return "Définir le CIRCLE pour cette HISTORY [".$this->getTitle()."]";
			}
		}
		return false;
	}
}
