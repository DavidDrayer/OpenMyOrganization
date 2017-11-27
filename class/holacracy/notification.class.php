<?php
	namespace holacracy;


class Notification extends Holacracy
{
	protected $_title ;			// Titre du message
	protected $_html_content;  	// Contenu du message en HTML
	protected $_text_content;  	// Contenu du message en format text
	protected $_user;  			// Destinataire sous la forme d'un lien sur le user
	protected $_user_id;  		// Destinataire sous la forme d'un ID
	protected $_delay;			// Delais avant envoi, en heures
	
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getTitle () {
		return $this->_title;
	}
	public function getDelay () {
		return $this->_delay;
	}
	public function getHTMLContent () {
		return $this->_html_content;
	}
	public function getTextContent () {
		return $this->_text_content;
	}
	public function getContent () {
		if (isset($this->_html_content) && $this->_html_content!="") 
			return $this->_html_content;
		else
			return $this->_text_content;
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
	
	public function setTitle ($title) {
		if ($this->_title!=$title) $this->setModified(true);
		$this->_title=$title;
	}
	public function setDelay ($delay) {
		if ($this->_delay!=$delay) $this->setModified(true);
		$this->_delay=$delay;
	}

	public function setHTMLContent($text) {
		$this->_html_content=$text;
	}
	
	public function setTextContent($text) {
		$this->_text_content=$text;
	}
	
	public function setContent($text) {
		if ($text != strip_tags($text)) {
			$this->_html_content=$text;
		} else {
			$this->_text_content=$text;
		}
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

	
/*	public function delete() {
		//fonction de base
		parent::delete();
		$this->_modified=true;
	}*/
}
?>
