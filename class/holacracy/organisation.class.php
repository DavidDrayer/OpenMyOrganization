<?php
	namespace holacracy;

class Organisation extends Holacracy
{
	private $_name ;		// Nom de l'organisation
	private $_shortName ;	// Nom court de l'organisation - DDr 5.6.2014
	
	private $_circles = array();		// Liste des cercles de l'organisation (pas de rôle)
	private $_members = array();
	private $_members_loaded = false;
	private $_admins = array();
	private $_admins_loaded = false;
	private $_meeting = array();
	private $_meeting_loaded = false;
	private $_values = array();
	private $_values_loaded = false;
	private $_description;
	private $_language;
	private $_website;
	private $_purpose;
	private $_purposedescription;
	private $_mission;
	private $_missiondescription;
	private $_vision;
	private $_visiondescription;
	private $_visibility = 0;
	private $_subscriptions = array();	// Liste des abonnements de l'organisation

	public function setVisibility($visible) {
		$this->_visibility=$visible;
	}
	
	public function getVisibility() {
		return $this->_visibility;
	}

	// Retourne le nom court de l'organisation, utilisé lorsqu'il y a peu de place (entête e-mail par exemple) - DDr 5.6.2014
	public function getShortName() {
		return $this->_shortName;
	}

 	public function setShortName($name) {
 		$this->_shortName=$name;
	}

	public function getName() {
		return $this->_name;
	}

 	public function setName($name) {
 		$this->_name=$name;
	}

 	public function setWebSite($website) {
 		$this->_website=$website;
	}

	public function getWebSite() {
 		return $this->_website;
	}

	public function getDescription() {
		return $this->_description;
	}
	
	public function getVision() {
		return $this->_vision;
	}
	
	public function getVisionDescription() {
		return $this->_visiondescription;
	}

	public function getMission() {
		return $this->_mission;
	}
	
	public function getMissionDescription() {
		return $this->_missiondescription;
	}

	public function getPurpose() {
		return $this->_purpose;
	}
	
	public function getPurposeDescription() {
		return $this->_purposedescription;
	}

	public function setVision($vision) {
		$this->_vision=$vision;
	}
	
	public function setVisionDescription($description) {
		$this->_visiondescription=$description;
	}

	public function setMission($mission) {
		$this->_mission=$mission;
	}
	
	public function setMissionDescription($description) {
		$this->_missiondescription=$description;
	}

	public function setPurpose($purpose) {
		$this->_purpose=$purpose;
	}
	
	public function setPurposeDescription($description) {
		$this->_purposedescription=$description;
	}


 	public function setDescription($description) {
 		$this->_description=$description;
	}
	
	public function getLanguage() {
		return $this->_language;
	}

 	public function setLanguage($language) {
 		$this->_language=$language;
	}
	
	// Retourne la liste des cercles associés à cette organisation
	public function getCircles($order=\holacracy\Circle::NAME_ORDER) {
		// Chargement de la liste des rôles que si nécessaire (pour éviter la création itérative de grosses structures)
		if (count($this->_circles)==0 && $this->_id>0) {
			$this->_circles=$this->getManager()->loadCircles($this);	
		}
		// Probablement inutile dans le sens où il y a qu'un cercle d'ancrage par organisation...
		if (count($this->_circles)>1) {
			if ($order==\holacracy\Circle::NAME_ORDER) usort($this->_circles, array("\holacracy\Circle", "compare_name"));
			if ($order==\holacracy\Circle::TYPE_ORDER) usort($this->_circles, array("\holacracy\Circle", "compare_type"));
		}
		return $this->_circles;		
	}
	
	public function isAdmin($user) {
		$isAdmin=false;
		$root=$this->getCircles();
		// Un seul cercle d'ancrage, le premier lien est de toutefaçon administrateur
		if (count($root)==1) {
			$root=$root[1];
			if (!is_null($root->getLeadLink()) && $root->getLeadLink()->getUserId()>0) {
				if ($root->getLeadLink()->getUserId()==$user->getId()) $isAdmin=true;
			}
		}
		
		// Sinon, charge la liste des administrateurs et y recherche le user courant
		$admins=$this->getAdmins();
		foreach ($admins as $admin) {
			if ($admin->getId()==$user->getId()) {
				$isAdmin=true;
			}
		}

		return $isAdmin;
	}
	
	public function isMember($user) {
		$isMember=false;
		
		// Sinon, charge la liste des administrateurs et y recherche le user courant
		$members=$this->getMembers();
		foreach ($members as $member) {
			if ($member->getId()==$user->getId()) {
				$isMember=true;
			}
		}

		return $isMember;
	}
	
	// Charge l'ensemble des abonnements d'une organisation
	public function getSubscriptions($order=\security\Subscription::DATE_ORDER) {
		// Chargement de la liste des abonnements que si nécessaire (pour éviter la création itérative de grosses structures)
		if (count($this->_subscriptions)==0 && $this->_id>0) {
			$this->_subscriptions=$this->getManager()->loadSubscriptions($this);	
		}
		if (count($this->_subscriptions)>1) {
			if ($order==\security\Subscription::DATE_ORDER) usort($this->_subscriptions, array("\security\Subscription", "compare_date"));
			if ($order==\security\Subscription::NAME_ORDER) usort($this->_subscriptions, array("\security\Subscription", "compare_name"));
		}
		return $this->_subscriptions;		
	}
	
	// Retourne l'abonnements en cours (accès direct, car le plus souvant utilisé)
	public function getSubscription() {
	}
	
	// Ajoute un abonnement
	public function addSubscription() {
	
	}
	
	public function getAdmins($donotloadDB = 0) {
		if (!$this->_admins_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadAdminListe($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_admins)) {
					$this->_admins[]=$tmp_array[$i];
				}
			}
			$this->_admins_loaded=true;
		}
		return $this->_admins;	
	}

	public function getValues($donotloadDB = 0) {
		if (!$this->_values_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadValueListe($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_values)) {
					$this->_values[]=$tmp_array[$i];
				}
			}
			$this->_values_loaded=true;
		}
		return $this->_values;	
	}

	public function getMembers($donotloadDB = 0) {
		if (!$this->_members_loaded && $this->_id>0 && !$donotloadDB) {
			$tmp_array=$this->getManager()->loadMemberListe($this);
			foreach ($tmp_array as $tmp) {
				if (isset($tmp) && !in_array($tmp, $this->_members)) {
					$this->_members[]=$tmp;
					$tmp->getId();
				}
			}
			/* for ($i=0; $i<count($tmp_array);$i++) {
				if (isset($tmp_array[$i]) && !in_array($tmp_array[$i], $this->_members)) {
					$this->_members[]=$tmp_array[$i];
					$tmp_array[$i]->getId();
				}
			}*/
			$this->_members_loaded=true;
		}
		return $this->_members;	
	}
	
		public function getMeetingList($donotloadDB = 0) {
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
	
}
?>
