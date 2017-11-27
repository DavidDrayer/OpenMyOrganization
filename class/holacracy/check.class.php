<?php
	namespace holacracy;

class Check extends Holacracy
{
	private $_role;
	private $_role_id;
	private $_user;
	private $_user_id;
	private $_acti;
	private $_acti_id;
	private $_check_date;

	// Prévu mais non implémenté: validation d'une tâche par plusieurs rôles
	public function getRole () {
		if (!isset($this->_role)) {
			if (isset($this->_role_id)) {
				$this->_role=self::getManager()->loadRole($this->_role_id);
			} 
		}
		return $this->_role;
	}
	
	public function getRoleId() {
		return $this->_role_id;
	}	
	
	public function setRole ($object) {
		if (is_object($object)) {
			$this->_role=$object;
			$this->_role_id=$object->getId();
		} else {
			$this->_role_id=$object;
		}
		$this->setModified(true);
	}
	
	public function getUser() {
		if (!isset($this->_user)) {
			if (isset($this->_user_id)) {
				$this->_user=self::getManager()->loadUser($this->_user_id);
			} 
		}
		return $this->_user;
	}
	public function getUserId() {
		return $this->_user_id;
	}
	public function setUser($object) {
		if (is_object($object)) {
			$this->_user=$object;
			$this->_user_id=$object->getId();
		} else {
			$this->_user_id=$object;
		}
		$this->setModified(true);
	}
	
	public function getAction() {
		if (!isset($this->_action)) {
			if (isset($this->_action_id)) {
				$this->_action=self::getManager()->loadActions($this->_action_id);
			} 
		}
		return $this->_action;
	}
	public function getActionId() {
		// Si l'ID est indéfini, c'est peut-être que l'objet a été créé avant la première sauvegarde de l'action
		if (!($this->_action_id>0) && isset($this->_action)) {
			$this->_action_id=$this->_action->getId();
		}
		return $this->_action_id;
	}
	public function setAction($object) {
		if (is_object($object)) {
			$this->_action=$object;
			$this->_action_id=$object->getId();
		} else {
			$this->_action_id=$object;
		}
		$this->setModified(true);
	}
	
	public function setCheckDate($date) {
		$this->_check_date=$date;
	}
	
	public function check($userId=NULL) {
		$this->_check_date=new \DateTime();
		$this->setModified(true);
	}
	
	public function getCheckDate() {
		return $this->_check_date;
	}
	
	public function isCheck() {
		return ($this->_check_date!="");
	}
	
}
