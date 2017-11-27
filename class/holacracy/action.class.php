<?php
	namespace holacracy;

class Action extends Holacracy
{
	private $_title;	// Nom de l'action
	private $_description;	// Description de l'action
	private $_role;
	private $_role_id;
	private $_proposer;
	private $_proposer_id;
	private $_proposer_role;
	private $_proposer_role_id;
	private $_circle;
	private $_circle_id;
	private $_project;
	private $_project_id;
	private $_creation_date;
	private $_delete_date;
	private $_check_date;
	private $_check_list=array();
	
	const ALL_ACTION = 1;
	const CHECKED_ACTION = 2;
	const UNCHECKED_ACTION = 4;


	public function addUser($object) {
		// Crée un nouvel élément de class check
		$check=new \holacracy\Check();
		// Y intègre les données
		$check->setAction($this);
		$check->setUser($object);
		// rajoute à la liste pour la sauvegarde
		$this->_check_list[]=$check;
	}

	public function setProposer($object) {
		if (is_object($object)) {
			$this->_proposer=$object;
			$this->_proposer_id=$object->getId();
		} else {
			$this->_proposer_id=$object;
		}
	}
	
	public function getProposerId() {
		return $this->_proposer_id;
	}
	
	public function getProposer() {
		if (!isset($this->_proposer)) {
			if (isset($this->_proposer_id)) {
				$this->_proposer=self::getManager()->loadUser($this->_proposer_id);
			} 
		}
		return $this->_proposer;
	}
	
	public function setProposerRole($object) {
		if (is_object($object)) {
			$this->_proposer_role=$object;
			$this->_proposer_role_id=$object->getId();
		} else {
			$this->_proposer_role_id=$object;
		}	
	}
	
	public function getProposerRoleId() {
		return $this->_proposer_role_id;
	}
	
	public function getProposerRole() {
		if (!isset($this->_proposer_role)) {
			if (isset($this->_proposer_role_id)) {
				$this->_proposer_role=self::getManager()->loadRole($this->_proposer_role_id);
			} 
		}
		return $this->_proposer_role;
	}
	
	
	public function getTitle() {
		return $this->_title;
	}
	
	public function getDescription () {
		return $this->_description;
	}
	
	public function setTitle($title) {
		$this->_title=$title;
	}
	
	public function setDescription ($description) {
		$this->_description=$description;
	}

	public function setRole ($object) {
		if (is_object($object)) {
			$this->_role=$object;
			$this->_role_id=$object->getId();
		} else {
			$this->_role_id=$object;
		}
	}
	
	public function setCircle($object) {
		if (is_object($object)) {
			$this->_circle=$object;
			$this->_circle_id=$object->getId();
		} else {
			$this->_circle_id=$object;
		}
	}
	
	public function setProject($object) {
		if (is_object($object)) {
			$this->_project=$object;
			$this->_project_id=$object->getId();
		} else {
			$this->_project_id=$object;
		}
	}
	
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
	
	public function getCircle() {
		if (!isset($this->_circle)) {
			if (isset($this->_circle_id)) {
				$this->_circle=self::getManager()->loadCircle($this->_circle_id);
			} 
		}
		return $this->_circle;
	}
	
	public function getCircleId() {
		return $this->_circle_id;
	}
	
	public function getProject() {
		if (!isset($this->_project)) {
			if (isset($this->_project_id)) {
				$this->_project=self::getManager()->loadProjects($this->_project_id);
			} 
		}
		return $this->_project;
	}
	
	public function getProjectId() {
		return $this->_project_id;
	}

	public function setDeleteDate($date) {
		$this->_delete_date=$date;
	}
	public function getDeleteDate() {
		return $this->_delete_date;
	}
		
	public function delete($bool=true) {
		$this->_delete_date=getdate();
	}
	
	public function isDelete() {
		return (isset($this->_delete_date) && $this->_delete_date!="");
	}
	
	public function setCheckDate($date) {
		$this->_check_date=$date;
	}
	
	public function check($user=NULL) {
		// Y a-t-il une liste?
		$liste=$this->getCheckList();
		
		if (count($liste)>0) {
			foreach ($liste as $elem) {
				if ($user!=NULL) {
					// On check seulement l'utilisateur courant
					if ($elem->getUserId()==$user->getId()) {
						$elem->check();
						$this->setModified(true);
					}
				} else {
					// On check tout
					$elem->check();
					$this->setModified(true);
					echo "2";
				}	
			} 
		} else {
			// Sinon on s'en fout, on check
			$this->_check_date=new \DateTime();
			$this->setModified(true);
		}
	}
	
	public function getChilds() {
		return $this->getCheckList();
	}
	
	public function getCheckDate() {
		if (isset($this->_check_date)) {
			return $this->_check_date;
		} else {
			// Parcours la liste et récupère la date la plus récente
			$liste=$this->getCheckList();
			$date=NULL;
			if (count($liste)>0) {
				foreach ($liste as $elem) {
					if (!is_null($elem->getCheckDate())) {
						if (is_null($date) || $elem->getCheckDate()>$date) {
							$date=$elem->getCheckDate();
						}
					}
				}
			}
			if (!is_null($date)) return $date;
		}
	}
	
	public function isCheck($user=null) {
		// Est-ce une liste de check?
		$liste=$this->getCheckList();
		if (count($liste)>0) {
			foreach ($liste as $elem) {
				if (isset($user)) {
					if ($elem->getUserId()==$user->getId() && !$elem->isCheck()) {
						return false;
					}	
				} else {
					if (!$elem->isCheck()) {
						return false;
					}
				}
				
			}
		} else {
			return ($this->_check_date!="");
		}
		return true;
	}
	
	// A implémenter pour connaitre le nombre de personnes ayant accompli la tâche
	public function getCheckCount($value=true) {
		$count=0;
		$liste=$this->getCheckList();
		if (count($liste)>0) {
			foreach ($liste as $elem) {
				if ($elem->isCheck()==$value) $count+=1;
			}
		}
		return $count;
	}
	
	public function setCreationDate($date) {
		$this->_creation_date=$date;
	}	
	
	public function getCreationDate() {
		return $this->_creation_date;
	}
	
	public function getCheckList() {
		if (count($this->_check_list)==0 && self::getManager()) {
			$this->_check_list=self::getManager()->loadActionChecks($this);
		}
		return $this->_check_list;	
	}
	
	public function isForUser($user) {
		// Récupère l'ID du User
		if (is_object($user)) {
			$id=$user->getId();
		} else {
			$id=$user;
		}
		$checks=$this->getCheckList();
		// Y a-t-il une attente pour ce user particulier?
		foreach($checks as $check) {
			if ($check->getUserId()==$id) {
				return true;
			}
		}
		// Sinon, l'utilisateur rempli-t-il un rôle associé à cette action ?
		if ($this->getRoleId()>0 && $user->isRole($this->getRole())) return true;
		
		// Sinon, l'action est-elle associée à un projet dont le user est en charge?
		if ($this->getProjectId()>0) {
			if ($this->getProject()->getUserId()==$id)  return true;
			if (!($this->getProject()->getUserId()>0) && $this->getProject()->getRole()->getUserId()==$id) return true;
		}
		

		
		
		// Alors non, pas pour lui;
		return false;
	}
}
?>
