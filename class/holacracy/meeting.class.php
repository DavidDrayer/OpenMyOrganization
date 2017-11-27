<?php
	namespace holacracy;


class Meeting extends Holacracy
{
	protected $_date ;		// Date de la réunion
	protected $_startTime ;		// Heure de début de la réunion planifié
	protected $_endTime ;		// Heure de fin de la réunion planifié
	protected $_openingTime ="";		// Heure de début de la réunion réel
	protected $_closingTime ="";		// Heure de fin de la réunion réel
	protected $_location;		// Lieu de la réunion
	protected $_scratchpad;		// Scratchpad de la réunion
	protected $_scratchdate;		// Date de la dernière modif du scratchpad (pour le refresh)
	protected $_meetingType_id;	// Type de meeting
	protected $_meetingType;	// Type de meeting
	protected $_circle_id;	// ID du cercle associé
	protected $_circle;	// Cercle associé
	protected $_organisation_id;	// ID de l'organisation
	protected $_organisation;	// organisation
	protected $_secretary;
	protected $_secretary_id;
	private $_history = array();
	private $_history_loaded = false;
	private $_chat = array();
	private $_chat_loaded = false;
	private $_tension = array();
	private $_tension_loaded = false;

	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	
	public function getHistory($donotloadDB = 0) {
		if (!$this->_history_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadHistoryList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_history)) {
					$this->_history[]=$tmp_array[$i];
				}
			}
			$this->_history_loaded=true;
		}
		return $this->_history;
	}

	public function getChat($donotloadDB = 0) {
		if (!$this->_chat_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadChatList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_chat)) {
					$this->_chat[]=$tmp_array[$i];
				}
			}
			$this->_chat_loaded=true;
		}
		return $this->_chat;
	}
	
	public function addTension($tension) {
		$this->getManager()->linkTensionMeeting($tension, $this->getId());
	}

	public function getTensions($donotloadDB = 0) {
		if (!$this->_tension_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadTensionList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_tension)) {
					$this->_tension[]=$tmp_array[$i];
				}
			}
			$this->_tension_loaded=true;
		}
		return $this->_tension;
	}


	public function getSecretary() {
		if (isset($this->_secretary)) 
			return $this->_secretary;
		else {
			if ($this->_secretary_id>0) {
				$this->_secretary=self::getManager()->loadUser($this->_secretary_id);
				return $this->_secretary;
			} else {
				if ($this->getCircle()->getSecretary()->getUserId()>0) {
					return $this->getCircle()->getSecretary()->getUser();
				} else {
					//return $this->getCircle()->getLeadLink()->getUser();
				}
			}
		}
	}
	public function getSecretaryId() {
		if ($this->_secretary_id>0)
			return $this->_secretary_id;
		else {
			if ($this->getCircle()->getSecretary()->getUserId()>0) {
				return $this->getCircle()->getSecretary()->getUserId();
			} else 
			if ($this->getCircle()->getLeadLink()->getUserId()>0) {
				//return $this->getCircle()->getLeadLink()->getUserId();
			} 
		}
	}

	public function getDate () {
		return $this->_date;
	}
	
	public function getStartTime () {
		return $this->_startTime;
	}
	
	public function getEndTime () {
		return $this->_endTime;
	}
	
	public function getOpeningTime () {
		return $this->_openingTime;
	}
	
	public function getClosingTime () {
		return $this->_closingTime;
	}
	
	public function getLocation () {
		return $this->_location;
	}
	
	public function getScratchPad () {
		return $this->_scratchpad;
	}
	
	public function getScratchDate () {
		return $this->_scratchdate;
	}
	
	public function getMeetingTypeId () {
		return $this->_meetingType_id;
	}
	
	public function getMeetingType () {
		return $this->_meetingType;
	}



	public function setScratchPad ($txt) {
		$this->_scratchpad=$txt;
	}
	
	public function setScratchDate ($date) {
		$this->_scratchdate=$date;
	}
	
	
	public function setDate ($date) {
		if (is_string($date)) {
			$date=new \DateTime($date);
		}
	
		if ($this->_date!=$date) $this->setModified(true);
		$this->_date=$date;
	}
	
	public function setStartTime ($time) {
		if ($this->_startTime!=$time) $this->setModified(true);
		$this->_startTime=$time;
	}
	
	public function setEndTime ($time) {
		if ($this->_endTime!=$time) $this->setModified(true);
		$this->_endTime=$time;
	}
	
	public function setOpeningTime ($time) {
		if ($this->_openingTime!=$time) $this->setModified(true);
		$this->_openingTime=$time;
	}
	
	public function setClosingTime ($time) {
		if ($this->_closingTime!=$time) $this->setModified(true);
		$this->_closingTime=$time;
	}
	
	public function setLocation ($location) {
		if ($this->_location!=$location) $this->setModified(true);
		$this->_location=$location;
	}
	
	public function setMeetingTypeId ($meetingTypeId) {
		if ($this->_meetingType_id!=$meetingTypeId) $this->setModified(true);
		$this->_meetingType_id=$meetingTypeId;
	}
	
	public function setMeetingType ($meetingType) {
		if (is_numeric($meetingType)) {
			$this->_setMeetingTypeId($meetingType);
		} else {
			if ($this->_meetingType!=$meetingType) $this->setModified(true);
			$this->_meetingType=$meetingType;
		}
	}

	public function setSecretaryId ($id) {
		if ($this->_secretary_id!=$id) $this->setModified(true);
		$this->_secretary_id=$id;
	}
	
	public function setSecretary ($user) {
		if (is_numeric($meetingType)) {
			$this->_setSecretaryId($user);
		} else {
			if ($this->_secretary!=$user) $this->setModified(true);
			$this->_secretary=$user;
		}
	}

	public function setOrganisation($object) {
		if (is_object($object)) {
			$this->_organisation=$object;
			$this->_organisation_id=$object->getId();
		} else {
			$this->_organisation_id=$object;
		}
	}
	
	public function getOrganisationId() {
		return $this->_organisation_id;
	}

	public function getOrganisation() {
		if (!isset($this->_organisation)) {
			if (isset($this->_organisation_id)) {
				$this->_organisation=self::getManager()->loadOrganisation($this->_organisation_id);
			} else {
				if ($this->getCircle()!="") {
					$this->_organisation=$this->getCircle()->getOrganisation();
				}
			}
		}
		return $this->_organisation;
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
	
	public function setCircle($circle) {
		if (is_numeric($circle)) {
			$this->_circle_id=$circle;	
		} else {
			$this->setManager($circle->getManager());
			$this->_circle=$circle;
			$this->_circle_id=$circle->getId();
		}
	}
		
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	public function checkForSave() {
		//if ($this->getLabel()=="") {
		//	return "Définir le LABEL pour cette RECURRENCE [".$this->getLabel()."]";
		//}
		return false;
	}
}
?>
