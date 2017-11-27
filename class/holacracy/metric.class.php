<?php
	namespace holacracy;


class Metric extends Holacracy
{
	protected $_name ;				// Nom
	protected $_shortname ;			// Nom court pour affichage graphique
	protected $_description ;		// Description
	protected $_circle_id;  		// Cercle sous la forme d'un id cercle
	protected $_circle;  			// Cercle sous la forme d'un lien sur le cercle
	protected $_role_id;  			// Role concerné sous la forme d'un id cercle
	protected $_role;  				// Role concerné sous la forme d'un lien sur le cercle
	protected $_user_id;  			// Role concerné sous la forme d'un id cercle
	protected $_user;  				// Role concerné sous la forme d'un lien sur le cercle
	protected $_recurrence_id;		// Recurrence sous forme d'ID 	
	protected $_recurrence;			// Recurrence sous la forme d'objet
	protected $_values = array();	// Liste historique des valeurs
	protected $_references = array();	// Liste des references à d'autres metrics
	protected $_numeric;			// Boolean pour savoir si l'indicateur doit être représenté graphiquement
	protected $_file;				// URL d'un fichier à téléchargé si renseigné
	protected $_goal;				// Integer à atteindre

	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getReferences() {
		// Chargement de la liste des valeurs que si nécessaire (pour éviter la création itérative de grosses structures)
		if (count($this->_references)==0) {
			$this->_references=self::getManager()->loadMetricReferences($this);	
		}
		$return_array=array();
		foreach($this->_references as $reference) {
				$return_array[] = $reference;
		}
		return $return_array;	
	}

	public function getGoal() {
		return $this->_goal;
	}
	
	public function setGoal($goal) {
		$this->_goal=$goal;
	}
	
	public function getName () {
		if ($this->_name=="") {
			// Version transitoire après l'ajout des propriétés name et shortname
			return $this->_description;
		} else {
			return $this->_name;
		}
	}
	public function getShortName () {
		return $this->_shortname;
	}
	public function getDescription () {
		return $this->_description;
	}
	public function getNumeric() {
		return $this->_numeric;
	}
	public function setNumeric($bool) {
		$this->_numeric=$bool;
	}
	public function getFile() {
		return $this->_file;
	}
	public function setFile($file) {
		$this->_file=$file;
	}
	
	public function getValues() {
		// Chargement de la liste des valeurs que si nécessaire (pour éviter la création itérative de grosses structures)
		if (count($this->_values)==0) {
			$this->_values=self::getManager()->loadMetricValues($this);	
		}
		$return_array=array();
		foreach($this->_values as $value) {
				$return_array[] = $value;
		}
		return $return_array;		
	}
	
	public function getValue() {
		if (count($this->_values)==0) {
			$this->_values=self::getManager()->loadMetricValues($this);	
		}		
		if (count($this->_values)>0) {
			return end($this->_values);
		}
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
	public function getUser () {
		if (is_null($this->_user) && $this->_user_id>0) {
			$this->_user=self::getManager()->loadUser($this->_user_id);
		}
		return $this->_user;
	}
	public function getUserId () {
		return $this->_user_id;
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

	public function getRecurrence () {
		if (is_null($this->_recurrence) && $this->_recurrence_id>0) {
			$this->_recurrence=self::getManager()->loadRecurrence($this->_recurrence_id);
		}
		return $this->_recurrence;
	}
	public function getRecurrenceId () {
		if (isset($this->_recurrence)) {
			$this->_recurrence_id=$this->_recurrence->getId();
		}
		return $this->_recurrence_id;
	}
	
	public function setName ($name) {
		if ($this->_name!=$name) $this->setModified(true);
		$this->_name=$name;
	}
	public function setShortName ($shortname) {
		if ($this->_shortname!=$shortname) $this->setModified(true);
		$this->_shortname=$shortname;
	}
	public function setDescription ($description) {
		if ($this->_description!=$description) $this->setModified(true);
		$this->_description=$description;
	}
	public function setRole ($role) {
		if ($this->_role_id!=$role->getId()) $this->setModified(true);
		$this->_role=$role;
		$this->_role_id=$role->getId();
	}
	public function setRoleId ($roleId) {
		if ($this->_role_id!=$roleId) $this->setModified(true);
		$this->_role_id=$roleId;
		$this->_role=null;
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
	public function setCircle ($circle) {
		if ($this->_circle_id!=$circle->getId()) $this->setModified(true);
		$this->_circle=$circle;
		$this->_circle_id=$circle->getId();
	}
	public function setCircleId ($circleId) {
		if ($this->_circle_id!=$circleId) $this->setModified(true);
		$this->_circle_id=$circleId;
		$this->_circle=null;
	}

	public function setRecurrence ($recurrence) {
		if ($this->_recurrence_id!=$recurrence->getId()) $this->setModified(true);
		$this->_recurrence=$recurrence;
		$this->_recurrence_id=$recurrence->getId();
	}
	public function setRecurrenceId ($recurrenceId) {
		if ($this->_recurrence_id!=$recurrenceId) $this->setModified(true);
		$this->_recurrence_id=$recurrenceId;
		$this->_recurrence=null;
	}

	
	public function delete() {
		//fonction de base
		parent ::delete();
		$this->_modified=true;
	}
	public function checkForSave() {
		if ($this->getName()=="") {
			return "Définir le Titre pour ce METRIC [".$this->getId()."]";
		}
		if (!($this->getCircleId()>0)) {
			// Possibilité de stoquer des metrics sans cercle
			//return "Définir le CIRCLE pour ce METRIC [".$this->getName()."]";
		}
		if (!($this->getRecurrenceId()>0)) {
			return "Définir la RECURRENCE pour ce METRIC [".$this->getName()."]";
		}
		
		return false;
	}
}
?>
