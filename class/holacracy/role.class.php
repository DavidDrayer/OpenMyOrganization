<?php
	namespace holacracy;

class Role extends Holacracy
{
	private $_name ="Empty role";						// Nom du Rôle
	private $_purpose = "";      	// Mission du Role 
	protected $_superCircleID;					// ID du Cercle supérieur
	private $_type = 1;					// Type du role
	private $_roleFillers = array();
	private $_projects = array();		// Liste des projets
	private $_accountabilities = array();
	private $_scopes = array();					// Périmètres du Rôle
	private $_active = 1;					// Role actif?
	private $_organisation;
	private $_organisation_id;
	private $_master;			// Cercle ayant créé ce role, si différent du super-cercle - DDr, 29.8.2014
	private $_master_id;
	private $_source;		// Role lié dans un autre cercle, pour les liens transverse - DDr, 29.8.2014
	private $_source_id;
	private $_sourceCircle;		// Cercle lié dans un autre cercle, pour les liens transverse - DDr, 29.8.2014
	private $_sourceCircle_id;
	private $_actionsMoi = array();		// Liste des actions
	private $_actions = array();		// Liste des actions
	private $_sourceUser;	// User en charge d'un lien transverse
	private $_sourceUser_id;	// User en charge d'un lien transverse
	private $_metrics_loaded = false;
	private $_metrics = array();
	private $_metric_loaded = false;
	private $_metric = array();
	private $_documents_loaded = false;
	private $_documents = array();
	private $_checklists_loaded = false;
	private $_checklists = array();
	private $_history = array();
	private $_history_loaded = false;
	private $_checklist = array();
	private $_checklist_loaded = false;

	
	// Constantes pour le type de role (structurel ou non, élu ou non, etc...)
	const STANDARD_ROLE = 1;
	const LEAD_LINK_ROLE = 2;
	const REP_LINK_ROLE = 4;
	const SECRETARY_ROLE = 8;
	const FACILITATOR_ROLE = 16;
	const CIRCLE = 32;
	const LINK_ROLE = 64;
	
	const STRUCTURAL_ROLES = 30;		
	
	// Constructeur lié à la base de donnée, et initialisé à partir de la base de donnée
	public function _construct7 ($id, $manager, $name, $purpose, $superCircle, $type, $roleFiller) {
		self::setManager($manager);
		$this->_id=$id;
		$this->_name=$name;
		$this->_purpose=$purpose;
		$this->_superCircleID=$superCircle->getId();
		$this->_type=$type;
		$this->_roleFillers=$roleFiller;
	}

	public function delete() {
		$this->setActive(0);
		self::getManager()->save($this);
		return $this;
	}
	
	public function restore() {
		$this->setActive(1);
		self::getManager()->save($this);
		return $this->_id;
	}
	
	public function getAllMetrics($donotloadDB = 0) {

		if (!$this->_metric_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadMetricList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_metric)) {
					$this->_metric[]=$tmp_array[$i];
				}
			}
			$this->_metric_loaded=true;
		}
		return $this->_metric;
	}
	

	public function getChecklist($donotloadDB = 0) {
		if (!$this->_checklist_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadChecklistList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_checklist)) {
					$this->_checklist[]=$tmp_array[$i];
				}
			}
			$this->_checklist_loaded=true;
		}
		return $this->_checklist;
	}
	
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
	
	public function getMetrics($donotloadDB = 0) {
		
		if (!$this->_metrics_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadMetrics($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_metrics)) {
					$this->_metrics[]=$tmp_array[$i];
				}
			}
			$this->_metrics_loaded=true;
		}
		return $this->_metrics;
	}
	
	public function addDocument($document) {
	}
	
	public function getDocuments($donotloadDB = 0) {
		if (!$this->_documents_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadDocuments($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_documents)) {
					$this->_documents[]=$tmp_array[$i];
				}
			}
			$this->_documents_loaded=true;
		}
		return $this->_documents;
	}

	public function getCheckLists($donotloadDB = 0) {
		if (!$this->_checklists_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadCheckLists($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_checklists)) {
					$this->_checklists[]=$tmp_array[$i];
				}
			}
			$this->_checklists_loaded=true;
		}
		return $this->_checklists;
	}

	
	public function attachAccountability($accountability) {
		$accountability->setRole($this);
	}
	// Liste des GETTER, retopurnant les valeurs des propriétés privées
	public function getName () {
		if ($this->getSourceId()!="") {
			return $this->getSource()->_name;
		} else
			return $this->_name;
	}
	
	public function getPurpose () {
		if ($this->getType()==\holacracy\Role::LEAD_LINK_ROLE) {
			return $this->getSuperCircle()->getPurpose();
		} else {
			if ($this->getSourceId()!="") {
				return $this->getSource()->_purpose;
			} else
			if ($this->getSourceCircleId()!="") {
				return $this->getSourceCircle()->_purpose;
			} else
			if (!$this->getSuperCircleId()>0) {
				return $this->getOrganisation()->getPurpose();
			} else {
				return $this->_purpose;
			}
		}
	}
	
	public function getScopes () {
		if (count($this->_scopes)==0 && self::getManager()) {
			$this->_scopes=self::getManager()->loadScopes($this);
		}
		if ($this->getSourceId()!="") {
			$return_array= $this->getSource()->getScopes();
		} else			
		if ($this->getSourceCircleId()!="") {
			$return_array= $this->getSourceCircle()->getScopes();
		} else		
			$return_array=array();
		foreach($this->_scopes as $scope) {
				$return_array[] = $scope;
			
		}
		return $return_array;

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
		if ($this->_organisation_id>0)
			return $this->_organisation_id;
		else
			if ($this->getSuperCircle()!="") {
				return $this->getSuperCircle()->getOrganisationId();
			}
	}
	
	public function getOrganisation() {
		if (!isset($this->_organisation)) {
			if (isset($this->_organisation_id) && $this->_organisation_id>0) {
				$this->_organisation=self::getManager()->loadOrganisation($this->_organisation_id);
			} else {
				if ($this->getSuperCircle()!="") {
					$this->_organisation=$this->getSuperCircle()->getOrganisation();
				}
			}
		}
		return $this->_organisation;
	}

	public function hasSuperCircle () {
		return !empty($this->_superCircleID);
	}
	public function getSuperCircle () {
		if ($this->hasSuperCircle()) {
			return self::getManager()->loadCircle($this->_superCircleID);
		} else {
			return null;
		}
	}
	
	public function getSuperCircleId () {
			return $this->_superCircleID;
	}

	public function getRoleFillers () {
		// Si c'est le premier lien, retourne la liste du cercle
		if ($this->getType()==\holacracy\Role::LEAD_LINK_ROLE && null!==$this->getSuperCircle()) {
			return $this->getSuperCircle()->getRoleFillers();
		} else {
	
			if (count($this->_roleFillers)==0 && self::getManager()) {
				$this->_roleFillers=self::getManager()->loadRoleFillers($this->_id);
			}
			$return_array=array();
			foreach($this->_roleFillers as $roleFiller) {
					$return_array[] = $roleFiller;
				
			}
			return $return_array;		
		}
	}

	// Charge la liste des projets associés à un rôle
	public function getProjects ($filter=\holacracy\Project::ACTIVE_PROJECTS,$user=NULL) {
		if (count($this->_projects)==0 && self::getManager()) {
			if (isset($user) && !is_null($user)){ 
				if (is_object($user)) {
				$this->_projects=self::getManager()->loadProjects($this,$user->getId());
				}
				else{
				$this->_projects=self::getManager()->loadProjects($this,$user);
				}		
			}
			else{
			$this->_projects=self::getManager()->loadProjects($this);
			}
		}
		$return_array=array();
		foreach($this->_projects as $project) {
			// Plus tard, uniquement ceux correspondant au filtre
			if (($project->getStatusId() & intval($filter))>0) {
				$return_array[] = $project;
			}
		}
		return $return_array;		
	}
	
	// Charge la liste des actions associées à un role
	public function getActionsMoi ($user=NULL) {
		if (count($this->_actionsMoi)==0 && self::getManager()) {
			if (!is_null($user)) { //Si plusieurs focus
			$this->_actionsMoi=self::getManager()->loadActionsMoi($this,$user);
			} else { //Si un seul focus
			$this->_actionsMoi=self::getManager()->loadActionsMoi($this);
			}
		}
		return $this->_actionsMoi;		
	}
	
		// Charge la liste des actions associées à un role
	public function getActions ($user=NULL) {
		if (count($this->_actions)==0 && self::getManager()) {
			if (!is_null($user)) { //Si plusieurs focus
			$this->_actions=self::getManager()->loadActions($this,$user);
			} else { //Si un seul focus
			$this->_actions=self::getManager()->loadActions($this);
			}
		}
		return $this->_actions;		
	}

	public function getAccountabilities () {
		if (count($this->_accountabilities)==0 && self::getManager()) {
			$this->_accountabilities=self::getManager()->loadAccountabilities($this->_id);
		}
		if ($this->getSourceId()!="") {
			$return_array= $this->getSource()->getAccountabilities();
		} else
		if ($this->getSourceCircleId()!="") {
			$return_array= $this->getSourceCircle()->getAccountabilities();
		} else
			$return_array=array();
		foreach($this->_accountabilities as $accountability) {
			$return_array[] = $accountability;
		}
		return $return_array;		
	}

	public function getType () {
		return $this->_type;
	}
	
	public function toString () {
			if ($this->getSourceCircleId()!="") {
				return ""."Lien transverse"." de [".$this->getSourceCircle()->_name."]";
			
		} else return $this->getName();
	}
    // Retourne une chaîne HTML avec un lien sur l'élément (obsolète)
	public function toHTMLString () {
		// Lien transverse
		if ($this->getSourceCircleId()!="") {
			// Role ???
			if ($this->getSourceId()!="") {
				return "<a href='role.php?id=".$this->_id."'>".$this->getSource()->_name."</a>".", lien transverse de [<a href='circle.php?id=".$this->getSourceCircle()->getId()."'>".$this->getSourceCircle()->_name."</a>]";
			} else {
				// Cercle ???
				return "<a href='role.php?id=".$this->_id."'>".$this->getSourceCircle()->_name."</a>".", lien transverse";
			}
		} else
			return "<a href='role.php?id=".$this->_id."'>".$this->toString()."</a>";
	}
	
	// Dans le cas où c'est un super-cercle qui crée un role dans un sous-cercle: permet de se souvenir de qui l'a créé
	public function setMaster($circle) {
		if (is_object($circle)) {
			$this->_master=$circle;
			$this->_master_id=$circle->getId();
		} else {
			$this->_master_id=$circle;
		}
	}
	public function setMasterId($id) {
		$this->setMaster($id);
	}
	public function getMaster() {
		if (!isset($this->_master)) {
			if (isset($this->_master_id)) {
				$this->_master=self::getManager()->loadCircle($this->_master_id);
			} 
		}
		return $this->_master;
	}
	public function getMasterId() {
		if (!isset($this->_master_id)) {
			if (isset($this->_master)) {
				$this->_master_id=$this->_master->getId();
			} 
		}
		return $this->_master_id;
	}
	
	
	// Uniquement pour les liens transverse, défini le role source
	public function setSource ($role) {
		if (is_object($role)) {
			$this->_source=$role;
			$this->_source_id=$role->getId();
		} else {
			$this->_source_id=$role;
		}
	}
	public function setSourceId ($id) {
		$this->setSource($id);
	}
	public function getSource() {
		if (!isset($this->_source)) {
			if (isset($this->_source_id)) {
				$this->_source=self::getManager()->loadRole($this->_source_id);
			} 
		}
		return $this->_source;
	}
	public function getSourceId() {
		if (!isset($this->_source_id)) {
			if (isset($this->_source)) {
				$this->_source_id=$this->_source->getId();
			} 
		}
		return $this->_source_id;
	}
	
	// Uniquement pour les liens transverse, défini le role source
	public function setSourceCircle ($circle) {
		if (is_object($circle)) {
			$this->_sourceCircle=$circle;
			$this->_sourceCircle_id=$circle->getId();
		} else {
			$this->_sourceCircle_id=$circle;
		}
	}
	
	public function setUserId ($id) {
	//	if ($this->getSourceId()>0 && $this->getSourceCircleId()>0) {
			// Attention à ce qui doit être fait ici... dans certain cas, il n'est peut-être pas souhaitable de pouvoir définir l'ID de la personne en charge, comme dans le cas de liens transverses
	//	} else {
			$this->setUser($id);
	//	}
	}
	public function getUserId () {
		// Premier lien, retourne le user d'au-dessus
		if ($this->_type==\holacracy\Role::LEAD_LINK_ROLE && null!==$this->getSuperCircle()) {
			return $this->getSuperCircle()->getUserId();
		} else
		if ($this->getSourceCircleId()>0 && $this->getSourceCircle()->getUserId()>0) {
				return $this->getSourceCircle()->getUserId();
			} else		
			if ($this->getSourceId()>0 && $this->getSource()->getUserId()>0) {
				return $this->getSource()->getUserId();
			} else
		if ($this->_sourceUser_id>0) {
			return $this->_sourceUser_id;
		} else {
			$fillers=$this->getRoleFillers();
			if (count($fillers)>0) {
				$tmpUser=self::getManager()->loadUser($fillers[0]->getUserId());
				return $tmpUser->getId();
			} else 
			return $this->_sourceUser_id;
		}
	}
	
	public function setUser ($user) {
		//if (!($this->getSourceId()>0 && $this->getSourceCircleId()>0)) {
			if (is_object($user)) {
				$this->_sourceUser=$user;
				$this->_sourceUser_id=$user->getId();
			} else {
				$this->_sourceUser_id=$user;
			}
		//}
	}
	public function getUser () {
		// Premier lien, retourne le user d'au-dessus
		if ($this->_type==\holacracy\Role::LEAD_LINK_ROLE) {
			return $this->getSuperCircle()->getUser();
		} else	if ($this->getSourceCircleId()>0 && $this->getSourceCircle()->getUserId()>0) {
				return $this->getSourceCircle()->getUser();
			} else	if ($this->getSourceId()>0 && $this->getSource()->getUserId()>0) {
				return $this->getSource()->getUser();
			} else	if (!isset($this->_sourceUser)) {
			if (isset($this->_sourceUser_id) && $this->_sourceUser_id>0) {
				$this->_sourceUser=self::getManager()->loadUser($this->_sourceUser_id);
			} else {
				$fillers=$this->getRoleFillers();
				if (count($fillers)>0) {
					$this->_sourceUser=self::getManager()->loadUser($fillers[0]->getUserId());
					$this->_sourceUser_id=$fillers[0]->getUserId();
				}
			}
		}
		return $this->_sourceUser;
	}
	
	
	public function setSourceCircleId ($id) {
		$this->setSourceCircle($id);
	}
	public function getSourceCircle() {
		if (!isset($this->_sourceCircle)) {
			if (isset($this->_sourceCircle_id)) {
				$this->_sourceCircle=self::getManager()->loadRole($this->_sourceCircle_id);
			} 
		}
		return $this->_sourceCircle;
	}
	public function getSourceCircleId() {
		if (!isset($this->_sourceCircle_id)) {
			if (isset($this->_sourceCircle)) {
				$this->_sourceCircle_id=$this->_sourceCircle->getId();
			} 
		}
		return $this->_sourceCircle_id;
	}
	
	

	// Liste des SETTER définissant les valeurs des propriétés	
	public function setSuperCircleID ($superCircleID) {

		$this->_superCircleID=$superCircleID;
	}
	
	public function setType ($type) {
		$this->_type=$type;
	}
	public function isActive () {
		if ($this->getSuperCircle() && $this->getSuperCircle()->isActive()==false) {
			return false;
		} else {
			return $this->_active;
		}
	}

	public function setActive ($active) {

		$this->_active=$active;
	}
	
	
	public function setPurpose ($purpose) {
		if ($purpose!="" && ! is_string ( $purpose )) // S'il ne s'agit pas d'une chaîne de caractère .
		{
			trigger_error ('The PURPOSE must be a string.', E_USER_WARNING );
			return ;
		}
		$this->_purpose=$purpose;
	}
	
	
	public function setName ($name) {
		if (! is_string ( $name )) // S'il ne s'agit pas d'une chaîne de caractère .
		{
			trigger_error ('The NAME must be a string.', E_USER_WARNING );
			return ;
		}
		$this->_name=$name;
	}
	
	
	public function checkIntegrity () {
	
		function checkArray($obj, $array) {
		if (!is_null($obj)) {
			foreach ($array as &$member) {
			  if ($member->getId() == $obj->getId()) {
			    return TRUE;
			  }
			}
			return FALSE;
		}}
	
		// Contrôle la bonne affectation des projets (doivent être énergétisés par des membres du cercle)
		//$roleFillers = $this->getRoleFillers();
		$members=$this->getSuperCircle()->getMembers();
		$projects = $this->getProjects();
		foreach ($projects as $project) {
			if (!checkArray($project->getUser(), $members)) {
				
					$project->setUser(NULL);
					$this->getManager()->save($project);
				
			}
		}
	}
}

?>
