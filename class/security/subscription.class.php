<?php
	namespace security;

	class Subscription extends \datamanager\DbObject {
		
		private $_name ;				// Nom de l'abonnement
		private $_description ;			// Description de l'abonnement
		private $_startDate;			// Début de l'abonnement
		private $_duration;				// Durée de l'abonnement
		private $_price;				// Prix de l'abonnement
		private $_organisation;			// Organisation attachée 
		private $_organisation_id;		// ID de l'organisation attachée 
		private $_subscriptionType;		// Modèle d'abonnement (pour récupérer les données de base (prix, etc...)
		private $_subscriptionTypeId;	// ID du modèle d'abonnement
		private $_active = 1;			// L'abonnement est-il actif
		
		// Type de classement pour la liste des abonnements (fonction loadSubrscriptions) - DDr, 8.5.2015
		const DATE_ORDER = 1;
		const NAME_ORDER = 2;
		
		// Retourne une liste de tous les abonnements à disposition
		public static function getSubscriptions() {
			return $_SESSION["currentManager"]->loadSubscriptions();
		}
		
		// Fonctions de tri de la liste des abonnements 
		public static  function compare_name($a, $b) { 
			return strcmp($a->getName(), $b->getName());
		} 
		public static  function compare_date($a, $b) { 
			if($a->getStartDate() == $b->getStartDate()) {
				return 0 ;
			} 
			return ($a->getStartDate() < $b->getStartDate()) ? -1 : 1;
		} 
	
		public function isActive() {
			return $this->_active;
		}
		
		public function setActive($active) {
			$this->_active=$active; 
		}
	
	
		// Retourne le nom de l'abonnement
		public function getName() {
			return $this->_name;
		}

		// Défini le nom de l'abonnement
		public function setName($name) {
			$this->_name=$name;
		}
		
		// Retourne la description de l'abonnement
		public function getDescription() {
			return $this->_description;
		}

		// Défini la description de l'abonnement
		public function setDescription($description) {
			$this->_description=$description;
		}
		
		// Retourne la date de début de l'abonnement
		public function getStartDate() {
			return $this->_startDate;
		}

		// Défini la date de début de l'abonnement
		public function setStartDate($date) {
			
			if (is_string($date)) {
				$this->_startDate=date_create($date);
			} else {
				$this->_startDate=$date;
			}
		}
		
		// Retourne la date de fin de l'abonnement
		public function getEndDate() {
			// Calcul la date de fin en fonction de la date de début et de la durée
			$end_date = clone $this->getStartDate();
			return $end_date->add(new \DateInterval($this->_duration));
		}

		// Défini la date de fin de l'abonnement
		public function setEndDate($date) {
			// Défini la durée en fontion de la différence entre la date de début et la date de fin
		}

		// Retourne la durée de l'abonnement
		public function getDuration() {
			return $this->_duration;
		}

		// Défini la durée de l'abonnement
		public function setDuration($duration) {
			$this->_duration=$duration;
		}
		
		// Retourne le prix de l'abonnement
		public function getPrice() {
			return $this->_price;
		}

		// Défini le prix de l'abonnement
		public function setPrice($price) {
			$this->_price=$price;
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
				}
			}
			return $this->_organisation;
		}
		
		// Défini l'org
		public function setOrganisationId($id) {
			$this->setOrganisation($id);
		}
		
		// Modèle d'abonnement
		public function setSubscriptionType($object) {
			if (is_object($object)) {
				$this->_subscriptionType=$object;
				$this->_subscriptionTypeId=$object->getId();
			} else {
				$this->_subscriptionTypeId=$object;
			}
		}

		public function getSubscriptionTypeId() {
			return $this->_subscriptionTypeId; 
		}
		
		public function getSubscriptionType() {
			if (!isset($this->_subscriptionType)) {
				if (isset($this->_subscriptionTypeId)) {
					$this->_subscriptionType=self::getManager()->loadSubscriptionType($this->_subscriptionTypeId);
				}
			}
			return $this->_subscriptionType;
		}
		
		// Défini l'org
		public function setSubscriptionTypeId($id) {
			$this->setSubscriptionType($id);
		}	
		
		public function setUser($object) {
			if (is_object($object)) {
				$this->_user=$object;
				$this->_user_id=$object->getId();
			} else {
				$this->_user_id=$object;
			}
		}

		public function getUserId() {
			return $this->_user_id;
		}
		
		public function getUser() {
			if (!isset($this->_user)) {
				if (isset($this->_user_id)) {
					$this->_user=self::getManager()->loadUser($this->_user_id);
				}
			}
			return $this->_user;
		}
		
		// Défini l'utilisateur ayant créé l'abonnement
		public function setUserId($id) {
			$this->setUser($id);
		}
		
	}

?>
