<?php
	namespace holacracy;


class Bug extends Holacracy
{
	protected $_title ;				// Titre de l'entrée d'historique
	protected $_description ;		// Description
	protected $_priority =0;			// Priorité
	protected $_author_id;  		// Auteur sous la forme d'un id user
	protected $_author;  			// Auteur sous la forme d'un lien sur le user
	protected $_type_id;  			// Type sous la forme d'un ID
	protected $_type;  				// Type 
	protected $_status;	// Liste des status
	protected $_status_id;	// Liste des status
	protected $_history;
	protected $_creationDate;
	
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getTitle () {
		return $this->_title;
	}
	public function getDescription () {
		return $this->_description;
	}
	public function getPriority () {
		return $this->_priority;
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
	
	public function getCreationDate() {
		return $this->_creationDate;
	}
	
	public function setCreationDate($date) {
		$this->_creationDate=$date;
	}
	
	public function getStatus () {
		if (is_null($this->_history)) {
			$this->_history=self::getManager()->loadBugStatus($this);
		}
		if (count($this->_history)>0) {
			return $this->_history[0];
		} else {
			return NULL;
		}		
	}
	
	public function getHistory () {
		if (is_null($this->_history)) {
			$this->_history=self::getManager()->loadBugStatus($this);
		}
		if (count($this->_history)>0) {
			return $this->_history;
		} else {
			return array();
		}		
	}

	public function getBugTypeId () {
		return $this->_type_id;
	}
	
	public function getBugStatus () {
		if (is_null($this->_status) && $this->_status_id>0) {
			$this->_status=self::getManager()->loadBugStatus($this->_status_id);
		}
		return $this->_status;
	}
	public function getBugStatusId () {
		if (isset($this->_status)) {
			$this->_status_id=$this->_status->getId();
		}
		return $this->_status_id;
	}
	
	public function setPriority ($priority) {
		if ($this->_priority!=$priority) $this->setModified(true);
		$this->_priority=$priority;
	}
	public function setTitle ($title) {
		if ($this->_title!=$title) $this->setModified(true);
		$this->_title=$title;
	}
	public function setDescription ($description) {
		if ($this->_description!=$description) $this->setModified(true);
		$this->_description=$description;
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

	public function setBugTypeId ($typeId) {
		if ($this->_type_id!=$typeId) $this->setModified(true);
		$this->_type_id=$typeId;
	}
	
	public function setBugStatus ($status) {
		if ($this->_status_id!=$status->getId()) $this->setModified(true);
		$this->_status=$status;
		$this->_status_id=$status->getId();
	}
	public function setBugStatusId ($statusId) {
		if ($this->_status_id!=$statusId) $this->setModified(true);
		$this->_status_id=$statusId;
		$this->_status=null;
	}
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
}
?>