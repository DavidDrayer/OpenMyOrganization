<?php
	namespace holacracy;


class User extends Holacracy
{
	protected $_firstname ;	// Nom du User
	protected $_lastname ;	// Nom du User
	protected $_username ="";	// Nom du User
	public $_email;  // E-mail de l'utilisateur
	protected $_password;  // mot de passe, uniquement pour SET et sauvegarde
	protected $_connexionDate; // Dernière heure de connexion
	protected $_langue;  // Langue de l'utilisateur
	
	protected $_code;	// Code d'activation ou de contrôle
	protected $_active;	// Utilisateur actif ou pas
	
	private $_orgsuser=array();	// Liste des orgs pour le user
	
	private $_admin=false;
	private $_developper=false;
	private $_moi = false; //kda 5.6.2014

	private $_contacts=array();	// Liste des contacts
	private $_contacts_loaded = false;	// Les contacts ont-ils été chargés du manager
	
	private $_notifications=array();	// Liste des notifications
	private $_notifications_loaded = false;	// Les notifications ont-elles été chargés du manager
	
	private $_metrics=array();	// Liste des notifications
	private $_metrics_loaded = false;	// Les notifications ont-elles été chargés du manager
	
	private $_checklists=array();	// Liste des notifications
	private $_checklists_loaded = false;	// Les notifications ont-elles été chargés du manager
	
	private $_meetings=array();	// Liste des notifications
	private $_meetings_loaded = false;	// Les notifications ont-elles été chargés du manager
	
	private $_preferences=array();	// Liste des preferences et parametres
	private $_preferences_loaded = false;	// Les preferences ont-elles été chargés du manager
	
	private $_listeRoles=array();
	private $_listeRoles_loaded = false;	// les rôles ont-ils été chargés?
	
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
	public function getCode () {
		return $this->_code;
	}
	public function isActive () {
		return $this->_active;
	}

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
	
	public function getUserLangue () {
		return $this->_langue;
	}
	
	public function getEmail () {
		return $this->_email;
	}
	
	public function getId () {
		return $this->_id;
	}
	
	public function getLastConnexion() {
		return $this->_connexionDate;
	}
	public function setLastConnexion($time="") {
		if ($time!="") {
			@$this->_connexionDate=$time;
		} else {
			@$this->_connexionDate=new \DateTime();
		}
		// Stock directement une entrée dans la table
		
	}
	
	public function setOrgsUser($org) {
		$this->_orgsuser = $org;
	}
	
	public function setUserId ($userid) {
		$this->_id=$userid;
	}
	
	public function setFirstName ($firstname) {
		$this->_firstname=$firstname;
	}
	public function setCode ($code) {
		$this->_code=$code;
	}
	public function setActive ($active) {
		$this->_active=$active;
	}
	
	public function setLastName ($lastname) {
		$this->_lastname=$lastname;
	}
	public function setUserName ($username) {
		$this->_username=$username;
	}
	
	public function setUserLangue ($userlangue) {
		$this->_langue=$userlangue;
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
	
	public function getAllLanguage(){
	$tmp_array = $this->getManager()->loadAllLanguage();
	return $tmp_array;
	}
	
	public function getActions($context=NULL) {
		$tmp_array = $this->getManager()->loadActions($this,true,$context);
		return $tmp_array;
	}
	
	//kda 2.7.2014
	public function ActiveLanguage($language,$basepath) {
	define('PROJECT_DIR', $basepath);
	define('LOCALE_DIR', PROJECT_DIR .'/languages');
	define('DEFAULT_LOCALE', 'fr_FR');
	
	$supported_locales = array('fr_FR', 'en_US', 'de_CH');
	$encoding = 'ISO-8859-1';

	$locale = $language;

	// gettext setup
	T_setlocale(LC_MESSAGES, $locale);
	// Set the text domain as 'messages'
	$domain = 'messages';
	T_bindtextdomain($domain, LOCALE_DIR);
	T_bind_textdomain_codeset($domain, $encoding);
	T_textdomain($domain);
	}	
	
	// Retourne la liste des éléments de type contact
	public function getContacts ($donotloadDB = 0) {
		if ($this->_contacts_loaded==false && $this->_id>0 && !$donotloadDB) {
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
	
	// Retourne la liste des éléments de type preference
	public function getPreference ($label, $donotloadDB = false) {
		if ($this->_preferences_loaded==false && $this->_id>0 && $donotloadDB==false) {
			$this->_preferences=$this->getManager()->loadPreference($this);
			$this->_preferences_loaded=true;
		} 
		if (isset($this->_preferences[$label]))
			return $this->_preferences[$label];	
		else 
			return "";
	}

	// Retourne la liste des checklistes associées à cet utilisateur
	public function getChecklists ($donotloadDB = false, $context=NULL) {
		if ($this->_checklists_loaded==false && $this->_id>0 && $donotloadDB==false) {
			$tmp_array=$this->getManager()->loadCheckLists($this, $context);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_checklists)) {
					$this->_checklists[]=$tmp_array[$i];
				}
			}
			$this->_checklists_loaded=true;
		} 
		return $this->_checklists;	
	}
	
	// Retourne la liste des checklistes associées à cet utilisateur
	public function getMetrics ($donotloadDB = false, $context=NULL) {
		if ($this->_metrics_loaded==false && $this->_id>0 && $donotloadDB==false) {
			$tmp_array=$this->getManager()->loadMetrics($this,$context);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_metrics)) {
					$this->_metrics[]=$tmp_array[$i];
				}
			}
			$this->_metrics_loaded=true;
		} 
		return $this->_metrics;	
	}
	
	// Retourne la liste des éléments de type notification
	public function getNotifications ($donotloadDB = false) {
		if ($this->_notifications_loaded==false && $this->_id>0 && $donotloadDB==false) {
			$tmp_array=$this->getManager()->loadNotification($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_notifications)) {
					$this->_notifications[]=$tmp_array[$i];
				}
			}
			$this->_notifications_loaded=true;
		} 
		return $this->_notifications;	
	}
	
	public function getMeetings($donotloadDB = false) {
		if ($this->_meetings_loaded==false && $this->_id>0 && $donotloadDB==false) {
			$tmp_array=$this->getManager()->loadMeetingList($this);
			for ($i=0; $i<count($tmp_array);$i++) {
				if (!in_array($tmp_array[$i], $this->_meetings)) {
					$this->_meetings[]=$tmp_array[$i];
				}
			}
			$this->_meetings_loaded=true;
		} 
		return $this->_meetings;	
	}

	public function getRoles($object, $filter=255, $main=false) {
		if (get_class($object)=="holacracy\Organisation") {
			if (count($this->_listeRoles)==0) {
			
				$this->_listeRoles=self::getManager()->loadRoleListe($this);
			}
			$return_array=array();
			foreach($this->_listeRoles as $role) {
				
				if (($role->getType() & $filter)>0 && $role->isActive() && $role->getOrganisation() && $role->getOrganisation()->getId()==$object->getId()) {
					$return_array[] = $role;
				}	
			}
			return $return_array;	
		}
		
		if (get_class($object)=="holacracy\Circle") {
			$roles=$object->getRoles($filter);
			$tmp=$object->getRoles(\holacracy\Role::LEAD_LINK_ROLE);
			$isLeadLink=($tmp[0]->getUserId()==$this->getId());
			$returnvalue=array();
			if (count($roles)>0) {
				// Parcours chaque élément, cherche sa liste de RollFiller et la compare au user actuel
				foreach ($roles as $elemrole) {
					if ($this->getId()==$elemrole->getUserId() || ($elemrole->getUserId()=="" && $isLeadLink)) {
						$returnvalue[]=$elemrole;
					} else 
					// Si c'est un cercle, regarde qui est le deuxième lien
						if ($elemrole->getType()==\holacracy\Role::CIRCLE ) {
							$r2=$elemrole->getRoles(\holacracy\Role::REP_LINK_ROLE);
							if (count($r2)>0 && $r2[0]->getUserId()==$this->getId()) {
								$returnvalue[]=$elemrole;
							}
						} else
					if (!$main) {
						$roleFillers=$elemrole->getRoleFillers();
						foreach ($roleFillers as $roleFiller) {
							if ($roleFiller->getUserId()==$this->getId()) {
								$returnvalue[]=$elemrole;
								break 1;
							}
						}
						
					} 
				}
			} 
			return $returnvalue;				
		}
	}
	
	public function isAdminOrg($orgId) {
		// Bug lors de certains rechargement... work-around pour éviter des problèmes d'affichages, en attendant de retravailler la fonction isAdmin - DDr, 5.9.2014
		$isadmin=false;
		$isadmin = @$this->_orgsuser[$orgId]['is_admin'];
		return $isadmin;
	}
	
	public function isAdmin($object=NULL) {
		if (!is_null($object)) {
			switch(get_class($object)){
				case "holacracy\\Organisation" :
					// Contrôle si l'utilisateur est bien administrateur de l'organisation
					return $object->isAdmin($this); // $this->_admin || // On ne tient compte que du côté administrateur de l'org dans cette fonction
				break;
			}
		} else return $this->_admin;
	}
	public function setAdmin($admin, $circle=NULL) {
		$this->_admin=$admin;
	}

	public function isDevelopper() {
		return $this->_developper;
	}
	public function setDevelopper($developper) {
		$this->_developper=$developper;
	}
	
	//Kda 5.6.2014
	public function hasMoi() {
		return $this->_moi;
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
						// Regarde si c'est le rôle principal
						if ($this->getId()==$elemrole->getUserId()) {
							$returnvalue=true;
						} else {
							// Sinon un focus
							$roleFillers=$elemrole->getRoleFillers();
							foreach ($roleFillers as $roleFiller) {
								if ($roleFiller->getUserId()==$this->getId()) {
									$returnvalue=true;
								}
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
				// Si le role n'est pas énergétisé regarde si c'est le premier lien
				if (!$role->getUserId()!="" && $role->getSuperCircle()->getLeadLink() && $role->getSuperCircle()->getLeadLink()->getUser()!="") {
					if ($role->getSuperCircle()->getLeadLink()->getUser()->getId()==$this->getId()) $returnvalue=true;
				} else
				// Regarde si c'est le rôle principal
				if ($this->getId()==$role->getUserId()) {
					$returnvalue=true;
				} else {
					// Sinon un focus
					$roleFillers=$role->getRoleFillers();
					foreach ($roleFillers as $roleFiller) {
						if ($roleFiller->getUserId()==$this->getId()) {
							$returnvalue=true;
						}
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
				break;
			case "holacracy\\Organisation" :
				$liste_members=$object->getMembers();

				$returnvalue=false;
				foreach ($liste_members as $member) {
					if ($member->getId()==$this->getId()) $returnvalue=true;
				}
				return $returnvalue;
				break;
			case "holacracy\\User" :
				return false;
			break;

			default:
			trigger_error ("USER can't be member of ".get_class($object)." object", E_USER_WARNING )." object";
					return false;
		}
		
	}
	
	// Paramètres  - DDr 5.6.2014
	// $title : Titre du message  - DDr 5.6.2014
	// $content : Contenu du message  - DDr 5.6.2014
	// $exp : expéditeur. Accèpte rien (adresse noreply), une chaîne de caractères ou un object user - DDr 5.6.2014
	// $delay : temps d'attente avant l'envoi effectif de l'email, en heures - DDr 3.12.2014
	public function sendMessage ($title, $content, $exp=NULL, $delay=NULL) {
		
				// Crée un nouvel objet notification
				$notif=new \holacracy\Notification();
				$notif->setTitle($title);
				$notif->setContent($content);
				$notif->setUser($this);
				//self::getManager()->save($notif);
				
				
				$to      = $this->getEmail();
				$firstname = $this->getFirstName();
				
				// Le titre est-il précédé d'une identification d'organisation entre crochet?  - DDr 5.6.2014
				if (preg_match("/\[.*\]/i", $title)) 
					// Le titre seulement  - DDr 5.6.2014
					$subject = $title;
				else
					// Le titre précédé du labal OMO  - DDr 5.6.2014
					$subject = "[O.M.O] - ".$title;
				
				
				$signature = "<br/>--<br/><br/><div style='font-size:smaller; font-color:#555555'>Ce message vous a été envoyé via l'interface d'OpenMyOrganisation, en tant qu'utilisateur de la version BETA. Merci pour votre participation au développement de cette dernière. N'hésitez pas à nous transmettre toute remarque concernant le fonctionnement des notifications via le formulaire pour signaler les bugs, disponible sur chaque page de l'application: http://".$_SERVER["HTTP_HOST"]."</div>"; // Devra être enlevé par la suite. Attention à l'encodage HTML
				$message = str_replace("\\n","<br/>",$content).$signature;
				
				// Adresse par défaut, NOREPLY  - DDr 5.6.2014
				if (is_null($exp)) {
					$headers = 'From: OpenMyOrganization <noreply@openmyorganization.com>' . "\r\n" ;
				} 
				// Un expéditeur est défini  - DDr 5.6.2014
				else {
					// C'est une chaîne de caractères  - DDr 5.6.2014
					if (is_string($exp)) {
						$headers = 'From: '.$exp . "\r\n" ;
					} else {
						// c'est un objet (type user accepté) - DDr 5.6.2014
						if (is_object($exp)) {
							switch (get_class($exp)) {
								case "holacracy\\User" :
										$headers = 'From: '.$exp->getFirstName()." ".$exp->getLastName()." via [O.M.O] <".$exp->getEmail(). ">" . "\r\n" ;
									break;
								default:
									// L'objet n'est pas accepté, le signale  - DDr 5.6.2014
									trigger_error ("Wrong Expeditor Object for User->sendMessage: ".get_class($exp)." object", E_USER_WARNING );
									return ;				
							}
						}
					}
				}
				
				$headers .='X-Mailer: PHP/' . phpversion();
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";  
				$headers .= "Bcc: david.draeyer@gmail.com\r\n";
				//$headers .= "Date: $entetedate \n";	// Ne fonctionne pas - DDr 5.6.2014			
				
				if (@mail($to, $subject, $message, $headers)) {
					return true; 
				} else {
					return false;
				} 
	}
	
}
?>
