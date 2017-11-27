<?php
	namespace holacracy;


class Moi extends Holacracy
{
	protected $_firstname ;	// Nom du User
	protected $_lastname ;	// Nom du User
	protected $_username ="Empty user";	// Nom du User
	protected $_email;  // E-mail de l'utilisateur
	protected $_password;  // mot de passe, uniquement pour SET et sauvegarde

	private $_contacts=array();	// Liste des contacts
	private $_contacts_loaded = false;	// Les contacts ont-ils été chargés du manager
	
	// Fonction permettant de comparer 2 objets de type USER, pour les fonction de comparaison de tableaux
	static function compareUser($obj_a, $obj_b) {
			      if ($obj_a->getId() < $obj_b->getId()) {
				        return -1;
				    } elseif ($obj_a->getId() > $obj_b->getId()) {
				        return 1;
				    } else {
				        return 0;
				    }
			}
	
	// Fonctions pour accéder aux propriétés de l'objet (GET et SET)
	public function getFullName () {
		return $this->_firstname." ".$this->_lastname;
	}
	public function getFirstName () {
		return $this->_firstname;
	}
	public function getLastName () {
		return $this->_lastname;
	}
	public function getUserName () {
		return $this->_username;
	}
	
	public function getEmail () {
		return $this->_email;
	}
	
	public function getId () {
		return $this->_id;
	}
	
	public function setUserId ($userid) {
		$this->_id=$userid;
	}
	
	public function setFirstName ($firstname) {
		$this->_firstname=$firstname;
	}
	
	public function setLastName ($lastname) {
		$this->_lastname=$lastname;
	}
	public function setUserName ($username) {
		$this->_username=$username;
	}
	
	public function setEmail ($email) {
		$this->_email=$email;
	}
	public function setPassword ($password) {
		$this->_password=$password;
	}	
	public function getPassword () {
		return $this->_password;
	}
	
	// Retourne la liste des éléments de type contact
	public function getContacts ($donotloadDB = 0) {
		if (!$this->_contacts_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadContactList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_contacts)) {
					$this->_contacts[]=$tmp_array[$i];
				}
			}
			$this->_contacts_loaded=true;
		}
		return $this->_contacts;	
	}

	public function getRoles($object, $filter=255) {
		if (get_class($object)=="holacracy\Circle") {	
			$roles=$object->getRoles($filter);
			$returnvalue=array();
			if (count($roles)>0) {
				// Parcours chaque élément, cherche sa liste de RollFiller et la compare au user actuel
				foreach ($roles as $elemrole) {
					$roleFillers=$elemrole->getRoleFillers();
					foreach ($roleFillers as $roleFiller) {
						if ($roleFiller->getUserId()==$this->getId()) {
							$returnvalue[]=$elemrole;
							break 1;
						}
					}
				}
			} 
			return $returnvalue;				
		}
	}

	// Fonctions pour connaître les droits et fonctions d'un user
	public function isRole($role, $circle=NULL) {
		// Rôle sous forme de constante ou d'objet?
		if (is_numeric($role)) {
			// Le Cercle est-il défini?
			if (isset($circle) && $circle!=NULL && get_class($circle)=="holacracy\Circle") {
				// Récupère le bon rôle et le compare au user
				$roles=$circle->getRoles($role);
				$returnvalue=false;
				if (count($roles)>0) {
					// Parcours chaque élément, cherche sa liste de RollFiller et la compare au user actuel
					foreach ($roles as $elemrole) {
						$roleFillers=$elemrole->getRoleFillers();
						foreach ($roleFillers as $roleFiller) {
							if ($roleFiller->getUserId()==$this->getId()) {
								$returnvalue=true;
							}
						}
					}
				} 
				return $returnvalue;
			}  else {
				trigger_error ("Invalid Circle object");
				return false;
			}
		} else {
			// Est-ce bien un objet de type ROLE
			if (get_class($role)=="holacracy\Role" || get_class($role)=="holacracy\Circle") {
				$returnvalue=false;
				$roleFillers=$role->getRoleFillers();
				foreach ($roleFillers as $roleFiller) {
					if ($roleFiller->getUserId()==$this->getId()) {
						$returnvalue=true;
					}
				}
				return $returnvalue;
			} else {
				trigger_error ("Invalid Role object");
				return false;
			}
		}
		
	}
	
	public function isMember($object) {
		switch (get_class($object))	{
			case "holacracy\\Circle" :
					$liste_members=$object->getMembers();
					$returnvalue=false;
					foreach ($liste_members as $member) {
						if ($member->getId()==$this->getId()) $returnvalue=true;
					}
				return $returnvalue;
			case "holacracy\\Organisation" :
				return true;
			default:
			trigger_error ("USER can't be member of ".get_class($object)." object", E_USER_WARNING )." object";
					return false;
		}
		
	}
	
}
?>