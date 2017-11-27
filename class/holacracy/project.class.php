<?php
	namespace holacracy;

class Project extends Holacracy
{
	private $_title ="Empty project";	// Nom du Projet
	private $_description;	// Description du Projet
	private $_role;			// Role responsable du projet
	private $_roleId;		// Id du role responsable du projet
	private $_user;			// Personne a qui est attribué le projet
	private $_userId;		// Id de la personne a qui est attribué le projet
	private $_statusId;		// status du projet
	private $_status;		// status du projet
	private $_position;		// Ordre de classement
	private $_typeId;			// type du projet
	private $_type;				// type du projet
	private $_dateCreation;		// date de création du projet
	private $_dateModif;		// date de la mise à jour du projet
	private $_dateStatus;		// date du dernier changement de status du projet
	private $_comments=array();			// Liste des commentaires
	private $_comments_loaded=false;	// Déjà chargé depuis le manager?
	private $_actions = array();		// Liste des actions
	private $_actionsMoi = array();		// Liste des actions - OBSOLETE
	private $_importantList = array();	// Liste des user trouvant ce projet important
	private $_visibility = 3; 			// Niveau de visibilité du projet
	private $_showcircle = FALSE;		// Partagé dans le cercle supérieur?
	private $_user_id_proposer;
	private $_user_proposer;
	private $_role_proposer;
	private $_role_id_proposer;
	
	// Variables statiques
	static private $_allStatus = array(); // Liste des différents status partagés entre tous les projets


	
	// Constantes pour le status du projet (en cours, en attente, fini, etc...)
	const CURRENT_PROJECT = 1;
	const BLOCKED_PROJECT = 2;
	const DELAYED_PROJECT = 8;
	const FINISHED_PROJECT = 4;
	const PROPOSED_PROJECT = 16;
	const REFUSED_PROJECT = 32;
	const ARCHIVED_PROJECT = 64;
	
	const ACTIVEMOI_PROJECTS = 7;
	const ACTIVE_PROJECTS = 15;
	const ALL_PROJECTS = 255;
	
	// Constantes pour le type de projet
	const PROJECT = 1;
	const ACTION = 2;
	
	static function getAllStatus($filter=self::ACTIVE_PROJECTS) {
		if (count(self::$_allStatus)==0) {
			self::$_allStatus=self::getManager()->loadStatus();	
		}
		$returnArray=Array();
		foreach(self::$_allStatus as $status) {
			if (($status->getId() & $filter) >0) $returnArray[]=$status;
		}
		return $returnArray;
		//return array(self::NEW_PROJECT,self::CURRENT_PROJECT,self::DELAYED_PROJECT,self::FINISHED_PROJECT);
	}

	function setProposer($user, $role=NULL) {
		if (!is_null($role)) {
			if (is_numeric($user)) {
				$this->_user_id_proposer=$user;
			} else {
				$this->_user_proposer=$user;
				$this->_user_id_proposer=$user->getId();
			}
		}
		if (!is_null($role) && $role!="") {
			if (is_numeric($role)) {
				$this->_role_id_proposer=$role;
			} else {
				$this->_role_proposer=$role;
				$this->_role_id_proposer=$role->getId();
			}		
		}
	}
	
	function getProposer() {
		if (!is_null($this->_user_proposer)) {
			return $this->_user_proposer;
		} else if (!is_null($this->_user_id_proposer)) {
			$this->_user_proposer= $this->getManager()->loadUser($this->_user_id_proposer);
			return $this->_user_proposer;
		}		
	}
	function getProposerId() {
		return $this->_user_id_proposer;
	}
	
	function getProposerRole() {
		if (!is_null($this->_role_proposer)) {
			return $this->_role_proposer;
		} else if (!is_null($this->_role_id_proposer)) {
			$this->_role_proposer= $this->getManager()->loadRole($this->_role_id_proposer);
			return $this->_role_proposer;
		}			
	}
	function getProposerRoleId() {
		return $this->_role_id_proposer;
		
	}

	function getTitle() {
		return $this->_title;
	}
	function getPosition() {
		return $this->_position;
	}
	
	function setPosition($pos) {
		$this->_position=$pos;
	}
	
	function getVisibility() {
		return $this->_visibility;
	}
	
	function setVisibility($visibility) {
		$this->_visibility=$visibility;
	}
	
	function isShowCircle() {
		return $this->_showcircle;
	}
	
	function setShowCircle($showcircle) {
		$this->_showcircle=$showcircle;
	}
	
	function getStatusId() {
		return $this->_statusId;
	}
	
	function getStatus() {
		return $this->_status;
	}

	function getDescription() {
		return $this->_description;
	}
	
	public function getComments($donotloadDB = 0) {
		if (!$this->_comments_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadCommentList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_comments)) {
					$this->_comments[]=$tmp_array[$i];
				}
			}
			$this->_comments_loaded=true;
		}
		return $this->_comments;	
	}
	
	function getRoleId() {
		return $this->_roleId;
	}

	function getUserId() {
		return $this->_userId;
	}
	

	function getCreationDate() {
		return $this->_dateCreation;
	}
	
	function getModificationDate() {

			return $this->_dateModif;
	}
	
	function getStatusDate() {

			return $this->_dateStatus;

	}
	
	function getImportantList() {
		if (count($this->_importantList)==0 && self::getManager()) {
			$this->_importantList=self::getManager()->loadImportantList($this);
		}
		return $this->_importantList;
	}
	
	function setImportant($status, $user) {
		if (self::getManager()) {
			self::getManager()->setImportant($status, $this, $user);
		}
	}

	function getUser() {
		if (!is_null($this->_user)) {
			return $this->_user;
		} else if (!is_null($this->_userId)) {
			$this->_user=$this->getManager()->loadUser($this->_userId);
			return $this->_user;
		}
	}
	function getRole() {
		if (!is_null($this->_role)) {
			return $this->_role;
		} else if (!is_null($this->_roleId)) {
			$this->_role=$this->getManager()->loadRole($this->_roleId);
			return $this->_role;
		}
	}
	

	function setUser($user) {
		if (is_null($user)) {
			$this->_user=$user;
			$this->_userId=$user;
		} else
		if ($user!="") {
			if (is_numeric($user)) {
				$this->_userId=$user;
			} else {
				$this->_user=$user;
				$this->_userId=$user->getId();
			}
		} else {
			$this->_user=NULL;
			$this->_userId=NULL;			
		}
	}
	
	function getTypeId() {
		return $this->_typeId;
	}
	
	// Charge les nouvelles actions
	public function getActions($filter=\holacracy\Action::ALL_ACTION) {
		if (count($this->_actions)==0 && self::getManager()) {
			$this->_actions=self::getManager()->loadActions($this);
		}
		if ($filter==\holacracy\Action::ALL_ACTION) {
			return $this->_actions;
		} else {
			$array=array();
			foreach ($this->_actions as $action) {
				if (!$action->isCheck()) $array[]=$action;
			}
			return $array;
		}
	}
	
	// ******************************************************
	// Ancienne version - obsolète
	public function getActionsMoi ($filter=NULL) {
		if (count($this->_actionsMoi)==0 && self::getManager()) {
			$this->_actionsMoi=self::getManager()->loadActionsMoi($this);
		}
		if (is_null($filter)) {
			return $this->_actionsMoi;

		} else {

			$array=array();
			foreach($this->_actionsMoi as $actionMoi) {
				if ($actionMoi->getStatusId() & $filter)
				$array[]=$actionMoi;
			}
			return $array;

		}	
	}
	
	function setTypeId($id) {
		$this->setType($id);
	}
	
	function setType($type) {
		if (is_null($type)) {
			$this->_type=NULL;
			$this->_typeId=NULL;
		} else
		if ($type!="") {
			if (is_numeric($type)) {
				$this->_typeId=$type;
			} else {
				$this->_type=$type;
				$this->_typeId=$type->getId();
			}
		} else {
			$this->_type=NULL;
			$this->_typeId=NULL;			
		}
	}
	
	function setTitle($title) {
		$this->_title=$title;
	}
	
	function setDescription($description) {
		$this->_description=$description;
	}
	
	function setStatus($status) {
		if (is_numeric($status)) {
			$this->_statusId=$status;
		} else {
			$this->_status=$status;
		}
	}

	function setCreationDate($date) {
		$this->_dateCreation=$date;
	}
	
	function setModificationDate($date) {
		$this->_dateModif=$date;
	}
	
	function setStatusDate($date) {
		$this->_dateStatus=$date;
	}	
	function setRoleId($role) {
		$this->_roleId=$role;
	}
	function setRole($role) {
		if (is_numeric($role)) {
			$this->_roleId=$role;
		} else {
			$this->_role=$role;
			$this->_roleId=$role->getId();
		}
	}
}
