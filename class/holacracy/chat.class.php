<?php
	namespace holacracy;


class Chat extends Holacracy
{
	protected $_text ;				// Titre de l'entrée d'historique
	protected $_author_id;  		// Auteur sous la forme d'un id user
	protected $_author;  			// Auteur sous la forme d'un lien sur le user
	protected $_meeting_id;  			// Type sous la forme d'un ID
	protected $_meeting;  				// Type 
	protected $_circle_id;  			// Type sous la forme d'un ID
	protected $_circle;  				// Type 
	protected $_date;				// Liste des status
	
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getText () {
		return $this->_text;
	}
	public function getDate () {
		return $this->_date;
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
	public function getMeeting () {
		if (is_null($this->_meeting) && $this->_meeting_id>0) {
			$this->_meeting=self::getManager()->loadMeeting($this->_meeting_id);
		}
		return $this->_meeting;
	}
	public function getMeetingId () {
		return $this->_meeting_id;
	}
	public function getCircle () {
		if (is_null($this->_circle) && $this->_circle_id>0) {
			$this->_circle=self::getManager()->loadCircle($this->_circle_id);
		}
		return $this->_circle;
	}
	public function getCircleId () {
		return $this->_circle_id;
	}
	

	
	public function setText ($text) {
		if ($this->_text!=$text) $this->setModified(true);
		$this->_text=$text;
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
	
	public function setMeeting ($meeting) {
		if ($this->_meeting_id!=$meeting->getId()) $this->setModified(true);
		$this->_meeting=$meeting;
		$this->_meeting_id=$meeting->getId();
	}
	public function setMeetingId ($id) {
		if ($this->_meeting_id!=$id) $this->setModified(true);
		$this->_meeting_id=$id;
		$this->_meeting=null;
	}
	public function setCircle ($circle) {
		if ($this->_circle_id!=$circle->getId()) $this->setModified(true);
		$this->_circle=$circle;
		$this->_circle_id=$circle->getId();
	}
	public function setCircleId ($id) {
		if ($this->_circle_id!=$id) $this->setModified(true);
		$this->_circle_id=$id;
		$this->_circle=null;
	}

	public function setDate ($date) {
		$this->_date=$date;
	}
	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
}
?>
