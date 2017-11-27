<?php
	namespace security;

	class Transaction extends \datamanager\DbObject {
		private $_tocken ;			// Code de communication avec Paypal
		private $_subscriptionId ;	// ID de l'abonnement choisi
		private $_subscription ;	// Abonnement choisi
		private $_price;			// Prix de l'abonnement (au cas où s'ajoutent des réductions)
		private $_startDate;		// Début de la date de validité
		private $_organisation;		// Organisation attachée 
		private $_organisation_id;	// ID de l'organisation attachée 
		private $_status;			// Status de la commande
		private $_user;
		private $_user_id;
	
		public function getStatus () {
			return $this->_status;
		}
		
		public function setStatus($status) {
			$this->_status=$status;
		}
		
		public function getTocken () {
			return $this->_tocken;
		}
		
		public function setTocken($tocken) {
			$this->_tocken=$tocken;
		}
		
		// Retourne le prix facturé
		public function getPrice() {
			return $this->_price;
		}

		// Défini le prix de l'abonnement
		public function setPrice($price) {
			$this->_price=$price;
		}
		
		// Retourne la date de début de validité
		public function getStartDate() {
			return $this->_startDate;
		}

		// Défini la date de début de validité
		public function setStartDate($date) {
			
			if (is_string($date)) {
				$this->_startDate=date_create($date);
			} else {
				$this->_startDate=$date;
			}
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
		
		// Défini l'utilisateur ayant créé la transaction
		public function setUserId($id) {
			$this->setUser($id);
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
		
		public function setSubscription($object) {
			if (is_object($object)) {
				$this->_subscription=$object;
				$this->_subscription_id=$object->getId();
			} else {
				$this->_subscription_id=$object;
			}
		}

		public function getSubscriptionId() {
			return $this->_subscription_id;
		}
		
		// Défini l'abonnement
		public function getSubscription() {
			if (!isset($this->_subscription)) {
				if (isset($this->_subscription_id)) {
					$this->_subscription=self::getManager()->loadSubscriptionType($this->_subscription_id);
				} 
			}
			return $this->_subscription;
		}

		
		// Défini l'abonnement
		public function setSubscriptionId($id) {
			$this->setSubscription($id);
		}	
	}
?>
