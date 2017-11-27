<?php
	namespace holacracy;


class Comment extends Holacracy
{
	private $_description ;		// Description
	private $_author_id;  		// Auteur sous la forme d'un id user
	private $_author;  			// Auteur sous la forme d'un lien sur le user
	private $_modifier_id;  		// Cercle sous la forme d'un id cercle
	private $_modifier;  			// Cercle sous la forme d'un lien sur le cercle
	private $_modificationDate;  				// Liens vers une page de complément
	private $_creationDate;  				// Liens vers une page de complément
	private $_project;
	private $_project_id;
	private $_tt;
	private $_tr;
	private $_tt_unite;
	private $_tr_unite;
	
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getDescription () {
		return $this->_description;
	}
	// SET et GET pour les temps travaillés et temps restants
	public function getTT() {
		return $this->_tt;
	}
	public function getTR() {
		return $this->_tr;
	}
	public function setTT($val) {
		$this->_tt=$val;
	}
	public function setTR($val) {
		$this->_tr=$val;
	}
	// SET et GET pour les unite de temps travaillés et de temps restant
	public function getTTUnite() {
		return $this->_tt_unite;
	}
	public function getTRUnite() {
		return $this->_tr_unite;
	}
	public function setTTUnite($val) {
		$this->_tt_unite=$val;
	}
	public function setTRUnite($val) {
		$this->_tr_unite=$val;
	}
	
	public function getProject () {
		if (is_null($this->_project) && $this->_project_id>0) {
			$this->_project=self::getManager()->loadUser($this->_project_id);
		}
		return $this->_project;
	}
	public function getProjectId () {
		return $this->_project_id;
	}
	public function getAuthor () {
		if (is_null($this->_author) && $this->_author_id>0) {
			$this->_author=self::getManager()->loadUser($this->_author_id);
		}
		return $this->_author;
	}
	public function getAuthorId () {
		return $this->_author_id;
	}
	public function getModifier () {
		if (is_null($this->_modifier) && $this->_modifier_id>0) {
			$this->_modifier=self::getManager()->loadUser($this->_modifier_id);
		}
		return $this->_modifier;
	}
	public function getModifierId () {
		if (isset($this->_modifier)) {
			$this->_modifier_id=$this->_modifier->getId();
		}
		return $this->_modifier_id;
	}

	public function setDescription ($description) {
		if ($this->_description!=$description) $this->setModified(true);
		$this->_description=$description;
	}
	public function setCreationDate($date) {
		$this->_creationDate=$date;
	}
	public function setModificationDate($date) {
		$this->_modificationDate=$date;
	}
	public function getCreationDate() {
		return $this->_creationDate;
	}
	public function getModificationDate() {
		return $this->_modificationDate;
	}

	public function setProject ($project) {
		if ($this->_project_id!=$project->getId()) $this->setModified(true);
		$this->_project=$project;
		$this->_project_id=$user->getId();
	}
	public function setProjectId ($projectId) {
		if ($this->_project_id!=$projectId) $this->setModified(true);
		$this->_project_id=$projectId;
		$this->_project=null;
	}
	public function setAuthor ($user) {
		if ($this->_author_id!=$user->getId()) $this->setModified(true);
		$this->_author=$user;
		$this->_author_id=$user->getId();
	}
	public function setAuthorId ($userId) {
		if ($this->_author_id!=$userId) $this->setModified(true);
		$this->_author_id=$userId;
		$this->_author=null;
	}
	public function setModifier ($user) {
		if ($this->_modifier_id!=$user->getId()) $this->setModified(true);
		$this->_modifier=$user;
		$this->_modifier_id=$user->getId();
	}
	public function setModifierId ($userId) {
		if ($this->_modifier_id!=$userId) $this->setModified(true);
		$this->_modifier_id=$userId;
		$this->_modifier=null;
	}

	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	public function checkForSave() {
		if (!($this->getAuthorId()>0) ) {
			if (isset($_SESSION["currentUser"])) {
				$this->setAuthor($_SESSION["currentUser"]);	
				return false;
			} else {
				return "Définir le USER auteur de ce COMMENT [".$this->getTitle()."]";
			}
		}
		
		return false;
	}
}
?>
