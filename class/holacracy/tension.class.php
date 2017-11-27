<?php
	namespace holacracy;


class Tension extends Holacracy
{
	protected $_title ;				// Titre de l'entrée d'historique
	protected $_description ;				// Titre de l'entrée d'historique
	protected $_author_id;  		// Auteur sous la forme d'un id user
	protected $_author;  			// Auteur sous la forme d'un lien sur le user
	protected $_meeting_id;  			// Type sous la forme d'un ID
	protected $_meeting;  				// Type 
	protected $_circle_id;  			// Type sous la forme d'un ID
	protected $_circle;  				// Type 
	protected $_organisation_id;  			// Type sous la forme d'un ID
	protected $_organisation;  				// Type 
	protected $_type_id;  			// Type sous la forme d'un ID
	protected $_role_id;  			// Type sous la forme d'un ID
	protected $_role;  				// Type 
	protected $_date;				// Liste des status
	protected $_checked;
	
	
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
			$this->_author=self::getManager()->loadUser($this->_author_id);
		}
		return $this->_author;
	}
	public function getUserId () {
		return $this->_author_id;
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
	public function getTypeId () {
		return $this->_type_id;
	}
	
	public function getCircle () {
		if (is_null($this->_circle) && $this->_circle_id>0) {
			$this->_circle=self::getManager()->loadCircle($this->_role_id);
		}
		return $this->_circle;
	}
	public function getCircleId () {
		return $this->_circle_id;
	}
	
	public function getOrganisation () {
		if (is_null($this->_organisation) && $this->_organisation_id>0) {
			$this->_organisation=self::getManager()->loadOrganisation($this->_role_id);
		}
		return $this->_organisation;
	}
	public function getOrganisationId () {
		return $this->_organisation_id;
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
	

	
	public function setTitle ($text) {
		if ($this->_title!=$text) $this->setModified(true);
		$this->_title=$text;
	}
	public function setDescription ($text) {
		if ($this->_description!=$text) $this->setModified(true);
		$this->_description=$text;
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
	public function setTypeId ($typeId) {
		if ($this->_type_id!=$typeId) $this->setModified(true);
		$this->_type_id=$typeId;
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

	public function setOrganisation ($organisation) {
		if ($this->_organisation_id!=$organisation->getId()) $this->setModified(true);
		$this->_organisation=$organisation;
		$this->_organisationid=$organisation->getId();
	}
	public function setOrganisationId ($id) {
		if ($this->_organisation_id!=$id) $this->setModified(true);
		$this->_organisation_id=$id;
		$this->_organisation=null;
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

	public function setRole ($role) {
		if ($this->_role_id!=$role->getId()) $this->setModified(true);
		$this->_role=$role;
		$this->_role_id=$role->getId();
	}
	public function setRoleId ($id) {
		if ($this->_role_id!=$id) $this->setModified(true);
		$this->_role_id=$id;
		$this->_role=null;
	}

	public function check ($checked) {
		if ($this->_checked!=$checked) $this->setModified(true);
		$this->_checked=$checked;
	}

	public function isChecked () {
		return $this->_checked;
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
