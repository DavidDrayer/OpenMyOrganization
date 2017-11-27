<?php
	namespace holacracy;

class ActionMoi extends Holacracy
{
	private $_title;	// Nom de l'action
	private $_description;	// Description de l'action
	private $_projectId;	// Id du projet lié à l'action
	private $_roleId;		// Id du role responsable du projet
	private $_idUserFocus;	// Userfocus
	private $_statusId;		// status de l'action
	private $_timestamp;    // Timestamp de l'action
	private $_timestampdelete;    // Timestamp de delete 7 jours
	private $_insert;    	// Insert pour créer l'action
	
	// Constantes pour le status d'une action MOI
	const CURRENT_ACTION = 1;
	const BLOCKED_ACTION = 2;
	const TRIGGER_ACTION = 8;
	const DELETE_ACTION = 16; //Timestamp d'archivage 7 jours plus tard

	function getTitle() {
		return $this->_title;
	}
	
	function getStatusId() {
		return $this->_statusId;
	}
	
	function getInsert() {
		return $this->_insert;
	}
	
	function getIdUserFocus() {
		return $this->_idUserFocus;
	}
	
	function getTimeStamp() {
		return $this->_timestamp;
	}
	
	function getTimeStampDelete() {
		return $this->_timestampdelete;
	}
	
	function getDescription() {
		return $this->_description;
	}
		
	function getProjectId() {
		return $this->_projectId;
	}
	
	function getRoleId() {
		return $this->_roleId;
	}
			
	function setTitle($title) {
		$this->_title=$title;
	}
	
	function setInsert($insert) {
		$this->_insert=$insert;
	}
	
	function setIdUserFocus($idfocus) {
		$this->_idUserFocus=$idfocus;
	}
		
	function setTimeStamp($timestamp) {
		$this->_timestamp=$timestamp;
	}
	
	function setTimeStampDelete($timestampdelete) {
		if ($timestampdelete!="") {
		$this->_timestampdelete=$timestampdelete;
		}
		else{
		$timestampdelete = time() + 604800; //7 jours pour la suppression
		$this->_timestampdelete=$timestampdelete;
		}
	}
	
	function setDescription($description) {
		$this->_description=$description;
	}
	
	function setStatus($status) {
		$this->_statusId=$status;
	}
	
	function setProjectId($project) {
		$this->_projectId=$project;
	}
	
	function setRoleId($role) {
		$this->_roleId=$role;
	}

}