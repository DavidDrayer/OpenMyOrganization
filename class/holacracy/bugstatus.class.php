<?php
	namespace holacracy;


class BugStatus extends Holacracy
{
	protected $_user_id;  		// Auteur sous la forme d'un id user
	protected $_user;  			// Auteur sous la forme d'un lien sur le user
	protected $_label;  			// Label
	protected $_comment;  			// Commentaire
	protected $_date;  				// Date 
	protected $_bug;  				// Bug sous forme d'objet
	protected $_bug_id;  			// Bug sous forme d'ID
	
	
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getLabel () {
		return $this->_label;
	}
	public function getComment () {
		return $this->_comment;
	}
	public function getDate () {
		return $this->_date;
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
	public function getBug () {
		if (is_null($this->_bug) && $this->_bug_id>0) {
			$this->_bug=self::getManager()->loadBug($this->_bug_id);
		}
		return $this->_bug;
	}
	public function getBugId () {
		return $this->_bug_id;
	}

	public function setLabel ($label) {
		$this->_label=$label;
	}
	public function setComment ($comment) {
		$this->_comment=$comment;
	}
	public function setDate ($date) {
		$this->_date=$date;
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
	public function setBug ($bug) {
		if ($this->_bug_id!=$bug->getId()) $this->setModified(true);
		$this->_bug=$bug;
		$this->_bug_id=$bug->getId();
	}
	public function setBugId ($bugId) {
		if ($this->_bug_id!=$bugId) $this->setModified(true);
		$this->_bug_id=$bugId;
		$this->_bug=null;
	}
}
?>