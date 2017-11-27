<?php
	namespace holacracy;

// Classe Cercle, représentant un cercle possédant plusieurs rôles
class Circle extends Role
{
	private $_listeRoles = array();
	private $_listeLinks = array();
	private $_listeMembers = array();
	private $_strategy;
	private $_circles = array();
	private $_policy = array();
	private $_policy_loaded = false;
	private $_meeting = array();
	private $_meeting_loaded = false;
	private $_allscope = array();
	private $_allscope_loaded = false;
	private $_circle_actions = array();
	private $_chat = array();
	private $_chat_loaded = false;
	
	// Type de classement pour la liste des rôles (fonction loadRoles) - DDr, 4.9.2014
	const NAME_ORDER = 1;
	const TYPE_ORDER = 2;
	
	const UNASSIGNED_LINK = 1;
	const SOURCE_LINK = 2;
	const TARGET_LINK = 4;
	const MASTER_LINK = 8;	
	
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
	
	public function setStrategy($strategy) {
		$this->_strategy=$strategy;
	}
	
	public function getStrategy() {
		return $this->_strategy;
	}
	
	public function attachRole($role) {
		$role->_superCircleID=$this->_id;
	}

	public function addMember($member) {
		if (is_object($member)) {
			if (get_class($member)=="holacracy\\RoleFiller") {
				self::getManager()->addMemberCircle($member->getUserId(),$this->getId());
			} else {
				self::getManager()->addMemberCircle($member->getId(),$this->getId());
			}
		} else {
			self::getManager()->addMemberCircle($member,$this->getId());
		}
	}

	// Charge la liste des membres d'un cercle
	public function getMembers() {
		// Chargement de la liste des rôles que si nécessaire (pour éviter la création itérative de grosses structures)
		if (count($this->_listeMembers)==0) {
			$this->_listeMembers=self::getManager()->loadMemberListe($this);	
		}
		$return_array=array();
		foreach($this->_listeMembers as $member) {
				$return_array[] = $member;
		}
		return $return_array;		
	}

		// Charge la liste des actions associées à un cercle mais pas à un rôle
	public function getCircleActions ($user=NULL) {
		if (count($this->_circle_actions)==0 && self::getManager()) {
			if (!is_null($user)) { //Si plusieurs focus
				$this->_circle_actions=self::getManager()->loadCircleActions($this,$user);
			} else { //Si un seul focus
				$this->_circle_actions=self::getManager()->loadCircleActions($this);
			}
		}
		return $this->_circle_actions;		
	}
	
	// Retourne la liste des sous-cercles associés à ce cercle
	public function getCircles($order=\holacracy\Circle::NAME_ORDER) {
		// Chargement de la liste des rôles que si nécessaire (pour éviter la création itérative de grosses structures)
		if (count($this->_circles)==0) {
			$this->_circles=self::getManager()->loadCircles($this);	
		}
		if (count($this->_circles)>1) {
			if ($order==\holacracy\Circle::NAME_ORDER) usort($this->_circles, array("\holacracy\Circle", "compare_name"));
			if ($order==\holacracy\Circle::TYPE_ORDER) usort($this->_circles, array("\holacracy\Circle", "compare_type"));
		}

		return $this->_circles;		
	}

	
	public function getAllScopes($donotloadDB = 0) {
		if (!$this->_allscope_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadAllScopes($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_allscope)) {
					$this->_allscope[]=$tmp_array[$i];
				}
			}
			$this->_allscope_loaded=true;
		}
		return $this->_allscope;
	}
	
	

	public function getMeetings($donotloadDB = 0, $old=false) {
		if ($old) {
			$tmp_array=$this->getManager()->loadMeetingList($this,$old);
			return $tmp_array;
		} else
		
		if (!$this->_meeting_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadMeetingList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_meeting)) {
					$this->_meeting[]=$tmp_array[$i];
				}
			}
			$this->_meeting_loaded=true;
		}
		return $this->_meeting;
	}
	
	public function getSubPolicy($donotloadDB = 0) {
		// Remonte la chaîne des cercles pour en charger toutes les politiques
	}

	public function getPolicy($donotloadDB = 0) {
		if (!$this->_policy_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadPolicyList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_policy)) {
					$this->_policy[]=$tmp_array[$i];
				}
			}
			
			$this->_policy_loaded=true;
		}
		
		return $this->_policy;	

	}
	
	// Fonctions de tri de la liste des rôles - DDr, 4.9.2014
	public static  function compare_name($a, $b) { 
		return strcmp($a->getName(), $b->getName());
	} 
	public static  function compare_type($a, $b) { 
	    if($a->getType() == $b->getType()) {
	        return 0 ;
	    } 
	  	return ($a->getType() < $b->getType()) ? -1 : 1;
	} 


	public function getLinks($filter=254, $assigned=NULL, $order=self::NAME_ORDER) {
		// Chargement de la liste des rôles que si nécessaire (pour éviter la création itérative de grosses structures)
		if (count($this->_listeLinks)==0) {
		
			$this->_listeLinks=self::getManager()->loadLinkListe($this);
		}
		$return_array=array();
		foreach($this->_listeLinks as $link) {
				$type=0;
				if ($link->getSourceCircleId()==$this->getId()) $type=$type | \holacracy\Circle::SOURCE_LINK; 
				if ($link->getSuperCircleId()==$this->getId()) $type=$type | \holacracy\Circle::TARGET_LINK; 
				if ($link->getMasterId()==$this->getId()) {

					$type=$type | \holacracy\Circle::MASTER_LINK; 
				
				}
				if (($type & $filter) >0) {
					if (is_null($assigned) || ($link->getSourceId()>0 && $assigned==true) || (!($link->getSourceId()>0) && $assigned==false)) {
						$return_array[] = $link;
					}
				}
		}
		
		// Trie selon les bons critères - DDr, 4.9.2014
		if ($order==self::NAME_ORDER) usort($return_array, array("\holacracy\Circle", "compare_name"));
		if ($order==self::TYPE_ORDER) usort($return_array, array("\holacracy\Circle", "compare_type"));
		
		return $return_array;		
	}
	
	// Retourne la liste des Roles d'un cercle, filtrés selon le paramètre (cf constantes dans la classe rôle)
	public function getRoles($filter=255, $order=self::NAME_ORDER) {
		// Chargement de la liste des rôles que si nécessaire (pour éviter la création itérative de grosses structures)
		
		if (count($this->_listeRoles)==0) {
		
			$this->_listeRoles=self::getManager()->loadRoleListe($this);
		}
		$return_array=array();
		foreach($this->_listeRoles as $role) {
			
			if (($role->getType() & $filter)>0 && $role->getSuperCircleId()==$this->getId() && $role->isActive()) {
				$return_array[] = $role;
			}	
		}
		
		// Trie selon les bons critères - DDr, 4.9.2014
		if ($order==self::NAME_ORDER) usort($return_array, array("\holacracy\Circle", "compare_name"));
		if ($order==self::TYPE_ORDER) usort($return_array, array("\holacracy\Circle", "compare_type"));
		
		return $return_array;
	}
	
	// Retourne le premier lien d'un cercle, à savoir un clône du Cercle dans le format Rôle
	
	// Fonction temporaire suite à une faute d'ortographe - DDr, 5.9.2014
	public function getLeadLink() {
		$tmp=$this->getRoles($this::LEAD_LINK_ROLE);
		if (count($tmp)>0)
			return $tmp[0];
	}
	public function getLeapLink() {
		return $this->getLeadLink();
	}
	public function getRepLink() {
		$tmp=$this->getRoles($this::REP_LINK_ROLE);
		if (count($tmp)>0)
			return $tmp[0];
	}
	
	public function getSecretary() {
		$tmp=$this->getRoles($this::SECRETARY_ROLE);
		if (count($tmp)>0)
			return $tmp[0];
	}
	
	// Retourne une chaîne de caractère identifiant le Cercle
	public function toString () {
		return $this->getName();
	}

    // Retourne une chaîne HTML avec un lien sur l'élément (obsolète)
	public function toHTMLString () {
		return "<a href='circle.php?id=".$this->_id."'>".$this->toString()."</a>";
	}
	

}

?>
