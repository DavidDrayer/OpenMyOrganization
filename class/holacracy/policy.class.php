<?php
	namespace holacracy;


class Policy extends Holacracy
{
	protected $_title ;				// Titre de l'entrée d'historique
	protected $_description ;		// Description
	protected $_author_id;  		// Auteur sous la forme d'un id user
	protected $_author;  			// Auteur sous la forme d'un lien sur le user
	protected $_circle_id;  		// Cercle sous la forme d'un id cercle
	protected $_circle;  			// Cercle sous la forme d'un lien sur le cercle
	protected $_link;  				// Liens vers une page de complément
	
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getTitle () {
		return $this->_title;
	}
	public function getDescription () {
		return $this->_description;
	}
	public function getUser () {
		if (is_null($this->_author) && $this->_author_id>0) {
			$this->_author=self::getManager()->loadUser($this->_author_id);
		}
		return $this->_author;
	}
	public function getUserId () {
		return $this->_author_id;
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
	public function getLink () {
		return $this->_link;
	}
	
	public function setTitle ($title) {
		if ($this->_title!=$title) $this->setModified(true);
		$this->_title=$title;
	}
	public function setDescription ($description) {
		if ($this->_description!=$description) $this->setModified(true);
		$this->_description=$description;
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
	public function setLink ($link) {
		if ($this->_link!=$link) $this->setModified(true);
		$this->_link=$link;
	}
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	public function checkForSave() {
		if (!($this->getUserId()>0) ) {
			if (isset($_SESSION["currentUser"])) {
				$this->setUser($_SESSION["currentUser"]);	
				return false;
			} else {
				return "Définir le USER auteur de cette POLICY [".$this->getTitle()."]";
			}
		}
		if (!($this->getCircleId()>0)) {
			return "Définir le CIRCLE pour cette POLICY [".$this->getTitle()."]";
		}
		
		return false;
	}
}
?>