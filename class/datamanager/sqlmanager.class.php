<?php
namespace datamanager;

class SqlManager implements GenericManager 
{
	private $_dbh;							// Base de donnée liée à cet objet
	private $_roles;						// Garde en mémoire les différents rôles
	private $_history = array();			// Cache de l'historique
	private $_policy = array();				// Cache des policy
	private $_contact = array();			// Cache des contacts
	private $_contact_type = array();		// Cache des types de contacts
	private $_comment = array();			// Cache pour les commentaires
	private $_recurrence = array();			// Cache pour éléments de récurrence
	private $_metric = array();				// Cache pour les metrics
	private $_tensionmoi = array();			// Cache pour les tensionsMoi
	private $_checklist = array();			// Cache pour les checklist
	private $_bug = array();				// Cache pour les bugs
	private $_chat = array();				// Cache pour les bugs
	private $_tension = array();				// Cache pour les bugs

	public function __construct($dbh)
	{
		$this->_dbh=$dbh;
	}

	
	public function loadHelp($key) {
		if (is_numeric($key)) {
			$query="select * from t_help where help_id = ".$key;
		} else {
			$query="select * from t_help where '".$key."' REGEXP help_key order by help_key";
		}
		$result=mysql_query($query, $this->_dbh);
		$help=array();
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute un projet
				$help[$i]=new \holacracy\Help($this,mysql_result($result,$i,"help_id"));
				$help[$i]->setKey(mysql_result($result,$i,"help_key"));
				$help[$i]->setText(mysql_result($result,$i,"help_text"));
				$help[$i]->setTitle(mysql_result($result,$i,"help_title"));
			}		
		}
		if (is_numeric($key)) 
			return $help[0];
		else
			return $help;
		
	}
	
	public function loadBugStatus($bug = NULL) {
		if (is_null($bug)) {
			$query="select * from t_statusbug order by stbu_id";
		} else {
			$query="select * from t_statusbug_user left join t_statusbug on (t_statusbug_user.stbu_id=t_statusbug.stbu_id) where t_statusbug_user.bug_id=".$bug->getId()." order by sbus_date DESC";
		}
		$result=mysql_query($query, $this->_dbh);
		$status=array();
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute un projet
				$status[$i]=new \holacracy\BugStatus($this,mysql_result($result,$i,"stbu_id"));
				$status[$i]->setLabel(mysql_result($result,$i,"stbu_label"));
				if (!is_null($bug)) {
					$status[$i]->setComment(mysql_result($result,$i,"sbus_comment"));
					$status[$i]->setDate(mysql_result($result,$i,"sbus_date"));
					$status[$i]->setUserId(mysql_result($result,$i,"user_id"));
					$status[$i]->setBugId(mysql_result($result,$i,"bug_id"));
				}
			}		
		}
		return $status;

	}
	
	public function loadStatus() {
		$query="select * from to_projectstatus";
		$result=mysql_query($query, $this->_dbh);
		$status=array();
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute un projet
				$status[$i]=new \holacracy\Status($this,mysql_result($result,$i,"prst_id"));
				$status[$i]->setLabel(mysql_result($result,$i,"prst_label"));
				$status[$i]->setColor(mysql_result($result,$i,"prst_color"));
			}		
		}
		return $status;
	}
	
	public function loadActionChecks ($object) {
		$query="select * from t_check where acti_id=".$object->getId()." ";
		$check=array();
		$result=mysql_query($query, $this->_dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				//Ajoute une action
				$check[$i]=new \holacracy\Check($this, mysql_result($result,$i,"chec_id"));
				$check[$i]->setUser(mysql_result($result,$i,"user_id"));
				$check[$i]->setRole(mysql_result($result,$i,"role_id"));
				$check[$i]->setAction(mysql_result($result,$i,"acti_id"));
				if (mysql_result($result,$i,"date_check")!="") $check[$i]->setCheckDate(date_create(mysql_result($result,$i,"date_check")));
			}
		}
		return $check;
	}
	
	public function loadDocuments ($object) {
		if (is_numeric($object)) {
			$query="select * from t_document where docu_id=".$object;
			$result=mysql_query($query, $this->_dbh);
			$document=new \holacracy\Document($this,mysql_result($result,0,"docu_id"));
			$document->setTitle(mysql_result($result,0,"docu_title"));
			$document->setDescription(mysql_result($result,0,"docu_description"));
			$document->setName(mysql_result($result,0,"docu_name"));
			$document->setFile(mysql_result($result,0,"docu_file"));
			$document->setURL(mysql_result($result,0,"docu_url"));
			$document->setEditURL(mysql_result($result,0,"docu_url_editable"));
			$document->setVisibility(mysql_result($result,0,"docu_visibility"));
			if (mysql_result($result,0,"role_id")!="") $document->setRole(mysql_result($result,0,"role_id"));
			if (mysql_result($result,0,"user_id")!="") $document->setUser(mysql_result($result,0,"user_id"));
			$document->setCreationDate(date_create(mysql_result($result,0,"docu_date_creation")));
			if (mysql_result($result,0,"docu_date_modification")!="") $document->setModificationDate(date_create(mysql_result($result,0,"docu_date_modification")));
			if (mysql_result($result,0,"docu_date_delete")!="") $document->setDeleteDate(date_create(mysql_result($result,0,"docu_date_delete")));
			
			return $document;
		}
		if (is_object($object)) { //Si c'est un projet ou role
			switch (get_class($object))			
			{

				case "holacracy\\Role" : 

					$query="select * from t_document where docu_date_delete is NULL and role_id=".$object->getId()." ";

				break;
				default :  //Si c'est un role ou cercle
				
			}

			$documents=array();
			//echo $query;
			$result=mysql_query($query, $this->_dbh);
				if ($result>0 && mysql_num_rows($result)>0) {
					for ($i=0; $i<mysql_num_rows($result); $i++) {
						//Ajoute une action
						$documents[$i]=$this->loadDocuments(mysql_result($result,$i,"docu_id"));
					}
				}
			return $documents;
			
		}		}
	
	public function loadCircleActions ($object) {
		return $this->loadActions($object, true);
	}
		
	public function loadActions ($object, $root=false, $context=NULL) { 
		if (is_numeric($object)) {
			$query="select * from t_action where acti_id=".$object;
			$result=mysql_query($query, $this->_dbh);
			$action=new \holacracy\Action($this,mysql_result($result,0,"acti_id"));
			$action->setTitle(mysql_result($result,0,"acti_title"));
			$action->setDescription(mysql_result($result,0,"acti_description"));
			if (mysql_result($result,0,"role_id")!="") $action->setRole(mysql_result($result,0,"role_id"));
			if (mysql_result($result,0,"circ_id")!="") $action->setCircle(mysql_result($result,0,"circ_id"));
			if (mysql_result($result,0,"proj_id")!="") $action->setProject(mysql_result($result,0,"proj_id"));
			if (mysql_result($result,0,"user_id_proposer")!="") $action->setProposer(mysql_result($result,0,"user_id_proposer"));
			if (mysql_result($result,0,"role_id_proposer")!="") $action->setProposerRole(mysql_result($result,0,"role_id_proposer"));
			$action->setCreationDate(date_create(mysql_result($result,0,"date_creation")));
			if (mysql_result($result,0,"date_check")!="") $action->setCheckDate(date_create(mysql_result($result,0,"date_check")));
			if (mysql_result($result,0,"date_delete")!="") $action->setDeleteDate(date_create(mysql_result($result,0,"date_delete")));
			
			return $action;
		}
		if (is_object($object)) { //Si c'est un projet ou role
			switch (get_class($object))			
			{
				case "holacracy\\Project" : 
				$query="select * from t_action where date_delete is NULL and proj_id=".$object->getId()." ";
				break;
			
				
				case "holacracy\\Circle" :
					if ($root) {
						$query="select * from t_action where date_delete is NULL and circ_id=".$object->getId()." and proj_id is NULL and role_id is NULL";
						break;
					}
				case "holacracy\\User" :
					if ($root) {
				
						$query="select * from t_action left join t_check on (t_action.acti_id=t_check.acti_id) left join t_role on (t_action.circ_id=t_role.role_id) where date_delete is NULL and t_check.user_id=".$object->getId()." and proj_id is NULL and t_action.role_id is NULL";
						if (isset($context)) {
							switch (get_class($context))			
							{
								case "holacracy\\Organisation" : 
									$query.=" and t_role.orga_id=".$context->getId();
								break;
								default:
									echo "Erreur: ".get_class($context);
									
							}
						}
						break;
					} else {
						$query="select * from t_action left join t_check on (t_action.acti_id=t_check.acti_id) left join t_role on (t_action.circ_id=t_role.role_id) where date_delete is NULL and t_check.user_id=".$object->getId()." and proj_id is NULL and t_action.role_id is NULL";
						
					}
					break;
				case "holacracy\\Role" : 

					$query="select * from t_action where date_delete is NULL and role_id=".$object->getId()." ";

				break;
				default :  //Si c'est un role ou cercle
					echo "Pas de getAction pour l'objet de type ".get_class($object);
					exit;
			}
			//echo $object->getName();
			$actions=array();
			$result=mysql_query($query, $this->_dbh);
				if ($result>0 && mysql_num_rows($result)>0) {
					for ($i=0; $i<mysql_num_rows($result); $i++) {
						//Ajoute une action
						$actions[$i]=$this->loadActions(mysql_result($result,$i,"acti_id"));
					}
				}
			return $actions;
			
		}		
	}
	public function loadActionsMoi($object,$object2=NULL) { 
		
		if (is_numeric($object)) { //Si c'est numérique c'est un statut
			if($object == 16){ 
			$query="select * from t_actionmoi where acst_id='".$object."'";
			$actionsmoi=array();
			$result=mysql_query($query, $this->_dbh);
			if ($result>0 && mysql_num_rows($result)>0) {
				for ($i=0; $i<mysql_num_rows($result); $i++) {
				//Ajoute une action
				$actionsmoi[$i]=new \holacracy\ActionMoi($this,mysql_result($result,$i,"act_id"));
				$actionsmoi[$i]->setProjectId(mysql_result($result,$i,"proj_id"));
				$actionsmoi[$i]->setRoleId(mysql_result($result,$i,"role_id"));
				$actionsmoi[$i]->setInsert(mysql_result($result,$i,"act_insert"));
				$actionsmoi[$i]->setTitle(mysql_result($result,$i,"act_title"));
				$actionsmoi[$i]->setIdUserFocus(mysql_result($result,$i,"user_id"));
				$actionsmoi[$i]->setStatus(mysql_result($result,$i,"acst_id"));
				$actionsmoi[$i]->setDescription(mysql_result($result,$i,"act_description"));
				$actionsmoi[$i]->setTimeStamp(mysql_result($result,$i,"act_timestamp"));
				$actionsmoi[$i]->setTimeStampDelete(mysql_result($result,$i,"act_timestampdelete"));
				}
			}
			}
		return $actionsmoi;
		}
		
		if (is_object($object)) { //Si c'est un projet ou role
			switch (get_class($object))			
			{
			case "holacracy\\Project" : 
			$query="select * from t_actionmoi where proj_id=".$object->getId()." order by acst_id asc";
			break;
			
			default :  //Si c'est un role ou cercle
			if (!is_null($object2)){ //si on a user
				$query="select * from t_actionmoi where role_id=".$object->getId()." and user_id=".$object2->getId()." order  by acst_id asc";
			} else {
			$query="select * from t_actionmoi where role_id=".$object->getId()." order by acst_id asc";
			}
			break;
			}
			//echo $object->getName();
			$actionsmoi=array();
			$result=mysql_query($query, $this->_dbh);
				if ($result>0 && mysql_num_rows($result)>0) {
					for ($i=0; $i<mysql_num_rows($result); $i++) {
					//Ajoute une action
					$actionsmoi[$i]=new \holacracy\ActionMoi($this,mysql_result($result,$i,"act_id"));
					$actionsmoi[$i]->setProjectId(mysql_result($result,$i,"proj_id"));
					$actionsmoi[$i]->setRoleId(mysql_result($result,$i,"role_id"));
					$actionsmoi[$i]->setInsert(mysql_result($result,$i,"act_insert"));
					$actionsmoi[$i]->setTitle(mysql_result($result,$i,"act_title"));
					$actionsmoi[$i]->setIdUserFocus(mysql_result($result,$i,"user_id"));
					$actionsmoi[$i]->setStatus(mysql_result($result,$i,"acst_id"));
					$actionsmoi[$i]->setDescription(mysql_result($result,$i,"act_description"));
					$actionsmoi[$i]->setTimeStamp(mysql_result($result,$i,"act_timestamp"));
					$actionsmoi[$i]->setTimeStampDelete(mysql_result($result,$i,"act_timestampdelete"));
					}
				}
			return $actionsmoi;
			
		} else { //C'est pas un objet mais un id de type timestampuX
		$query="select * from t_actionmoi where act_id='".$object."'";
		$result=mysql_query($query, $this->_dbh);
			if ($result>0 && mysql_num_rows($result)>0) {
				$actionmoi=new \holacracy\ActionMoi($this,mysql_result($result,0,"act_id"));
				$actionmoi->setProjectId(mysql_result($result,0,"proj_id"));
				$actionmoi->setTitle(mysql_result($result,0,"act_title"));
				$actionmoi->setIdUserFocus(mysql_result($result,0,"user_id"));
				$actionmoi->setRoleId(mysql_result($result,0,"role_id"));
				$actionmoi->setInsert(mysql_result($result,0,"act_insert"));
				$actionmoi->setStatus(mysql_result($result,0,"acst_id"));
				$actionmoi->setDescription(mysql_result($result,0,"act_description"));
				$actionmoi->setTimeStamp(mysql_result($result,0,"act_timestamp"));
				$actionmoi->setTimeStampDelete(mysql_result($result,0,"act_timestampdelete"));
				return $actionmoi;
			}			
		}
	}
	
	public function setImportant ($status, $object, $user) {
		if (is_numeric($user)) {
			if ($status) {
				$query="insert into t_important (proj_id, user_id) VALUES (".$object->getId().", ".$user.")";
			} else {
				$query="delete from t_important where proj_id=".$object->getId()." and user_id=".$user;
			}
		} else {
			if ($status) {
				$query="insert into t_important (proj_id, user_id) VALUES (".$object->getId().", ".$user->getId().")";
			} else {
				$query="delete from t_important where proj_id=".$object->getId()." and user_id=".$user->getId();
			}
		}
		$result=mysql_query($query, $this->_dbh);
	}
		
	public function loadImportantList($object) {
		if (is_numeric($object)) 
		{
		} else {
			switch (get_class($object))			
			{
					case "holacracy\\Project" : 
						$query="select t_important.user_id from t_important where t_important.proj_id=".$object->getId();
						break;
					default:
						trigger_error ("No projects for ".get_class($object).".", E_USER_WARNING );
						exit;
			}
			$result=mysql_query($query, $this->_dbh);
			$returnArray=array();
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute un membre
				$returnArray[$i]=$this->loadMember(mysql_result($result,$i,"user_id"));

			}
		return $returnArray;		

		}

	}
	
	public function loadProjects($object,$user=NULL) {
		if (is_numeric($object)) 
		{
			$query="select * from t_project left join to_projectstatus on (t_project.prst_id=to_projectstatus.prst_id) where proj_id=".$object;
			$result=mysql_query($query, $this->_dbh);
			if ($result>0 && mysql_num_rows($result)>0) {
				$project=new \holacracy\Project($this,mysql_result($result,0,"proj_id"));
				$project->setRole(mysql_result($result,0,"role_id"));
				if (mysql_result($result,0,"user_id")>0) $project->setUser(mysql_result($result,0,"user_id"));
				if (mysql_result($result,0,"user_id_creator")>0) $project->setProposer(mysql_result($result,0,"user_id_creator"));
				$project->setTitle(mysql_result($result,0,"proj_title"));

				$project->setDescription(mysql_result($result,0,"proj_description"));
				$project->setPosition(mysql_result($result,0,"proj_position"));
				$project->setStatus(mysql_result($result,0,"prst_id"));
				$project->setVisibility(mysql_result($result,0,"proj_visibility"));
				$project->setShowCircle(mysql_result($result,0,"proj_showcircle"));
				$project->setProposer(mysql_result($result,0,"user_id_proposer"),mysql_result($result,0,"role_id_proposer"));
				$project->setCreationDate(date_create(mysql_result($result,0,"proj_dateCreation")));
				if (mysql_result($result,0,"proj_dateModif")!="")
					$project->setModificationDate(date_create(mysql_result($result,0,"proj_dateModif")));
				if (mysql_result($result,0,"proj_dateStatus")!="")
						$project->setStatusDate(date_create(mysql_result($result,0,"proj_dateStatus")));
				$project->setType(mysql_result($result,0,"typr_id"));		
				
				return $project;
			}
		} else {
		switch (get_class($object))			
		{
				case "holacracy\\Circle" : 
				case "holacracy\\Role" : 
					if (!is_null($user)){ 
						if (is_object($user)) {$user = "and (t_project.user_id=".$user->getId()." or (!t_project.user_id>0 and t_role.user_id=".$user->getId().")) ";}
							else{$user = "and (t_project.user_id=".$user." or (!t_project.user_id>0 and t_role.user_id=".$user.")) ";}
					}else{$user="";}
					$query="select * from t_project left join to_projectstatus on (t_project.prst_id=to_projectstatus.prst_id) left join t_role on (t_project.role_id=t_role.role_id) where t_project.role_id=".$object->getId()." ".$user." order by t_project.prst_id, proj_position, proj_dateStatus";
					//echo $query;
					break;
				default:
					trigger_error ("No projects for ".get_class($object).".", E_USER_WARNING );
					exit;
		}
		$result=mysql_query($query, $this->_dbh);
		$projects=array();
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				$tmp=$this->loadProjects(mysql_result($result,$i,"proj_id"));
				// L'utilisateur courant a-t-il le droit de voir ce projet?
				if ($tmp->getVisibility()==1 ||     // Projet public, visible par tous
					$tmp->getUserId()==$_SESSION["currentUser"]->getId() || // Projet associé à la personne, visible dans tous les cas
					($tmp->getVisibility()==4 && $_SESSION["currentUser"]->isRole($tmp->getRole())) ||
					($tmp->getVisibility()==3 && $_SESSION["currentUser"]->isMember($tmp->getRole()->getSuperCircle())) ||
					($tmp->getVisibility()==2 && $_SESSION["currentUser"]->isMember($tmp->getRole()->getSuperCircle()->getOrganisation())) 
				   ) {
					   $projects[$i]=$tmp;
				}

			}		
		}
		return $projects;
		}
	}
		
	public function loadTransaction($object=null) {
		if (is_null($object)) 
		{
				trigger_error ("loadTransaction not implemented for null value.", E_USER_WARNING );
				exit;

		} else
		if (is_numeric($object)) 
		{
			$query="select * from t_transaction where tran_id =".$object;
		} else 
		if (is_string($object)) 
		{
			$query="select * from t_transaction where tran_tocken ='".$object."'";
		} else 
		if (is_object($object)) 
		{
			switch (get_class($object))			
			{
				default:
					trigger_error ("loadTransaction not implemented for ".get_class($object)." objects.", E_USER_WARNING );
				exit;
			}
		} else {
			trigger_error ("loadTransaction not implemented for unknown type.", E_USER_WARNING );
				exit;
		}
		$result=mysql_query($query, $this->_dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			$transaction=new \security\Transaction( $this,mysql_result($result,0,"tran_id"));
			$transaction->setTocken(mysql_result($result,0,"tran_tocken"));
			$transaction->setStartDate(mysql_result($result,0,"tran_startDate"));
			$transaction->setPrice(mysql_result($result,0,"tran_price"));
			$transaction->setOrganisationId(mysql_result($result,0,"orga_id"));
			$transaction->setUserId(mysql_result($result,0,"user_id"));
			$transaction->setSubscriptionId(mysql_result($result,0,"tyab_id"));
			return $transaction;
		} else {
			trigger_error ("ID not found for loadTransaction.", E_USER_WARNING );
		}
	}

	public function loadSubscription($object=null) {
		if (is_null($object)) 
		{
				trigger_error ("loadSubscription not implemented for null value.", E_USER_WARNING );
				exit;

		} else
		if (is_numeric($object)) 
		{
			$query="select * from t_abonnement left join t_typeabonnement on (t_abonnement.tyab_id=t_typeabonnement.tyab_id) where abon_id =".$object;
		} else 
		if (is_object($object)) 
		{
			switch (get_class($object))			
			{
				default:
					trigger_error ("loadSubscription not implemented for ".get_class($object)." objects.", E_USER_WARNING );
				exit;
			}
		} else {
			trigger_error ("loadSubscription not implemented for unknown type.", E_USER_WARNING );
				exit;
		}
		$result=mysql_query($query, $this->_dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			$subscription=new \security\Subscription( $this,mysql_result($result,0,"abon_id"));
			$subscription->setName(mysql_result($result,0,"tyab_nom"));
			$subscription->setStartDate(mysql_result($result,0,"abon_date"));
			$subscription->setDuration(mysql_result($result,0,"abon_duree"));
			$subscription->setPrice(mysql_result($result,0,"abon_prix"));
			$subscription->setOrganisationId(mysql_result($result,0,"orga_id"));
			return $subscription;
		} else {
			trigger_error ("ID not found for loadSubscription.", E_USER_WARNING );
		}
	}
	
	public function loadSubscriptionType($object=null) {
		if (is_null($object)) 
		{
				trigger_error ("loadSubscriptionType not implemented for null value.", E_USER_WARNING );
				exit;

		} else
		if (is_numeric($object)) 
		{
			$query="select * from t_typeabonnement where tyab_id =".$object;
		} else {
			trigger_error ("loadSubscriptionType not implemented for unknown type.", E_USER_WARNING );
				exit;
		}
		$result=mysql_query($query, $this->_dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			$subscription=new \security\Subscription( $this,mysql_result($result,0,"tyab_id"));
			$subscription->setName(mysql_result($result,0,"tyab_nom"));
			$subscription->setDescription(mysql_result($result,0,"tyab_description"));
			$subscription->setDuration(mysql_result($result,0,"tyab_duree"));
			$subscription->setPrice(mysql_result($result,0,"tyab_prix"));
			return $subscription;
		} else {
			trigger_error ("ID not found for loadSubscription.", E_USER_WARNING );
		}
	}
	
	public function loadSubscriptions($object=null) {
		if (is_null($object)) 
		{
			// Traitement particulier, charge les modèles d'abonnement
			$query="select * from t_typeabonnement where tyab_actif=1";
			$result=mysql_query($query, $this->_dbh);
			$subscriptions=array();
			if ($result>0 && mysql_num_rows($result)>0) {
				for ($i=0; $i<mysql_num_rows($result); $i++) {
					$subscriptions[$i]=$this->loadSubscriptionType(mysql_result($result,$i,"tyab_id"));
				}
			}
			return $subscriptions;
			exit;

		} else
		if (is_numeric($object)) 
		{
				trigger_error ("loadSubscriptions not implemented for numeric value.", E_USER_WARNING );
				exit;

		} else 
		if (is_object($object)) 
		{
			switch (get_class($object))			
			{
				case "holacracy\\Organisation" : 
					$query="select * from t_abonnement where orga_id=".$object->getId();
				break;
				default:
					trigger_error ("loadSubscriptions not implemented for ".get_class($object)." objects.", E_USER_WARNING );
				exit;
			}
		} else {
			trigger_error ("loadSubscriptions not implemented for unknown type.", E_USER_WARNING );
				exit;
		}
		$result=mysql_query($query, $this->_dbh);
		$subscriptions=array();
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				$subscriptions[$i]=$this->loadSubscription(mysql_result($result,$i,"abon_id"));
			}
		}
		return $subscriptions;
		
	} 
	
	public function loadOrganisation($id=null) {

		// Affiche les organisations visibles pour un membre
		if (is_object($id) && get_class($id)=="holacracy\\User") {
			$isAdmin=$id->isAdmin();
			$userId=$id->getId();
			$id=null;
		}

		// L'ID est-il spécifié dans l'appel de la fonction?
		if (!isset($id)) {
			// Si NON, charge la liste de toutes les organisations (uniquement les publics ou celles dont le user est membre)
			// Charge toutes les org pour l'Admin global
			if (isset($isAdmin) && $isAdmin)
				$query="select * from t_organisation order by orga_name";
			else 
			if (isset($userId)) 
				$query="select * from t_organisation left join t_organisationmember on (t_organisation.orga_id=t_organisationmember.orga_id && t_organisationmember.user_id=".$userId.") where orga_public=2 or t_organisationmember.user_id=".$userId." order by orga_name";
			else
				$query="select * from t_organisation where orga_public=2 order by orga_name";
			$result=mysql_query($query, $this->_dbh);
			$returnArray=array();
			if ($result>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute un membre
				$returnArray[$i]=$this->loadOrganisation(mysql_result($result,$i,"orga_id"));
			}
			} else {
				//echo $query;
			}
			return $returnArray;		
			
		} else {
			// Si OUI, charge une organisation en particulier
			$query="select * from t_organisation where orga_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a ORGANISATION table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID ('.$id.') not found in ORGANISATION table.', E_USER_WARNING );
				return ;
			} 	
			$organisation=new \holacracy\Organisation($this,$id);	
			
			$organisation->setName(mysql_result($result,0,"orga_name"));
			$organisation->setShortName(mysql_result($result,0,"orga_shortname"));
			$organisation->setDescription(mysql_result($result,0,"orga_description"));
			$organisation->setVision(mysql_result($result,0,"orga_vision"));
			$organisation->setVisionDescription(mysql_result($result,0,"orga_visiontxt"));
			$organisation->setMission(mysql_result($result,0,"orga_mission"));
			$organisation->setMissionDescription(mysql_result($result,0,"orga_missiontxt"));
			$organisation->setPurpose(mysql_result($result,0,"orga_purpose"));
			$organisation->setPurposeDescription(mysql_result($result,0,"orga_purposetxt"));
			$organisation->setWebSite(mysql_result($result,0,"orga_website"));
			$organisation->setVisibility(mysql_result($result,0,"orga_public"));
	
			return $organisation;
		}
	}
	
	
	
	public function loadPreference($object) {
		if (is_numeric($object)) {
			/* Pas de sens vu le système de stockage qui n'est pas un objet
			  
			 $query="select * from t_notification where noti_id=".$object." ";
			$result=mysql_query($query, $this->_dbh);
			if ($result>0 && mysql_num_rows($result)>0) {
				$userNotification=new \holacracy\Notification($this,mysql_result($result,0,"noti_id"));
				$userNotification->setTitle(mysql_result($result,0,"noti_title"));
				$userNotification->setHTMLContent(mysql_result($result,0,"noti_htmlcontent"));
				$userNotification->setTextContent(mysql_result($result,0,"noti_textcontent"));
				$userNotification->setDelay(mysql_result($result,0,"noti_delay"));
				$userNotification->setUserId(mysql_result($result,0,"user_id"));
				return $userNotification;
			}*/
			
		} else if (is_object($object)) {
			// Fonctionne différemment selon le type d'objet
			switch (get_class($object))			
			{
				case "holacracy\\User" : 
					$query ="select * from t_preference where user_id=".$object->getId()."";
					$result=mysql_query($query, $this->_dbh);
					$userPreferences=array();
					if ($result>0 && mysql_num_rows($result)>0) {
						for ($i=0; $i<mysql_num_rows($result); $i++) {
							// Ajoute une redevabilité
							$userPreferences[mysql_result($result,$i,"pref_key")]=mysql_result($result,$i,"pref_value");
						}
					}
					return $userPreferences;
					break;
			}
		}
		// Alerte erreur : cas non traité!
	}	
	public function loadNotification($object) {
		if (is_numeric($object)) {
			$query="select * from t_notification where noti_id=".$object." ";
			$result=mysql_query($query, $this->_dbh);
			if ($result>0 && mysql_num_rows($result)>0) {
				$userNotification=new \holacracy\Notification($this,mysql_result($result,0,"noti_id"));
				$userNotification->setTitle(mysql_result($result,0,"noti_title"));
				$userNotification->setHTMLContent(mysql_result($result,0,"noti_htmlcontent"));
				$userNotification->setTextContent(mysql_result($result,0,"noti_textcontent"));
				$userNotification->setDelay(mysql_result($result,0,"noti_delay"));
				$userNotification->setUserId(mysql_result($result,0,"user_id"));
				return $userNotification;
			}
			
		} else if (is_object($object)) {
			// Fonctionne différemment selon le type d'objet
			switch (get_class($object))			
			{
				case "holacracy\\User" : 
					$query ="select * from t_notification where user_id=".$object->getId()." order by noti_publication_date";
					$result=mysql_query($query, $this->_dbh);
					$userNotification=array();
					if ($result>0 && mysql_num_rows($result)>0) {
						for ($i=0; $i<mysql_num_rows($result); $i++) {
							// Ajoute une redevabilité
							$userNotification[$i]=$this->loadNotification(mysql_result($result,$i,"noti_id"));
						}
					}
					return $userNotification;
					break;
			}
		}
		//return $userNotification; 
	}
	
	// Charge une ou plusieurs redevabilité en fonction du type d'objet passé
	public function loadAccountability($object) {	
		if (is_numeric($object)) {
			$query="select * from t_accountability where acco_id=".$object." ";
			$result=mysql_query($query, $this->_dbh);
			if ($result>0 && mysql_num_rows($result)>0) {
				$roleAccountability=new \holacracy\Accountability($this,mysql_result($result,0,"acco_id"));
				$roleAccountability->setDescription(mysql_result($result,0,"acco_description"));
				$roleAccountability->setRole(mysql_result($result,0,"role_id"));
			}
			
		} else if (is_object($object)) {
				$result=mysql_query($query, $this->_dbh);
		$roleAccountabilities=array();
	// Fonctionne différemment selon le type d'objet
		}
		return $roleAccountability;
	}
	
	// !!! Decrepated : doit être remplacé par loadAccountability(ROLE)
	// Charge les redevabilités d'un rôle 
	public function loadAccountabilities($id) {
		$query="select * from t_accountability where role_id=".$id." and acco_active=1 order by acco_description ";
		$result=mysql_query($query, $this->_dbh);
		$roleAccountabilities=array();
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute une redevabilité
				$roleAccountabilities[$i]=new \holacracy\Accountability($this,mysql_result($result,$i,"acco_id"));
				$roleAccountabilities[$i]->setDescription(mysql_result($result,$i,"acco_description"));
				$roleAccountabilities[$i]->setRole(mysql_result($result,$i,"role_id"));

			}		
		}
		return $roleAccountabilities;
	}
	
	public function loadRoleFillers($id) {
		$query="select * from t_rolefiller left join t_user on (t_rolefiller.user_id=t_user.user_id) where role_id=".$id." order by (isnull(rofi_focus))";
		$result=mysql_query($query, $this->_dbh);
		$roleFillers=array();
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute un cercle
				$roleFillers[$i]=new \holacracy\RoleFiller($this,mysql_result($result,$i,"rofi_id"));
				$this->_populateMember($roleFillers[$i],$result,$i,NULL);	
				$roleFillers[$i]->setFocus(mysql_result($result,$i,"rofi_focus"));
				$roleFillers[$i]->setUserId(mysql_result($result,$i,"user_id"));
				$roleFillers[$i]->setRoleId(mysql_result($result,$i,"role_id"));
			}		
		}
		return $roleFillers;
	}
	
	public function loadRoleFiller($id) {
		$query="select * from t_rolefiller left join t_user on (t_rolefiller.user_id=t_user.user_id) where rofi_id=".$id." order by (isnull(rofi_focus))";
		$result=mysql_query($query, $this->_dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute un cercle
				$roleFiller=new \holacracy\RoleFiller($this,mysql_result($result,$i,"rofi_id"));
				$this->_populateMember($roleFiller,$result,$i,NULL);	
				$roleFiller->setFocus(mysql_result($result,$i,"rofi_focus"));
				$roleFiller->setUserId(mysql_result($result,$i,"user_id"));
				$roleFiller->setRoleId(mysql_result($result,$i,"role_id"));
			}		
		}
		return $roleFiller;
	}
	
	public function loadRoleFillerFromUser($id) {
		$query="select * from t_user where user_id=".$id."";
		$result=mysql_query($query, $this->_dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				// Ajoute un cercle
				$roleFiller=new \holacracy\RoleFiller($this,NULL);
				$this->_populateMember($roleFiller,$result,$i,NULL);	
				$roleFiller->setUserId(mysql_result($result,$i,"user_id"));
			}		
		}
		return $roleFiller;
	}
	
	public function loadCircles($object) {
		//En fonction du type d'objet, choisi de charger les cercles d'une organisation ou d'un autre cercle
		switch (get_class($object))			
		{
				case "holacracy\\Organisation" : 
					$query="select t_role.role_id, count(souscercle.role_id) as cpt from t_role left join t_role as souscercle on (t_role.role_id=souscercle.role_id_superCircle and souscercle.role_active=1) where t_role.role_active=1 and t_role.orga_id=".$object->getId()." and t_role.role_id_superCircle=0  group by t_role.role_id order by t_role.roty_id";
				
					break;
				case "holacracy\\Circle" :
					$query="select t_role.role_id, count(souscercle.role_id) as cpt from t_role left join t_role as souscercle on (t_role.role_id=souscercle.role_id_superCircle and souscercle.role_active=1) where t_role.role_active=1 and t_role.role_id_supercircle=".$object->getId()." group by t_role.role_id order by t_role.roty_id";
				break;
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();

		for ($i=0; $i<mysql_num_rows($result); $i++) {
			if (mysql_result($result,$i,"cpt")>0) {
				// Ajoute un cercle
				$returnArray[$i+1]=$this->loadCircle(mysql_result($result,$i,"role_id"));
				
			} 
		}		
		return $returnArray;

	}
	
	// Initialise un membre à partir d'un recordset
	private function _populateMember($member, $result, $row, $orguser) {
		$member->setFirstName(mysql_result($result,$row,"user_firstName"));
		$member->setLastName(mysql_result($result,$row,"user_lastName"));
		$member->setUserName(mysql_result($result,$row,"user_userName"));
		$member->setEmail(mysql_result($result,$row,"user_email"));
		$member->setUserLangue(mysql_result($result,$row,"user_lang"));
		$member->setActive(mysql_result($result,$row,"user_isActive"));
		$member->setCode(mysql_result($result,$row,"user_code"));
		$member->setAdmin(mysql_result($result,$row,"user_isAdmin")!=0);
		$member->setDevelopper(mysql_result($result,$row,"user_isDevelopper")!=0);
		if (mysql_result($result,$row,"user_lastConnexionDate")!="") 
			$member->setLastConnexion(date_create(mysql_result($result,$row,"user_lastConnexionDate")));
		$member->setOrgsUser($orguser);
	}
	
	
	public function loadUser($id) {
		if (!($id>0)) {
				trigger_error ('Select an ID for function loadUser().', E_USER_WARNING );
				return ;
		} else {
			$query="select * from t_user where user_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID ('.$id.') not found in USER table.', E_USER_WARNING );
				return ;
			} else {	

				$returnValue=new \holacracy\User( $this,mysql_result($result,0,"user_id"));
				$this->_populateMember($returnValue,$result,0,NULL);	
				return $returnValue;	
			}	
		}
	}
	public function loadMember($id) {
		return $this->loadUser($id);
	}	
	
	
	// Recherche dans tous les objets
	public function findAll(\holacracy\Filter $filter = NULL) {
		$query="select 1 as type, acco_id as obj_id, t_role.role_id  from t_accountability join t_role on (t_accountability.role_id=t_role.role_id) where acco_description like '%".$filter->getCriteria("keyword")."%' and t_role.role_active=1 ";
		$query.="union ";
		$query.="select 2 as type, scop_id, t_role.role_id from t_scope join t_role on (t_scope.role_id=t_role.role_id) where (scop_description like '%".$filter->getCriteria("keyword")."%' or scop_politiques like '%".$filter->getCriteria("keyword")."%') and t_role.role_active=1 ";
		$query.="union ";
		$query.="select 3 as type, role_id, role_id from t_role where (role_name like '%".$filter->getCriteria("keyword")."%' or role_purpose like '%".$filter->getCriteria("keyword")."%' or role_strategy like '%".$filter->getCriteria("keyword")."%') and t_role.role_active=1 ";
		// Ajouter les politiques
		$query.="union ";
		$query.="select 4 as type, poli_id, t_role.role_id from t_policy join t_role on (t_policy.role_id=t_role.role_id) where (poli_title like '%".$filter->getCriteria("keyword")."%' or poli_description like '%".$filter->getCriteria("keyword")."%') and t_role.role_active=1 ";
		$query.="order by role_id ";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément selon son type
			switch (mysql_result($result,$i,"type")) {
				case 1: 
				
					$tmp=$this->loadAccountability(mysql_result($result,$i,"obj_id")); 
					$org=$tmp->getRole()->getOrganisation();
					
					break;
				case 2: 
								
					$tmp=$this->loadScope(mysql_result($result,$i,"obj_id")); 
					$org=$tmp->getRole()->getOrganisation();
					
					break;
				case 3: 
								
					$tmp=$this->loadRole(mysql_result($result,$i,"obj_id")); 
					$org=$tmp->getOrganisation();
					
					break;		
				case 4: 
								
					$tmp=$this->loadPolicy(mysql_result($result,$i,"obj_id")); 
					$org=$tmp->getCircle()->getOrganisation();
					
					break;		
			}
			// Ajoute l'élément seulement s'il correspond au contexte
			// Actuellement, utilise l'objet pour définir si le contexte est bon. Si cela devient trop lent, il faudra l'intégrer dans le SQL
			if ((isset($org) && $filter->getCriteria("organisation")==$org->getId()) || 
			  ($filter->getCriteria("organisation")=="" && 
			  $_SESSION["currentUser"]->isMember($org))) 
			  
				$returnArray[]=$tmp;
		}
		
		return $returnArray;			
	}
	
	// Cherche une liste d'utilisateurs selon un filtre variable
	public function findUsers(\holacracy\Filter $filter) {
		$query="select * from t_user where 1=1 ";
		if (!is_null($filter->getCriteria("all"))) {$query.=" and user_id>1 and (user_userName='".$filter->getCriteria("all")."' or user_email='".$filter->getCriteria("all")."' or user_lastName='".$filter->getCriteria("all")."' or user_firstName='".$filter->getCriteria("all")."')";}
		if (!is_null($filter->getCriteria("userId"))) {$query.=" and user_id='".$filter->getCriteria("userId")."'";}
		if (!is_null($filter->getCriteria("userName"))) {$query.=" and BINARY user_userName='".$filter->getCriteria("userName")."'";}
		if (!is_null($filter->getCriteria("password"))) {$query.=" and user_password='".md5($filter->getCriteria("password"))."'";}
		if (!is_null($filter->getCriteria("email"))) {$query.=" and user_email='".$filter->getCriteria("email")."'";}
		if (!is_null($filter->getCriteria("code"))) {$query.=" and user_code='".$filter->getCriteria("code")."'";}
		$query.="";
		$result=mysql_query($query, $this->_dbh);
		

		if (mysql_num_rows($result)==0 && strlen($filter->getCriteria("all"))>2 && !is_null($filter->getCriteria("all"))) {
			$query="select * from t_user where 1=1 ";
			$query.=" and user_id>1 and (user_userName like '%".$filter->getCriteria("all")."%' or user_email like '".$filter->getCriteria("all")."%' or user_lastName like '%".$filter->getCriteria("all")."%' or user_firstName like '%".$filter->getCriteria("all")."%')";
			$result=mysql_query($query, $this->_dbh);
		}
		
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un membre
			$returnArray[$i]=new \holacracy\User( $this,mysql_result($result,$i,"user_id"));
			$id = mysql_result($result,$i,"user_id");
			$query="select orga_id,user_isAdmin from t_organisationmember where user_id=".$id;
			$result2=mysql_query($query, $this->_dbh);
			$orgArray=array();
			for ($j=0; $j<@mysql_num_rows($result2); $j++) {
			$orgArray[mysql_result($result2,$j,"orga_id")]['is_admin'] = mysql_result($result2,$j,"user_isAdmin"); 
			}
			
			$this->_populateMember($returnArray[$i],$result,$i,$orgArray);			
		}
		return $returnArray;		

	}
	
	public function loadAllLanguage() {
	$query="select lang_name from t_language";
	$result=mysql_query($query, $this->_dbh);
	$returnArray=array();
	for ($i=0; $i<mysql_num_rows($result); $i++) {
		$returnArray[$i+1]= mysql_result($result,$i,"lang_name");
		}
	return $returnArray;
	}

	public function loadValue($id) {
		$query="select * from t_value where valu_id=".$id." ";
		$result=mysql_query($query, $this->_dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			$value=new \holacracy\Value($this,mysql_result($result,0,"valu_id"));
			$value->setLabel(mysql_result($result,0,"valu_title"));
			$value->setOrganisationId(mysql_result($result,0,"orga_id"));
		}			
		return $value;	
	}
	
	public function loadPrinciple($id) {
		$query="select * from t_principle where prin_id=".$id." ";
		$result=mysql_query($query, $this->_dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			$principle=new \holacracy\Principle($this,mysql_result($result,0,"prin_id"));
			$principle->setDescription(mysql_result($result,0,"prin_description"));
			$principle->setValueId(mysql_result($result,0,"valu_id"));
		}			
		return $principle;	
	}

	public function loadValueListe($object=NULL) {
		
		if (is_null($object)) {
			$query="select * from t_value";
		} else {	
			switch (get_class($object))	{
				case "holacracy\\Organisation" :
					$query="select * from t_value where orga_id=".$object->getId()." ";
				break;
				default: 
					trigger_error ("Unknown CLASS for loadValueListe", E_USER_WARNING );
					return ;	
			}
		}
		$query.=" order by rand()";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un membre
			$returnArray[$i]=$this->loadValue(mysql_result($result,$i,"valu_id"));

		}
		return $returnArray;		
	}

	public function loadPrincipleListe($object=NULL) {
		
		if (is_null($object)) {
			$query="select * from t_principle";
		} else {	
			switch (get_class($object))	{
				case "holacracy\\Value" :
					$query="select * from t_principle where valu_id=".$object->getId()." ";
				break;
				default: 
					trigger_error ("Unknown CLASS for loadPrincipleListe", E_USER_WARNING );
					return ;	
			}
		}
		$query.=" order by rand()";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un membre
			$returnArray[$i]=$this->loadPrinciple(mysql_result($result,$i,"prin_id"));

		}
		return $returnArray;		
	}

	// Charge dynamiquement et seulement si nécessaire les administrateurs d'un objet
	public function loadAdminListe($object=NULL) {
		
		if (is_null($object)) {
			$query="select * from t_user where user_isAdmin=1";
		} else {
	
		switch (get_class($object))	{
		/*	case "holacracy\\Circle" :
				$query="select t_user.user_id, t_user.user_firstname, t_user.user_lastname  from t_circlemember left join t_user on (t_circlemember.user_id=t_user.user_id) where role_id=".$object->getId()." UNION DISTINCT select t_user.user_id, t_user.user_firstname, t_user.user_lastname from t_user where t_user.user_id='".$object->getUserId()."' UNION DISTINCT select t_user.user_id, t_user.user_firstname, t_user.user_lastname  from t_role join t_user on (t_role.user_id=t_user.user_id) where t_role.role_id_superCircle=".$object->getId()." UNION DISTINCT select t_user.user_id, t_user.user_firstname, t_user.user_lastname from t_user join t_role as r1 on (r1.user_id=t_user.user_id and r1.roty_id=4) join t_role as r2 on (r1.role_id_superCircle=r2.role_id) where r2.role_id_superCircle=".$object->getId()."";
			
			break;*/
			case "holacracy\\Organisation" :
				$query="select t_user.user_id, t_user.user_firstname, t_user.user_lastname  from t_organisationmember left join t_user on (t_organisationmember.user_id=t_user.user_id) where orga_id=".$object->getId()." and t_organisationmember.user_isAdmin=1";
			break;
			default: 
				trigger_error ("Unknown CLASS for loadMemberList", E_USER_WARNING );
				return ;	
		}
		}
		$query.=" order by user_firstname, user_lastname";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un membre
			$returnArray[$i]=$this->loadMember(mysql_result($result,$i,"user_id"));

		}
		return $returnArray;		
	}

	// Charge dynamiquement et seulement si nécessaire les membres d'un cercle
	public function loadMemberListe($object=NULL) {
		
		if (is_null($object)) {
			$query="select * from t_user where user_isAdmin=0";
		} else {
	
		switch (get_class($object))	{
			case "holacracy\\Circle" :
				$query="select t_user.user_id, t_user.user_firstname, t_user.user_lastname  from t_circlemember left join t_user on (t_circlemember.user_id=t_user.user_id) where role_id=".$object->getId()." UNION DISTINCT select t_user.user_id, t_user.user_firstname, t_user.user_lastname from t_user where t_user.user_id='".$object->getUserId()."' UNION DISTINCT select t_user.user_id, t_user.user_firstname, t_user.user_lastname  from t_role join t_user on (t_role.user_id=t_user.user_id) where t_role.role_active=1 and t_role.role_id_superCircle=".$object->getId()." UNION DISTINCT select t_user.user_id, t_user.user_firstname, t_user.user_lastname from t_user join t_role as r1 on (r1.user_id=t_user.user_id and r1.roty_id=4) join t_role as r2 on (r1.role_id_superCircle=r2.role_id) where r2.role_id_superCircle=".$object->getId()."";
			
			break;
			case "holacracy\\Organisation" :
				$query="select t_user.user_id, t_user.user_firstname, t_user.user_lastname  from t_organisationmember left join t_user on (t_organisationmember.user_id=t_user.user_id) where orga_id=".$object->getId()." ";
			break;
			default: 
				trigger_error ("Unknown CLASS for loadMemberList", E_USER_WARNING );
				return ;	
		}
		}
		$query.=" order by user_firstname, user_lastname";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un membre
			//echo "<!-- load user ".mysql_result($result,$i,"user_id")." -->";
			if (mysql_result($result,$i,"user_id")>0)
				$returnArray[$i]=$this->loadMember(mysql_result($result,$i,"user_id"));

		}
		return $returnArray;		
	}

	
	// ATTENTION: Pas très propre... à remplacer
	// Ajoute un user à un cercle
	public function addMemberCircle($user,$role) {
		$query="insert into t_circlemember (user_id, role_id) VALUES (".$user.",".$role.")";
		mysql_query($query, $this->_dbh);
		
		// L'ajoute également à l'organisation
		$organisation=$this->loadRole($role)->getOrganisation();
		$query="insert into t_organisationmember (user_id, orga_id) VALUES (".$user.",".$organisation->getId().")";
		//echo $query;
		mysql_query($query, $this->_dbh);

	}
	
	// Supprimer un membre de cercle
	public function delMemberCircle($user,$circle) {
	$query = "delete from t_circlemember WHERE t_circlemember.user_id = ".$user." AND t_circlemember.role_id = ".$circle."";
	mysql_query($query, $this->_dbh);

	
	//Action pour desaffecter automatiquement les roles affectes à cet user
	$query = "select * FROM t_rolefiller WHERE role_id IN (select role_id from t_role where role_id_superCircle = ".$circle." and user_id = ".$user.")";
	$result=mysql_query($query, $this->_dbh);
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Requete pour supprimer l'affectation
			$roledesaff = mysql_result($result,$i,"role_id");
			$query = "delete from t_rolefiller WHERE t_rolefiller.user_id = ".$user." AND t_rolefiller.role_id = ".$roledesaff."";
			mysql_query($query, $this->_dbh);
		}
	}
	
	// Affecter un membre de cercle à un rôle 
	public function addMemberFillerRole($role,$user) {
	//Obligation d'être membre de cercle pour être affilie au role
	$query="insert into t_rolefiller (role_id, user_id) VALUES (".$role.",".$user.")";
	mysql_query($query, $this->_dbh);
	
	}
	
	// Desaffecter un membre de cercle de son role existant - DDR -> Innutilisé pour l'instant je crois.
	public function delMemberFillerRole($role,$user) {
		$query = "delete from t_rolefiller WHERE t_rolefiller.user_id = ".$user." AND t_rolefiller.role_id = ".$role."";
		mysql_query($query, $this->_dbh);

	}
	
	// Affecter un membre à une organisation 
	public function addMemberOrganisation($user, $orga, $admin=0) {
		// Le membre est-il déjà affilié? Dans ce cas, le supprime d'abord
		$query="delete from t_organisationmember where user_id='".$user."' and orga_id='".$orga."'";

		mysql_query($query, $this->_dbh);
		$query="insert into t_organisationmember (orga_id, user_id, user_isAdmin) VALUES ('".$orga."','".$user."',".$admin.")";

		mysql_query($query, $this->_dbh);
	
	}
	
	// Desaffecter un membre d'une organisation
	public function delMemberOrganisation($user, $orga) {
	$query = "delete from t_organisationmember WHERE user_id = ".$user." AND orga_id = ".$orga."";
	mysql_query($query, $this->_dbh);
	}
	
	

	public function loadScope($id) {
		$query="select * from t_scope where scop_id=".$id;
		$result=mysql_query($query, $this->_dbh);
		$scope= new \holacracy\Scope( $this,mysql_result($result,0,"scop_id"));
		$scope->setDescription(mysql_result($result,0,"scop_description"));
		$scope->setPolitiques(mysql_result($result,0,"scop_politiques"));
		$scope->setRoleId(mysql_result($result,0,"role_id"));
		return $scope;
	}
		
	// Charge dynamiquement et seulement si nécessaire les domaines d'un cercle ou d'un rôle
	public function loadScopes($role) {
		$query="select * from t_scope where role_id=".$role->getId()." order by scop_description";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un membre
			$returnArray[$i]=$this->loadScope(mysql_result($result,$i,"scop_id"));
		}
		return $returnArray;		
	}
	
	// Charge dynamiquement tous les domaines d'un cercle (donc de ses sous-rôles
	public function loadAllScopes($circle) {
		$query="select * from t_scope left join t_role on (t_scope.role_id=t_role.role_id)  where t_role.role_active=1 and t_role.role_id_superCircle=".$circle->getId()." order by scop_description";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un membre
			$returnArray[$i]=$this->loadScope(mysql_result($result,$i,"scop_id"));
		}
		return $returnArray;		
	}
	
	public function loadMeeting($id) {
		$query="select * from t_meeting left join to_meetingtype on (t_meeting.mety_id=to_meetingtype.mety_id) where t_meeting.meet_id=0".$id;
		$result=mysql_query($query, $this->_dbh);
		$meeting= new \holacracy\Meeting($this,mysql_result($result,0,"meet_id"));
		$meeting->setDate(date_create(mysql_result($result,0,"meet_date")));
		if (mysql_result($result,0,"meet_opening")!="") $meeting->setOpeningTime(date_create(mysql_result($result,0,"meet_opening")));
		if (mysql_result($result,0,"meet_closing")!="") $meeting->setClosingTime(date_create(mysql_result($result,0,"meet_closing")));
		$meeting->setStartTime(mysql_result($result,0,"meet_starttime"));
		$meeting->setEndTime(mysql_result($result,0,"meet_endtime"));
		$meeting->setLocation(mysql_result($result,0,"meet_location"));
		$meeting->setMeetingTypeId(mysql_result($result,0,"mety_id"));
		$meeting->setSecretaryId(mysql_result($result,0,"memb_id_secretary"));
		$meeting->setMeetingType(mysql_result($result,0,"mety_name"));
		$meeting->setOrganisation(mysql_result($result,0,"orga_id"));
		$meeting->setCircle(mysql_result($result,0,"role_id_circle"));
		$meeting->setScratchpad(mysql_result($result,0,"meet_scratchpad"));
		$meeting->setScratchdate(date_create(mysql_result($result,0,"meet_scratchdate")));
		return $meeting;
		
	}
	
	public function loadMeetingList($object=NULL, $old=false) {
	
		if (is_null($object)) {
			$query="select * from t_meeting";
		} else {
	
		switch (get_class($object))	{
			case "holacracy\\Member" :
			case "holacracy\\User" :
				$query="select distinct t_meeting.* from t_meeting join t_role as circle on (t_meeting.role_id_circle=circle.role_id) join t_role on (t_role.role_id_superCircle=circle.role_id and t_role.user_id=".$object->getId().") where t_meeting.meet_date>NOW() UNION select distinct t_meeting.* from t_meeting join t_role as circle on (t_meeting.role_id_circle=circle.role_id) left join t_role on (circle.role_id=t_role.role_id_superCircle) join t_rolefiller on (t_rolefiller.role_id=t_role.role_id and t_rolefiller.user_id=".$object->getId().") where 1=1 ";
				break;
			case "holacracy\\Circle" :
				$query="select * from t_meeting where t_meeting.role_id_circle=".$object->getId()." ";			
				break;
			case "holacracy\\Organisation" :
				$query="select * from t_meeting where t_meeting.orga_id=".$object->getId()." ";			
				break;
			default: 
				trigger_error ("Unknown CLASS for loadMeetingList", E_USER_WARNING );
				return ;	
		}
		}	
	
		// Charge tous les meeting 
		if ($old) {
			//passés
			$query.=" and (t_meeting.meet_date<=CURDATE() and (not(t_meeting.meet_opening is NULL) and not(t_meeting.meet_closing is NULL)))";
			$query.=" order by meet_date DESC";
		} else {
			//à venir ou les meetings en cours (pas fermés)
			$query.=" and (t_meeting.meet_date>=CURDATE() or (not(t_meeting.meet_opening is NULL) and t_meeting.meet_closing is NULL))";
			$query.=" order by meet_date";
		}
		$result=mysql_query($query, $this->_dbh);
		if ($result<=0) echo $query;
		$returnArray=array();

		for ($i=0; $i<mysql_num_rows($result); $i++) {
			$returnArray[$i]=$this->loadMeeting(mysql_result($result,$i,"meet_id"));
		}		
		return $returnArray;		
	}
	
	public function loadLinkListe($circle) {
		$query="select t_role.role_id, count(souscercle.role_id) as cpt from t_role left join t_role as souscercle on (t_role.role_id=souscercle.role_id_superCircle) where t_role.roty_id=".\holacracy\Role::LINK_ROLE." and (t_role.role_id_superCircle=".$circle->getId()." or t_role.circ_id_source=".$circle->getId()." or t_role.role_id_master=".$circle->getId().") group by t_role.role_id order by t_role.role_name";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();

		for ($i=0; $i<mysql_num_rows($result); $i++) {
			if (mysql_result($result,$i,"cpt")>0) {
				// Ajoute un cercle
				$returnArray[$i]=$this->loadCircle(mysql_result($result,$i,"role_id"));
				
			} else {
				// Ajoute un rôle
				$returnArray[$i]=$this->loadRole(mysql_result($result,$i,"role_id"));
			}
		}	
		return $returnArray;
	}
	
	public function loadRoleListe($circle) {
		// Si c'est un user
		if (get_class($circle)=="holacracy\User") {
			$query="select t_role.role_id,  count(souscercle.role_id) as cpt from t_role left join t_role as souscercle on (t_role.role_id=souscercle.role_id_superCircle and souscercle.role_active=1) left join t_role as supercercle on ((t_role.roty_id=2 or t_role.user_id is NULL) and t_role.role_id_superCircle=supercercle.role_id) left join t_rolefiller on (t_role.role_id=t_rolefiller.role_id) where (t_role.roty_id<>2 and t_role.user_id=".$circle->getId().") or supercercle.user_id=".$circle->getId()." or t_rolefiller.user_id=".$circle->getId()." group by t_role.role_id order by t_role.role_name";
			//$query="select t_role.role_id, count(souscercle.role_id) as cpt from t_role left join t_role as souscercle on (t_role.role_id=souscercle.role_id_superCircle) where t_role.user_id=".$circle->getId()." group by t_role.role_id order by t_role.role_name";
			$result=mysql_query($query, $this->_dbh);
			$returnArray=array();

			// Supprimé pour différencier le 1er lien du rôle supérieur - DDr - 29.8.2014
			//$returnArray[0]=$circle->getLeapLink();

			$hasLeadLink=false;
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				if (mysql_result($result,$i,"cpt")>0) {
					// Ajoute un cercle
					$returnArray[$i]=$this->loadCircle(mysql_result($result,$i,"role_id"));
					
				} else {
					// Ajoute un rôle
					$returnArray[$i]=$this->loadRole(mysql_result($result,$i,"role_id"));
				}
				if ($returnArray[$i]->getType()==\holacracy\Role::LEAD_LINK_ROLE) {
					$hasLeadLink=true;
				}
			}	
				
			return $returnArray;			
		} else
		// Si c'est un cercle
		if (get_class($circle)=="holacracy\Circle") {
			// Si c'est un cercle
			$query="select t_role.role_id, count(souscercle.role_id) as cpt from t_role left join t_role as souscercle on (t_role.role_id=souscercle.role_id_superCircle and souscercle.role_active=1) where t_role.role_id_superCircle=".$circle->getId()." group by t_role.role_id order by t_role.role_name";
			$result=mysql_query($query, $this->_dbh);
			$returnArray=array();

			// Supprimé pour différencier le 1er lien du rôle supérieur - DDr - 29.8.2014
			//$returnArray[0]=$circle->getLeapLink();

			$hasLeadLink=false;
			for ($i=0; $i<mysql_num_rows($result); $i++) {
				if (mysql_result($result,$i,"cpt")>0) {
					// Ajoute un cercle
					$returnArray[$i]=$this->loadCircle(mysql_result($result,$i,"role_id"));
					
				} else {
					// Ajoute un rôle
					$returnArray[$i]=$this->loadRole(mysql_result($result,$i,"role_id"));
				}
				if ($returnArray[$i]->getType()==\holacracy\Role::LEAD_LINK_ROLE) {
					$hasLeadLink=true;
				}
			}	
				
			return $returnArray;
		}
	}
	
	public function loadRole($id, $forcedRole=0) {
		
		// Si l'élément n'a pas encore été chargé
		if (empty($this->_roles[$id])) {

			$query="select * from t_role left join to_roletype on (t_role.roty_id=to_roletype.roty_id) left join t_role as fils on (t_role.role_id=fils.role_id_superCircle and fils.role_active=1) where t_role.role_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a ROLE table.', E_USER_WARNING );
				return ;
			} 	
			if (mysql_num_rows($result)==0) {
				trigger_error ("ID not found in ROLE table : ".$id.".", E_USER_WARNING );
				return ;
			} 	
			// S'agit-il d'un cercle en fait
			if ((mysql_num_rows($result)>1 || mysql_result($result,0,"fils.role_id")!="") && $forcedRole==0) {
				return $this->loadCircle($id);
			}

			$role=new \holacracy\Role($this,$id);	
			
			$role->setName(mysql_result($result,0,"role_name"));
			if (mysql_result($result,0,"role_purpose")!="") {
				$role->setPurpose(mysql_result($result,0,"role_purpose"));
			} else {
				$role->setPurpose(mysql_result($result,0,"roty_defaultPurpose"));
			}
			$role->setSuperCircleID(mysql_result($result,0,"role_id_superCircle"));
			$role->setType(mysql_result($result,0,"t_role.roty_id"));
			$role->setActive(mysql_result($result,0,"role_active"));
			if (mysql_result($result,0,"role_id_source")!="") $role->setSourceId(mysql_result($result,0,"role_id_source"));
			if (mysql_result($result,0,"circ_id_source")!="") $role->setSourceCircleId(mysql_result($result,0,"circ_id_source"));
			if (mysql_result($result,0,"role_id_master")!="") $role->setMasterId(mysql_result($result,0,"role_id_master"));
			if (mysql_result($result,0,"user_id")!="") $role->setUserId(mysql_result($result,0,"user_id"));
			$this->_roles[$id]=$role;
		} else {
			// Sinon, renvoie simplement l'élément précédamment chargé
			$role=$this->_roles[$id];
		}
		return $role;
	}
	

	
	public function loadCircle($id) {
		
		// Si l'élément n'a pas encore été chargé
		if (empty($this->_roles[$id])) {
		
		$query="select * from t_role where role_id=".$id;
		$result=mysql_query($query, $this->_dbh);
		if ($result<=0) {
			trigger_error ('The DATABASE don\'t have a ROLE table.', E_USER_WARNING );
			return ;
		} 		
		if (mysql_num_rows($result)==0) {
			trigger_error ('ID not found in ROLE table.', E_USER_WARNING );
			return ;
		} 
	
			$circle=new \holacracy\Circle($this,$id);	
		
		$circle->setName(mysql_result($result,0,"role_name"));
		$circle->setPurpose(mysql_result($result,0,"role_purpose"));
		$circle->setSuperCircleID(mysql_result($result,0,"role_id_superCircle"));
			$circle->setType(\holacracy\Role::CIRCLE); // Passe par dessus le type de rôle
		$circle->setStrategy(mysql_result($result,0,"role_strategy"));
		$circle->setActive(mysql_result($result,0,"role_active"));
		$circle->setOrganisation(mysql_result($result,0,"orga_id"));
		$circle->setUserId(mysql_result($result,0,"user_id"));

		} else {
			// Sinon, renvoie simplement l'élément précédamment chargé
			$circle=$this->_roles[$id];
		}
		return $circle;
	}
	
	public function loadStrategy($id) {
	$query="select role_strategy from t_role where role_id=".$id;
	$result=mysql_query($query, $this->_dbh);
	if ($result<=0) {
		trigger_error ('The DATABASE don\'t have a ROLE table.', E_USER_WARNING );
		return ;
		} 		
	if (mysql_num_rows($result)==0) {
		trigger_error ('ID not found in ROLE table.', E_USER_WARNING );
		return ;
		} 	
	$strategy = mysql_result($result,0,"role_strategy");
	return $strategy;
	}
	
	public function loadMetricReferences($object=NULL) {
		if (is_null($object)) {
			trigger_error ("You must specify an Object", E_USER_WARNING );
			return ;
		} else if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Metric":
					$query="select * from tl_metric where metr_id_src=".$object->getId();
				break;
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadMetricReferences", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong PARAMETER for loadMetricReferences", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadMetric(mysql_result($result,$i,"metr_id_ref"));

		}
		return $returnArray;		
	}
	
	public function loadMetricValues($object=NULL) {
		if (is_null($object)) {
			trigger_error ("You must specify an Object", E_USER_WARNING );
			return ;
		} else if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Metric":
					$query="select * from t_metric_value where metr_id=".$object->getId()." order by meva_date";
				break;
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadMetricValues", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong PARAMETER for loadMetricValues", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=new \holacracy\MetricValue($this);
			$returnArray[$i]->setValue(mysql_result($result,$i,"meva_value"));
			$returnArray[$i]->setDate(date_create(mysql_result($result,$i,"meva_date")));
		}
		return $returnArray;		
	}
	
	public function loadChecklistDates($object=NULL) {
		if (is_null($object)) {
			trigger_error ("You must specify an Object", E_USER_WARNING );
			return ;
		} else if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Checklist":
					$query="select * from t_checklist_date where chli_id=".$object->getId()." order by clda_date";
				break;
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadChecklistDates", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong PARAMETER for loadChecklistDates", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=new \holacracy\ChecklistDate($this);
			$returnArray[$i]->setDate(date_create(mysql_result($result,$i,"clda_date")));
		}
		return $returnArray;		
	}
	
	// Retourne une liste d'éléments metrics enfants attachés à un cercle
	public function loadRecurrenceList($object=NULL) {
		if (is_null($object)) {
			$query="select recu_id from t_recurrence order by recu_order";
		} else if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadRecurrenceList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadRecurrenceList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadRecurrence(mysql_result($result,$i,"recu_id"));
		}
		return $returnArray;		
	}
	
	public function loadRecurrence($id) {
		if (!isset($this->_recurrence[$id]) || is_null($this->_recurrence[$id])) {
			$query="select * from t_recurrence where recu_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a RECURRENCE table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in RECURRENCE table.', E_USER_WARNING );
				return ;
			} 	
			$recurrence=$this->_recurrence[$id]=new \holacracy\Recurrence($this,$id);	
			$recurrence->setLabel(mysql_result($result,0,"recu_label"));
			$recurrence->setTimeLaps(mysql_result($result,0,"recu_timelaps"));
			$recurrence->setModified(false);
			return $recurrence; 
		} else {
			return $this->_recurrence[$id];
		}
	}
	

	// Retourne une liste d'éléments metrics enfants attachés à un cercle
	public function loadCheckLists($object, $context=NULL) {
		if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\User" :
					$query ="select chli_id from t_checklist join t_role on (t_checklist.role_id=t_role.role_id) join t_role as t_circle on (t_role.role_id_supercircle=t_circle.role_id) where (t_role.user_id=".$object->getId()." or t_checklist.user_id=".$object->getId().") ";
					switch (get_class($context))	{
						case "holacracy\\Organisation" :
							$query.="  and t_circle.orga_id=".$context->getId()."";
						break;
					}
					$query.=" order by t_role.roty_id, t_role.role_name";
				break;
				case "holacracy\\Role" :
				case "holacracy\\Circle" :
					$query="select chli_id from t_checklist left join t_role on (t_checklist.role_id=t_role.role_id) where t_role.role_id=".$object->getId()." order by t_role.roty_id, t_role.role_name";
				break;
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadCheckList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadMetricsList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		if (!$result>0) {
			echo '<div title="'.$query.'">Erreur SQL</div>';
		}
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			$returnArray[$i]=$this->loadChecklist(mysql_result($result,$i,"chli_id"));
		}
		return $returnArray;		
	}

	// Retourne une liste d'éléments metrics enfants attachés à un cercle
	public function loadChecklistList($object) {
		if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Circle" :
					$query="select chli_id from t_checklist left join t_role on (t_checklist.role_id=t_role.role_id) where role_id_circle=".$object->getId()." order by t_role.roty_id, t_role.role_name";
				break;
				case "holacracy\\Role" :
					$query="select chli_id from t_checklist left join t_role on (t_checklist.role_id=t_role.role_id) where t_checklist.role_id=".$object->getId()." order by t_role.roty_id, t_role.role_name";
				break;
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadChecklistList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadMetricsList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadChecklist(mysql_result($result,$i,"chli_id"));
		}
		return $returnArray;		
	}

	public function loadChecklist($id) {
		if (!isset($this->_checklist[$id]) || is_null($this->_checklist[$id])) {
			$query="select * from t_checklist where chli_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a CHECKLIST table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in CHECKLIST table.', E_USER_WARNING );
				return ;
			} 	
			$checklist=$this->_checklist[$id]=new \holacracy\Checklist($this,$id);	
			$checklist->setTitle(mysql_result($result,0,"chli_title"));
			$checklist->setDescription(mysql_result($result,0,"chli_description"));
			$checklist->setUserId(mysql_result($result,0,"user_id"));
			$checklist->setRoleId(mysql_result($result,0,"role_id"));
			$checklist->setCircleId(mysql_result($result,0,"role_id_circle"));
			$checklist->setRecurrenceId(mysql_result($result,0,"recu_id"));
			$checklist->setModified(false);
			return $checklist; 
		} else {
			return $this->_checklist[$id];
		}
	}
	
	
	// Retourne une liste d'éléments metrics enfants attachés à un cercle kda 19.6.2014
	public function loadTensoinMoiList($object,$user,$filtre) {
	if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
		switch (get_class($object))	{
				case "holacracy\\Circle" : //On retourne un array pour l'affichage de la liste de tension d'un user/circle
					$query="select * from t_tension_moi where user_id =".$user->getId()." and  circle_id = ".$object->getId()." and tmoi_type = '".$filtre."'";
				break;
				case "holacracy\\Organisation" :  //On retourne un array pour le Moi d'un user/org
					$query="select * from t_tension_moi where user_id =".$user->getId()." and  orga_id = ".$object->getId()."";
				break;
				}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément de tension
			$returnArray[$i]=$this->loadTensionMoi(mysql_result($result,$i,"tmoi_id"));
		}
		return $returnArray;
		}
		
	}
	
	public function loadTensionMoi($id) {
		if (!isset($this->_tensionmoi[$id]) || is_null($this->_tensionmoi[$id])) {
			
			$query="SELECT t_tension_moi.*,t_role.role_name FROM t_tension_moi left JOIN t_role ON t_tension_moi.role_id = t_role.role_id where tmoi_id =".$id;
			//$query="select * from t_tension_moi where tmoi_id =".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a TENSION_MOI table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID ('.$id.') not found in TENSION_MOI table.', E_USER_WARNING );
				return ;
			} 	
			$tensionmoi=$this->_tensionmoi[$id]=new \holacracy\TensionMoi($this,$id);	
			$tensionmoi->setDescription(mysql_result($result,0,"tmoi_description"));
			if (mysql_result($result,0,"role_id")>0) $tensionmoi->setRoleId(mysql_result($result,0,"role_id"));
			$tensionmoi->setUserId(mysql_result($result,0,"user_id"));
			$tensionmoi->setType(mysql_result($result,0,"tmoi_type"));
			$tensionmoi->setName(mysql_result($result,0,"tmoi_name"));
			$tensionmoi->setCircleId(mysql_result($result,0,"circle_id"));
			$tensionmoi->setOrgId(mysql_result($result,0,"orga_id"));
			$tensionmoi->setRoleName(mysql_result($result,0,"role_name"));
			$tensionmoi->setModified(false);
			return $tensionmoi; 
		} else {
			return $this->_tensionmoi[$id];
		}
	}

	// Retourne une liste d'éléments metrics enfants attachés à un cercle
	public function loadMetricList($object) {
		if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Role" :
					$query="select metr_id from t_metric left join t_role on (t_metric.role_id=t_role.role_id) where t_metric.role_id=".$object->getId()."  order by t_role.roty_id, t_role.role_name";
				break;

				case "holacracy\\Circle" :
					$query="select metr_id from t_metric left join t_role on (t_metric.role_id=t_role.role_id) where role_id_circle=".$object->getId()."  order by t_role.roty_id, t_role.role_name";
				break;
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadMetricsList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadMetricsList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadMetric(mysql_result($result,$i,"metr_id"));
		}
		return $returnArray;		
	}
	
		// Retourne une liste d'éléments metrics enfants attachés à un cercle
	public function loadMetrics($object, $context=NULL) {
		if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\User" :
					$query="select metr_id from t_metric join t_role on (t_metric.role_id=t_role.role_id) join t_role as t_circle on (t_role.role_id_supercircle= t_circle.role_id) where (t_metric.user_id=".$object->getId()." or t_role.user_id=".$object->getId().")";
					switch (get_class($context))	{
						case "holacracy\\Organisation" :
							$query.=" and t_circle.orga_id='".$context->getId()."'";
						break;
					}
					$query.=" order by t_role.roty_id, t_role.role_name";
				
				break;

			case "holacracy\\Role" :
					$query="select metr_id from t_metric where t_metric.role_id=".$object->getId()." ";
				break;

				case "holacracy\\Circle" :
					$query="select metr_id from t_metric left join t_role on (t_metric.role_id=t_role.role_id) where t_role.role_id=".$object->getId()."  order by t_role.roty_id, t_role.role_name";
				break;
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadMetricsList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadMetrics", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		if (!$result>0) {
			echo '<div title="'.$query.'">Erreur SQL</div>';
		}
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadMetric(mysql_result($result,$i,"metr_id"));
		}
		return $returnArray;		
	}


	public function loadMetric($id) {
		if (!isset($this->_metric[$id]) || is_null($this->_metric[$id])) {
			$query="select * from t_metric where metr_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a METRIC table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in METRIC table.', E_USER_WARNING );
				return ;
			} 	
			$metric=$this->_metric[$id]=new \holacracy\Metric($this,$id);	
			$metric->setDescription(mysql_result($result,0,"metr_description"));
			$metric->setName(mysql_result($result,0,"metr_name"));
			$metric->setShortName(mysql_result($result,0,"metr_shortname"));
			$metric->setRoleId(mysql_result($result,0,"role_id"));
			$metric->setUserId(mysql_result($result,0,"user_id"));
			$metric->setNumeric(mysql_result($result,0,"metr_isnumeric"));
			$metric->setFile(mysql_result($result,0,"metr_file"));
			$metric->setCircleId(mysql_result($result,0,"role_id_circle"));
			$metric->setRecurrenceId(mysql_result($result,0,"recu_id"));
			$metric->setGoal(mysql_result($result,0,"metr_goal"));
			$metric->setModified(false);
			return $metric; 
		} else {
			return $this->_metric[$id];
		}
	}

	public function loadPolicy($id) {
		if (!isset($this->_policy[$id]) || is_null($this->_policy[$id])) {
			$query="select * from t_policy where poli_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a POLICY table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in POLICY table.', E_USER_WARNING );
				return ;
			} 	
			$policy=$this->_policy[$id]=new \holacracy\Policy($this,$id);	
			$policy->setTitle(mysql_result($result,0,"poli_title"));
			$policy->setDescription(mysql_result($result,0,"poli_description"));
			$policy->setUserId(mysql_result($result,0,"user_id"));
			$policy->setCircleId(mysql_result($result,0,"role_id"));
			$policy->setLink(mysql_result($result,0,"poli_link"));
			$policy->setModified(false);
			return $policy; 
		} else {
			return $this->_policy[$id];
		}
	}

		

	// Retourne une liste d'éléments historiques enfants attachés à un élément d'historique
	public function loadPolicyList($object) {
		if (is_numeric($object)) {
			trigger_error ("Not implemented for ID", E_USER_WARNING );
			return ;	
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Circle" :
					$query="select poli_id from t_policy where role_id=".$object->getId()." order by poli_date DESC";
				break;
				default: 
					trigger_error ("Unknown CLASS [".get_class($object)."]for loadPolicyList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadPolicyList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadPolicy(mysql_result($result,$i,"poli_id"));
		}
		return $returnArray;		
	}
		
	public function loadChat($id) {
		if (!isset($this->_chat[$id]) || is_null($this->_chat[$id])) {
			$query="select * from t_chat where chat_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a CHAT table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in CHAT table.', E_USER_WARNING );
				return ;
			} 	
			$chat=$this->_chat[$id]=new \holacracy\Chat($this,$id);	
			$chat->setText(mysql_result($result,0,"chat_text"));
			$chat->setMeetingId(mysql_result($result,0,"meet_id"));
			$chat->setUserId(mysql_result($result,0,"user_id"));
			$chat->setDate(date_create(mysql_result($result,0,"chat_date")));
			$chat->setModified(false);
			return $chat; 
		} else {
			return $this->_chat[$id];
		}	}
		
		
	// Charge les entrées de chat d'un objet
	public function loadChatList($object) {
		if (is_numeric($object)) {
			$query="select chat_id from t_chat where chat_id=".$object;
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Circle" :
					$query="select chat_id from t_chat where circ_id=".$object->getId()." order by chat_date DESC";
				break;
				case "holacracy\\Meeting" :
					$query="select chat_id from t_chat where meet_id=".$object->getId()." order by chat_date DESC";
				break;
				default: 
					trigger_error ("Unknown CLASS for loadChatList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadChatList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadChat(mysql_result($result,$i,"chat_id"));
		}
		return $returnArray;		
	}
	
		public function loadTension($id) {
		if (!isset($this->_tension[$id]) || is_null($this->_tension[$id])) {
			$query="select * from t_tension where tens_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a TENSION table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in TENSION table.', E_USER_WARNING );
				return ;
			} 	
			$tension=$this->_tension[$id]=new \holacracy\Tension($this,$id);	
			$tension->setTitle(mysql_result($result,0,"tens_title"));
			$tension->setDescription(mysql_result($result,0,"tens_description"));
			//$tension->setMeetingId(mysql_result($result,0,"meet_id"));
			$tension->setCircleId(mysql_result($result,0,"circ_id"));
			$tension->setRoleId(mysql_result($result,0,"role_id"));
			$tension->setUserId(mysql_result($result,0,"user_id"));
			$tension->setTypeId(mysql_result($result,0,"tyte_id"));
			$tension->setDate(date_create(mysql_result($result,0,"tens_datecreation")));
			$tension->check((mysql_result($result,0,"tens_dateend")!=""));
			$tension->setModified(false);
			return $tension; 
		} else {
			return $this->_tension[$id];
		}	}
		
		
	public function linkTensionMeeting($tension, $meeting) {
		if (!is_numeric($tension)) $tension=$tension->getId();
		if (!is_numeric($meeting)) $meeting=$meeting->getId();
		$query="insert into tl_tension_meeting (tens_id, meet_id) VALUES (".$tension.",".$meeting.")";
		$result=mysql_query($query, $this->_dbh);
	}
	
	// Charge les entrées de tension d'un objet
	public function loadTensionList($object) {
		if (is_numeric($object)) {
			$query="select tens_id from t_tension where tens_id=".$object;
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Meeting" :
					$query="select tens_id from tl_tension_meeting where meet_id=".$object->getId()." order by mete_datecreation";
				break;
				default: 
					trigger_error ("Unknown CLASS for loadTensionList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadTensionList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		if (!$result>0)
		{
			trigger_error ("Bad SQL request:".$query, E_USER_WARNING );
			return ;				
		}
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadTension(mysql_result($result,$i,"tens_id"));
		}
		return $returnArray;		
	}
	
	public function loadHistory($id) {
		if (!isset($this->_history[$id]) || is_null($this->_history[$id])) {
			$query="select * from t_history where hist_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a HISTORY table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in HISTORY table.', E_USER_WARNING );
				return ;
			} 	
			$history=$this->_history[$id]=new \holacracy\History($this,$id);	
			$history->setTitle(mysql_result($result,0,"hist_title"));
			$history->setDescription(mysql_result($result,0,"hist_description"));
			$history->setUserId(mysql_result($result,0,"user_id"));
			$history->setCircleId(mysql_result($result,0,"role_id_circle"));
			$history->setRoleId(mysql_result($result,0,"role_id"));
			$history->setMeetingId(mysql_result($result,0,"meet_id"));
			$history->setTensionId(mysql_result($result,0,"tens_id"));
			$history->setLink(mysql_result($result,0,"hist_link"));
			$history->setParentId(mysql_result($result,0,"hist_id_parent"));
			$history->setDate(date_create(mysql_result($result,0,"hist_date")));
			$history->setModified(false);
			return $history; 
		} else {
			return $this->_history[$id];
		}
	}
	
	public function loadContactTypeList() {
		$query="select tyco_id from t_typecontact order by tyco_order";
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadContactType(mysql_result($result,$i,"tyco_id"));
		}
		return $returnArray;		
	}
	
	public function loadContactType($id) {
		if (!isset($this->_contact_type[$id]) || is_null($this->_contact_type[$id])) {
			$query="select * from t_typecontact where tyco_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a TYPECONTACT table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in TYPECONTACT table.', E_USER_WARNING );
				return ;
			} 	
			$typecontact=$this->_contact_type[$id]=new \holacracy\TypeContact($this,$id);	
			$typecontact->setId(mysql_result($result,0,"tyco_id"));
			$typecontact->setLabel(mysql_result($result,0,"tyco_label"));
			$typecontact->setFormat(mysql_result($result,0,"tyco_format"));
			return $typecontact; 
		} else {
			return $this->_contact_type[$id];
		}	
	}
	
	public function loadContact($id) {
		if (!isset($this->_contact[$id]) || is_null($this->_contact[$id])) {
			$query="select * from t_contact left join t_typecontact on (t_contact.tyco_id=t_typecontact.tyco_id) where cont_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a CONTACT table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in CONTACT table.', E_USER_WARNING );
				return ;
			} 	
			$contact=$this->_contact[$id]=new \holacracy\Contact($this,$id);	
			$contact->setId(mysql_result($result,0,"cont_id"));
			$contact->setLabel(mysql_result($result,0,"cont_label"));
			$contact->setType(mysql_result($result,0,"t_contact.tyco_id"));
			$contact->setValue(mysql_result($result,0,"cont_value"));
			$contact->setUser(mysql_result($result,0,"user_id"));
			return $contact; 
		} else {
			return $this->_contact[$id];
		}	
	}
	
	// Charge la liste des bugs
	public function loadBugList($filter=NULL, $order='priority') {
		if ($order=='datestatus') {
			$query="select t_bug.*, max(t_statusbug_user.sbus_date) as max from t_bug left join t_statusbug_user on (t_bug.bug_id=t_statusbug_user.bug_id) group by bug_id order by max DESC, bug_priority DESC, bug_datecreation DESC";;				
		} else {
			$query="select t_bug.* from t_bug order by bug_priority DESC, bug_datecreation DESC";;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique

			$bug=$this->loadBug(mysql_result($result,$i,"bug_id"));
			if (is_null($filter) || (is_null($bug->getStatus()) && $filter==0)  || (!is_null($bug->getStatus()) && $bug->getStatus()->getId()==$filter) ) {
				$returnArray[]=$this->loadBug(mysql_result($result,$i,"bug_id"));
			}
		}
		return $returnArray;		
	}
	
	public function loadBug($id) {
		if (!isset($this->_bug[$id]) || is_null($this->_bug[$id])) {
			$query="select * from t_bug where bug_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a BUG table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in BUG table.', E_USER_WARNING );
				return ;
			} 	
			$bug=$this->_bug[$id]=new \holacracy\Bug($this,$id);	
			$bug->setId(mysql_result($result,0,"bug_id"));
			$bug->setTitle(mysql_result($result,0,"bug_title"));
			$bug->setDescription(mysql_result($result,0,"bug_description"));
			$bug->setPriority(mysql_result($result,0,"bug_priority"));
			$bug->setCreationDate(mysql_result($result,0,"bug_datecreation"));
			$bug->setAuthorId(mysql_result($result,0,"user_id_creation"));
			$bug->setBugStatusId(mysql_result($result,0,"stbu_id"));
			$bug->setBugTypeId(mysql_result($result,0,"tybu_id"));
			return $bug; 
		} else {
			return $this->_bug[$id];
		}			
	}	

	public function loadComment($id) {
		if (!isset($this->_comment[$id]) || is_null($this->_comment[$id])) {
			$query="select * from t_comment where comm_id=".$id;
			$result=mysql_query($query, $this->_dbh);
			if ($result<=0) {
				trigger_error ('The DATABASE don\'t have a COMMENT table.', E_USER_WARNING );
				return ;
			} 		
			if (mysql_num_rows($result)==0) {
				trigger_error ('ID not found in COMMENT table.', E_USER_WARNING );
				return ;
			} 	
			$comment=$this->_comment[$id]=new \holacracy\Comment($this,$id);	
			$comment->setId(mysql_result($result,0,"comm_id"));
			$comment->setDescription(mysql_result($result,0,"comm_description"));
			$comment->setProjectId(mysql_result($result,0,"proj_id"));
			$comment->setAuthorId(mysql_result($result,0,"user_id_creation"));
			$comment->setModifierId(mysql_result($result,0,"user_id_modification"));
			$comment->setCreationDate(mysql_result($result,0,"comm_date_creation"));
			$comment->setModificationDate(mysql_result($result,0,"comm_date_modification"));
			//$comment->setTT(mysql_result($result,0,"comm_tt"));
			//$comment->setTTUnite(mysql_result($result,0,"comm_tt_unite"));
			//$comment->setTR(mysql_result($result,0,"comm_tr"));
			//$comment->setTRUnite(mysql_result($result,0,"comm_tr_unite"));
			return $comment; 
		} else {
			return $this->_comment[$id];
		}			
	}
	
	public function loadCommentList($object) {
		if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\Project" :
					$query="select comm_id from t_comment where proj_id=".$object->getId()." order by comm_date_creation DESC";
				break;
				default: 
					trigger_error ("Unknown CLASS for loadCommentList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadCommentList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadComment(mysql_result($result,$i,"comm_id"));
		}
		return $returnArray;
	}
	
	// Retourne la liste des objets de type contact
	public function loadContactList($object) {
		if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\RoleFiller" :
				case "holacracy\\Member" :
				case "holacracy\\User" :
					$query="select cont_id from t_contact left join t_typecontact on (t_contact.tyco_id=t_typecontact.tyco_id) where user_id=".$object->getId()." order by t_typecontact.tyco_order, t_contact.cont_order";
				break;
				default: 
					trigger_error ("Unknown CLASS for loadContactList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadContactList", E_USER_WARNING );
			return ;				
		}
		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadContact(mysql_result($result,$i,"cont_id"));
		}
		return $returnArray;		
	}
	
	// Retourne une liste d'éléments historiques enfants attachés à un élément d'historique
	public function loadHistoryList($object) {
		if (is_numeric($object)) {
			$query="select hist_id from t_history where hist_id_parent=".$object;
		} else if (is_object($object)) {
			switch (get_class($object))	{
				case "holacracy\\History" :
					return $this->loadHistoryList($object->getId());
				break;
				case "holacracy\\Circle" :
					$query="select hist_id from t_history where role_id_circle=".$object->getId()." order by hist_date DESC";
				break;
				case "holacracy\\Role" :
					$query="select hist_id from t_history where role_id=".$object->getId()." order by hist_date DESC";
				break;
				case "holacracy\\Meeting" :
					$query="select hist_id from t_history where meet_id=".$object->getId()." order by hist_date DESC";
				break;
				default: 
					trigger_error ("Unknown CLASS for loadHistoryList", E_USER_WARNING );
					return ;	
			}
						
		} else {
			trigger_error ("Wrong OBJECT for loadHistoryList", E_USER_WARNING );
			return ;				
		}

		$result=mysql_query($query, $this->_dbh);
		$returnArray=array();
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			// Ajoute un élément d'historique
			$returnArray[$i]=$this->loadHistory(mysql_result($result,$i,"hist_id"));
		}
		return $returnArray;		
	}
	
	// Fonction pour supprimer les objets
	public function delete($object) {
		switch (get_class($object))	{			
			case "holacracy\\Document" :
				if ($object->getId()>0) {
					$query="delete from t_document where docu_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;			
			case "holacracy\\Action" :
				if ($object->getId()>0) {
					$query="update t_action set date_delete=NOW() where acti_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;
			case "holacracy\\Meeting" :
				if ($object->getId()>0) {
					$query="delete from t_meeting where meet_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;			
			case "holacracy\\Metric" :
				if ($object->getId()>0) {
					$query="delete from t_metric where metr_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;			
			case "holacracy\\Checklist" :
				if ($object->getId()>0) {
					$query="delete from t_checklist where chli_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;			
			case "holacracy\\Scope" :
				if ($object->getId()>0) {
					$query="delete from t_scope where scop_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;			
			case "holacracy\\Contact" :
				if ($object->getId()>0) {
					$query="delete from t_contact where cont_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;			
			case "holacracy\\RoleFiller" :
				if ($object->getId()>0) {
					$query="delete from t_rolefiller where rofi_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete(); // Supprime récursivement les objets liés, comme les affectations de projet
				}	
				break;			
			case "holacracy\\Scope" :
				if ($object->getId()>0) {
					$query="delete from t_scope where scop_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;
			case "holacracy\\TensionMoi" :
				if ($object->getId()>0) {
					$query="delete from t_tension_moi where tmoi_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}	
				break;
			case "holacracy\\Accountability" :
				if ($object->getId()>0) {
					$query="delete from t_accountability where acco_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					$object->delete();
				}		
				break;		
			case "holacracy\\Policy" :
				if ($object->getId()>0) {
					$query="delete from t_policy where poli_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					// Y a-t-il une liste d'objets à effacer?
					$liste=$object->delete();
					if (isset($liste) && is_array($liste) && count($liste)>0) {
						foreach ($liste as $obj) {
							$this->delete($obj);	
						}
					}
										
				}	
				break;			
			case "holacracy\\History" :
				if ($object->getId()>0) {
					$query="delete from t_history where hist_id=".$object->getId();
					mysql_query($query, $this->_dbh);
					// Y a-t-il une liste d'objets à effacer?
					$liste=$object->delete();
					if (isset($liste) && is_array($liste) && count($liste)>0) {
						foreach ($liste as $obj) {
							$this->delete($obj);	
						}
					}
										
				}	
				break;			
			case "holacracy\\Project" :
				if ($object->getId()>0) {
					$query="delete from t_project where proj_id=".$object->getId();
					mysql_query($query, $this->_dbh);					
				}
				break;
			case "holacracy\\ActionMoi" :
					//On archive l'action
					$query="insert into t_actionmoiarchiv (act_id, act_insert, act_title,act_description,proj_id,role_id,user_id,acst_id,act_timestamp) values ('".$object->getId()."', '1', '".str_replace("'","\'",$object->getTitle())."','".str_replace("'","\'",$object->getDescription())."','".$object->getProjectId()."','".$object->getRoleId()."','".$object->getIdUserFocus()."','".$object->getStatusId()."','".$object->getTimeStamp()."')";
					mysql_query($query, $this->_dbh);
					mysql_insert_id($this->_dbh);
					
					//On supprime ensuite l'action
					$query="delete from t_actionmoi where act_id='".$object->getId()."'";
					mysql_query($query, $this->_dbh);	
					
				break;
			default :
				
				// IDEA : implement a generic delete - DDR
			
				trigger_error ("DELETE not possible on ".get_class($object)." object", E_USER_ERROR );
				return ;				
		}
	}
		
	// Fonction pour sauver les objets 	
	// Paramètres : $object : l'objet à sauver
	//              $include_child : Sauvegarde récursive des enfants?
	public function save($object, $include_child=0) {
			switch (get_class($object))	{
				case "holacracy\\Document" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible: ".$errString.".", E_USER_ERROR );
						return;
					}	
					// Contrôle si un document identique existe déjà
					$query="select * from t_document where docu_name='".str_replace("'","\'",$object->getName())."' and role_id='".$object->getRoleId()."'";
					$result=mysql_query($query, $this->_dbh);
					if ($result>0 && mysql_num_rows($result)>0) {
						$object->setId(mysql_result($result,0,"docu_id"));
					}
					
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_document set ".
							"docu_title='".str_replace("'","\'",$object->getTitle())."', ".
							"docu_description='".str_replace("'","\'",$object->getDescription())."', ".
							"docu_name='".str_replace("'","\'",$object->getName())."', ".
							"docu_file='".str_replace("'","\'",$object->getFile())."', ".
							"docu_url='".str_replace("'","\'",$object->getURL())."', ".
							"docu_url_editable='".str_replace("'","\'",$object->getEditURL())."', ".
							"docu_date_delete=".($object->getDeleteDate()!=""?$object->getDeleteDate()->format("'Y-m-d H:i:s'"):"NULL").", ".
							"docu_date_modification=".($object->getModificationDate()!=""?$object->getModificationDate()->format("'Y-m-d H:i:s'"):"NULL").", ".
							"docu_date_creation=".($object->getCreationDate()!=""?$object->getCreationDate()->format("'Y-m-d H:i:s'"):"NULL").", ".
							"docu_visibility=".($object->getVisibility()!=""?$object->getVisibility():"3").", ".
							"user_id='".$object->getUserId()."' , ". 
							"user_id='".$object->getUserId()."' , ". 
							"role_id='".$object->getRoleId()."'  where docu_id=".$object->getId(); 
						mysql_query($query, $this->_dbh);
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_document ".
							"(docu_title, docu_description, docu_name, docu_file, docu_url, docu_url_editable, docu_date_delete, docu_date_modification, docu_date_creation, docu_visibility, user_id, role_id)".
						" values ".
							"("."'".str_replace("'","\'",$object->getTitle())."',".
							"'".str_replace("'","\'",$object->getDescription())."',".
							"'".str_replace("'","\'",$object->getName())."',".
							"'".str_replace("'","\'",$object->getFile())."',".
							"'".str_replace("'","\'",$object->getURL())."',".
							"'".str_replace("'","\'",$object->getEditURL())."',".
							($object->getDeleteDate()!=""?$object->getDeleteDate()->format("'Y-m-d H:i:s'"):"NULL").",".
							($object->getModificationDate()!=""?$object->getModificationDate()->format("'Y-m-d H:i:s'"):"NULL").",".
							($object->getCreationDate()!=""?$object->getCreationDate()->format("'Y-m-d H:i:s'"):"NULL").",".
							($object->getVisibility()!=""?$object->getVisibility():"3").",".
							"'".$object->getUserId()."',".
							"'".$object->getRoleId()."')";
	
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
					}				
				return;
				case "holacracy\\Check" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible: ".$errString.".", E_USER_ERROR );
						return;
					}	
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_check set ".
							"date_check=".($object->getCheckDate()!=""?$object->getCheckDate()->format("'Y-m-d H:i:s'"):"NULL").", ".
							"acti_id='".$object->getActionId()."', ".
							"user_id='".$object->getUserId()."' , ". 
							"role_id='".$object->getRoleId()."'  where chec_id=".$object->getId(); 
						mysql_query($query, $this->_dbh);
						$object->setManager($this);
						$object->setModified(false);
						$childs=$object->getChilds(true);
						if ($include_child && is_array($childs)) {
							for ($i=0; $i<count($childs); $i++) {
								$this->save($childs[$i],true);
							}
						}
					} else {
						// L'objet doit être créé
						$query="insert into t_check ".
							"(date_check, acti_id, user_id, role_id)".
						" values ".
							"(".($object->getCheckDate()!=""?$object->getCheckDate()->format("'Y-m-d H:i:s'"):"NULL").",".
							"'".$object->getActionId()."',".
							"'".$object->getUserId()."',".
							"'".$object->getRoleId()."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$childs=$object->getChilds(true);
						if ($include_child && is_array($childs)) {
							for ($i=0; $i<count($childs); $i++) {
								$this->save($childs[$i],true);
							}
						}
					}
					return;		
				case "holacracy\\Action" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible: ".$errString.".", E_USER_ERROR );
						return;
					}	
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_action set ".
							"acti_title='".str_replace("'","\'",$object->getTitle())."', ".
							"acti_description='".str_replace("'","\'",$object->getDescription())."', ".
							"date_check=".($object->getCheckDate()!=""?$object->getCheckDate()->format("'Y-m-d H:i:s'"):"NULL").", ".
							"date_delete=".($object->getDeleteDate()!=""?$object->getDeleteDate()->format("'Y-m-d H:i:s'"):"NULL").", ".
							"circ_id=".($object->getCircleId()>0?"'".$object->getCircleId()."'":"NULL").", ".
							"role_id=".($object->getRoleId()>0?"'".$object->getRoleId()."'":"NULL")." , ". 
							"role_id_proposer=".($object->getProposerRoleId()>0?"'".$object->getProposerRoleId()."'":"NULL")." , ". 
							"user_id_proposer=".($object->getProposerId()>0?"'".$object->getProposerId()."'":"NULL")." , ". 
							"proj_id=".($object->getProjectId()>0?"'".$object->getProjectId()."'":"NULL")."  where acti_id=".$object->getId(); 
							
				
						mysql_query($query, $this->_dbh);
						$object->setManager($this);
						$object->setModified(false);
						$childs=$object->getChilds(true);
						
						if ($include_child && is_array($childs)) {
							for ($i=0; $i<count($childs); $i++) {
								$this->save($childs[$i],true);
							}
						}
					} else {
						// L'objet doit être créé
						$query="insert into t_action ".
							"(acti_title, acti_description, date_check, date_delete, circ_id, role_id, role_id_proposer, user_id_proposer, proj_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getTitle())."',".
							"'".str_replace("'","\'",$object->getDescription())."',".
							"".($object->getCheckDate()!=""?$object->getCheckDate()->format("'Y-m-d H:i:s'"):"NULL").",".
							"".($object->getDeleteDate()!=""?$object->getDeleteDate()->format("'Y-m-d H:i:s'"):"NULL").",".
							($object->getCircleId()>0?"'".$object->getCircleId()."'":"NULL").",".
							($object->getRoleId()>0?"'".$object->getRoleId()."'":"NULL").",".
							($object->getProposerRoleId()>0?"'".$object->getProposerRoleId()."'":"NULL").",".
							($object->getProposerId()>0?"'".$object->getProposerId()."'":"NULL").",".
							($object->getProjectId()>0?"'".$object->getProjectId()."'":"NULL").")";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$childs=$object->getChilds(true);
						if ($include_child && is_array($childs)) {
							for ($i=0; $i<count($childs); $i++) {
								$this->save($childs[$i],true);
							}
						}
					}
					return;			
				case "holacracy\\History" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_history set ".
							"hist_title='".str_replace("'","\'",$object->getTitle())."', ".
							"hist_description='".str_replace("'","\'",$object->getDescription())."', ".
							"hist_link='".str_replace("'","\'",$object->getLink())."', ".
							"hist_id_parent='".($object->getParentId()>0?$object->getParentId():(!is_null($object->getParent())?$object->getParent()->getId():""))."', ".
							"role_id_circle='".$object->getCircleId()."', ".
							"role_id='".$object->getRoleId()."', ".
							"user_id='".$object->getUserId()."' , ". 
							"tens_id=".($object->getTensionId()>0?"'".$object->getTensionId()."'":"NULL")." , ". 
							"meet_id='".$object->getMeetingId()."'  where hist_id=".$object->getId(); 
						mysql_query($query, $this->_dbh);
						$object->setManager($this);
						$object->setModified(false);
						$childs=$object->getChilds(true);
						if ($include_child && is_array($childs)) {
							for ($i=0; $i<count($childs); $i++) {
								$this->save($childs[$i],true);
							}
						}
					} else {
						// L'objet doit être créé
						$query="insert into t_history ".
							"(hist_title,hist_description,hist_link,hist_id_parent, role_id_circle, role_id, user_id, tens_id, meet_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getTitle())."',".
							"'".str_replace("'","\'",$object->getDescription())."',".
							"'".str_replace("'","\'",$object->getLink())."',".
							"'".($object->getParentId()>0?$object->getParentId():(!is_null($object->getParent())?$object->getParent()->getId():""))."',".
							"'".$object->getCircleId()."',".
							"'".$object->getRoleId()."',".
							"'".$object->getUserId()."',".
							($object->getTensionId()>0?"'".$object->getTensionId()."'":"NULL").",".
							"'".$object->getMeetingId()."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$this->_history[$id]=$object;
						$childs=$object->getChilds(true);
						if ($include_child && is_array($childs)) {
							for ($i=0; $i<count($childs); $i++) {
								$this->save($childs[$i],true);
							}
						}
					}
					return;
				case "holacracy\\Notification" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on NOTIFICATION object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_notification set ".
							"noti_title='".str_replace("'","\'",$object->getTitle())."', ".
							"noti_textcontent='".str_replace("'","\'",$object->getTextContent())."', ".
							"noti_htmlcontent='".str_replace("'","\'",$object->getHTMLContent())."', ".
							"noti_delay='".$object->getDelay()."', ".
							"user_id='".$object->getUserId()."'  where noti_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_notification ".
							"(noti_title,noti_textcontent, noti_htmlcontent, noti_delay, user_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getTitle())."',".
							"'".str_replace("'","\'",$object->getTextContent())."',".
							"'".str_replace("'","\'",$object->getHTMLContent())."',".
							"'".$object->getDelay()."',".
							"'".$object->getUserId()."')";
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						//$this->_notification[$id]=$object; -> pas besoin de mettre en cache pour l'instant
					}
					return;				
				case "holacracy\\Policy" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on POLICY object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_policy set ".
							"poli_title='".str_replace("'","\'",$object->getTitle())."', ".
							"poli_description='".str_replace("'","\'",$object->getDescription())."', ".
							"poli_link='".str_replace("'","\'",$object->getLink())."', ".
							"role_id='".$object->getCircleId()."', ".
							"user_id='".$object->getUserId()."'  where poli_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_policy ".
							"(poli_title,poli_description,poli_link, role_id, user_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getTitle())."',".
							"'".str_replace("'","\'",$object->getDescription())."',".
							"'".str_replace("'","\'",$object->getLink())."',".
							"'".$object->getCircleId()."',".
							"'".$object->getUserId()."')";
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$this->_policy[$id]=$object;
					}
					return;
				case "holacracy\\Meeting" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on MEETING object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_meeting set ".
							"meet_scratchpad='".str_replace("'","\'",$object->getScratchPad())."', ".
							"meet_scratchdate=".($object->getScratchDate()!=""?$object->getScratchDate()->format("'Y-m-d H:i:s'"):"NULL").", ".
							"meet_location='".str_replace("'","\'",$object->getLocation())."', ".
							"mety_id='".$object->getMeetingTypeId()."', ".
							"orga_id='".$object->getOrganisationId()."', ".
							"role_id_circle='".$object->getCircleId()."', ".
							"memb_id_secretary='".$object->getSecretaryId()."', ".
							"meet_date=".($object->getDate()!=""?$object->getDate()->format("'Y-m-d'"):"NULL").", ".
							"meet_starttime='".$object->getStartTime()."', ".
							"meet_endtime='".$object->getEndTime()."', ".
							"meet_opening=".($object->getOpeningTime()!=""?$object->getOpeningTime()->format("'Y-m-d H:i:s'"):"NULL").", ".
							"meet_closing=".($object->getClosingTime()!=""?$object->getClosingTime()->format("'Y-m-d H:i:s'"):"NULL")."  where meet_id=".$object->getId(); 
						//echo $query;
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}

						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_meeting ".
							"(meet_scratchpad,meet_location,mety_id, orga_id, role_id_circle, memb_id_secretary, meet_date, meet_starttime, meet_endtime, meet_opening, meet_closing)".
						" values ".
							"( '".str_replace("'","\'",$object->getScratchPad())."',".
							"'".str_replace("'","\'",$object->getLocation())."',".
							"'".$object->getMeetingTypeId()."',".
							"'".$object->getOrganisationId()."',".
							"'".$object->getCircleId()."',".
							"'".$object->getSecretaryId()."',".
							"'".($object->getDate()!=""?$object->getDate()->format("Y-m-d"):"NULL")."',".
							"'".$object->getStartTime()."',".
							"'".$object->getEndTime()."',".
							"".($object->getOpeningTime()!=""?"'".$object->getOpeningTime()->format("Y-m-d H:i:s")."'":"NULL").",".
							"".($object->getClosingTime()!=""?"'".$object->getClosingTime()->format("Y-m-d H:i:s")."'":"NULL").")";
						//echo $query;
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$this->_policy[$id]=$object;
					}
					return;
				case "holacracy\\Contact" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on CONTACT object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_contact set ".
							"cont_value='".str_replace("'","\'",$object->getValue())."', ".
							"cont_label='".str_replace("'","\'",$object->getLabel())."', ".
							"user_id='".$object->getUserId()."', ".
							"tyco_id='".$object->getTypeId()."'  where cont_id=".$object->getId(); 
						//echo $query;
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_contact ".
							"(cont_value, cont_label, user_id, tyco_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getValue())."',".
							"'".str_replace("'","\'",$object->getLabel())."',".
							"'".$object->getUserId()."',".
							"'".$object->getTypeId()."')";
						//echo $query;
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$this->_contact[$id]=$object;
					}
					return;
				case "holacracy\\Comment" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on COMMENT object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_comment set ".
							"comm_description='".str_replace("'","\'",$object->getDescription())."', ".
							"proj_id='".$object->getProjectId()."', ".
							"comm_date_modification=NOW(), ".
							"user_id_modification='".$SESSION["currentUser"]->getId()."'  where conn_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_comment ".
							"(comm_description,  proj_id, user_id_creation)". //comm_tt, comm_tt_unite, comm_tr, comm_tr_unite,
						" values ".
							"( '".str_replace("'","\'",$object->getDescription())."',".
							// "'".$object->getTT()."',".
							// "'".$object->getTTUnite()."',".
							// "'".$object->getTR()."',".
							//"'".$object->getTRUnite()."',".
							"'".$object->getProjectId()."',".
							"'".$_SESSION["currentUser"]->getId()."')";	
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}	
						
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$this->_comment[$id]=$object;
					}
					return;
				case "holacracy\\Help" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on CHECKLIST object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_help set ".
							"help_key='".str_replace("'","\'",$object->getKey())."', ".
							"help_title='".str_replace("'","\'",$object->getTitle())."', ".
							"help_text='".str_replace("'","\'",$object->getText())."' where help_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_help ".
							"(help_key, help_title, help_text)".
						" values ".
							"( '".str_replace("'","\'",$object->getKey())."',".
							" '".str_replace("'","\'",$object->getTitle())."',".
							" '".str_replace("'","\'",$object->getText())."')";	
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}	
						
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
					}
					return;
				case "holacracy\\Chat" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on CHAT object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_chat set ".
							"help_key='".str_replace("'","\'",$object->getKey())."', ".
							"help_title='".str_replace("'","\'",$object->getTitle())."', ".
							"help_text='".str_replace("'","\'",$object->getText())."' where help_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_chat ".
							"(chat_text, user_id, meet_id, circ_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getText())."',".
							" '".$object->getUserId()."',".
							" ".($object->getMeetingId()>0?"'".$object->getMeetingId()."'":"NULL").",".	
							" ".($object->getCircleId()>0?"'".$object->getCircleId()."'":"NULL").")";	
						if (!mysql_query($query, $this->_dbh)) {
							
							return "Erreur : ".$query;
						}	
						
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
					}
					return;	

				case "holacracy\\Tension" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on a TENSION object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_tension set ".
							"tens_title='".str_replace("'","\'",$object->getTitle())."', ".
							"tens_description='".str_replace("'","\'",$object->getDescription())."', ".
							"tens_dateend=".($object->isChecked()?"NOW()":"null").", ".
							"user_id=".($object->getUserId()>0?$object->getUserId():"null").", ".
							"role_id=".($object->getRoleId()>0?$object->getRoleId():"null").", ".
							"circ_id=".($object->getCircleId()>0?$object->getCircleId():"null").", ".
							"tyte_id=".($object->getTypeId()>0?$object->getTypeId():"0").", ".
							"orga_id=".($object->getOrganisationId()>0?$object->getOrganisationId():"null")." where tens_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_tension ".
							"(tens_title, tens_description, user_id, role_id, circ_id, tyte_id, orga_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getTitle())."',".
							" '".str_replace("'","\'",$object->getDescription())."',".
							" ".($object->getUserId()>0?$object->getUserId():"null").",".
							" ".($object->getRoleId()>0?$object->getRoleId():"null").",".
							" ".($object->getCircleId()>0?$object->getCircleId():"null").",".
							" ".($object->getTypeId()>0?$object->getTypeId():"0").",".
							" ".($object->getOrganisationId()>0?$object->getOrganisationId():"null").")";	
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}	
						
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
					}
					return;	
			
					case "holacracy\\Checklist" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on CHECKLIST object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_checklist set ".
							"chli_title='".str_replace("'","\'",$object->getTitle())."', ".
							"chli_description='".str_replace("'","\'",$object->getDescription())."', ".
							"role_id_circle=".($object->getCircleId()!=""?"'".$object->getCircleId()."'":"NULL").", ".
							"role_id=".($object->getRoleId()!=""?"'".$object->getRoleId()."'":"NULL").", ".
							"user_id=".($object->getUserId()!=""?"'".$object->getUserId()."'":"NULL").", ".
							"recu_id='".$object->getRecurrenceId()."' where chli_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_checklist ".
							"(chli_title,chli_description, role_id_circle, role_id, user_id, recu_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getTitle())."',".
							"'".str_replace("'","\'",$object->getDescription())."',".
							($object->getCircleId()!=""?"'".$object->getCircleId()."'":"NULL").", ".
							($object->getRoleId()!=""?"'".$object->getRoleId()."'":"NULL").", ".
							($object->getUserId()!=""?"'".$object->getUserId()."'":"NULL").", ".
							"'".$object->getRecurrenceId()."')";	
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}	
						
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$this->_checklist[$id]=$object;
					}
					return;
					case "security\\Transaction" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on TRANSACTION object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_transaction set ".
							"tran_tocken='".str_replace("'","\'",$object->getTocken())."', ".
							"tran_startDate='".$object->getStartDate()->format("Y-m-d H:i:s")."', ".
							"tran_price='".$object->getPrice()."', ".
							"tran_ack='".str_replace("'","\'",$object->getStatus())."', ".
							"user_id='".$object->getUserId()."', ".
							"orga_id='".$object->getOrganisationId()."', ".
							"tyab_id='".$object->getSubscriptionId()."' where tran_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_transaction ".
							"(tran_tocken, tran_startDate, tran_price, tran_ack, user_id, orga_id, tyab_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getTocken())."',".
							"'".$object->getStartDate()->format("Y-m-d H:i:s")."',".
							"'".$object->getPrice()."',".
							"'".str_replace("'","\'",$object->getStatus())."',".
							"'".$object->getUserId()."',".
							"'".$object->getOrganisationId()."',".
							"'".$object->getSubscriptionId()."')";	
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}	
						
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
					}
					return;
					
					case "security\\Subscription" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on SUBSCRIPTION object: ".$errString.".", E_USER_ERROR );
						return;
					}
					
					// Est-ce un abonnement, ou un modèle d'abonnement?
					if ($object->getId()>0 && !($object->getOrganisationId()>0)) {
						// Pas implémenté, sauve des modèles d'abonnement
					} else {
					
						if ($object->getId()>0) {
							// L'object existe déjà, le sauve
							$query="update t_abonnement set ".
								"orga_id='".$object->getOrganisationId()."', ".
								"tyab_id='".$object->getSubscriptionTypeId()."', ".
								"abon_duree='".$object->getDuration()."', ".
								"abon_prix='".$object->getPrice()."', ".
								"abon_date='".$object->getStartDate()->format("Y-m-d H:i:s")."', ".
								"abon_actif='".$object->isActive()."' where abon_id=".$object->getId(); 
							if (!mysql_query($query, $this->_dbh)) {
								return "Erreur : ".$query;
							}
							$object->setManager($this);
							$object->setModified(false);
						} else {
							// L'objet doit être créé
							$query="insert into t_abonnement ".
								"(orga_id, tyab_id, abon_duree, abon_prix, abon_date, abon_actif)".
							" values ".
								"( '".$object->getOrganisationId()."',".
								"'".$object->getSubscriptionTypeId()."',".
								"'".$object->getDuration()."',".
								"'".$object->getPrice()."',".
								"'".$object->getStartDate()->format("Y-m-d H:i:s")."',".
								"'".$object->isActive()."')";	
							if (!mysql_query($query, $this->_dbh)) {
								return "Erreur : ".$query;
							}	
							
							$id=mysql_insert_id($this->_dbh);
							$object->setId($id);
							$object->setManager($this);
							$object->setModified(false);
						}
					}
					return;
				case "holacracy\\Metric" :
					// Contrôle la validité des données
					if ($errString=$object->checkForSave()) {
						trigger_error ("SAVE not possible on METRIC object: ".$errString.".", E_USER_ERROR );
						return;
					}

					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_metric set ".
							"metr_name='".str_replace("'","\'",$object->getName())."', ".
							"metr_shortname='".str_replace("'","\'",$object->getShortName())."', ".
							"metr_description='".str_replace("'","\'",$object->getDescription())."', ".
							"metr_file='".str_replace("'","\'",$object->getFile())."', ".
							"metr_isnumeric='".$object->getNumeric()."', ".
							"metr_goal='".$object->getGoal()."', ".
							"role_id_circle=".($object->getCircleId()!=""?"'".$object->getCircleId()."'":"NULL").", ".
							"role_id='".$object->getRoleId()."', ".
							"user_id=".($object->getUserId()>0?"'".$object->getUserId()."'":"NULL").", ".
							"recu_id='".$object->getRecurrenceId()."' where metr_id=".$object->getId(); 
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}
						$object->setManager($this);
						$object->setModified(false);
					} else {
						// L'objet doit être créé
						$query="insert into t_metric ".
							"(metr_name, metr_shortname, metr_description, metr_file, metr_isnumeric, metr_goal, role_id_circle, role_id, user_id, recu_id)".
						" values ".
							"( '".str_replace("'","\'",$object->getName())."',".
							"'".str_replace("'","\'",$object->getShortName())."',".
							"'".str_replace("'","\'",$object->getDescription())."',".
							"'".str_replace("'","\'",$object->getFile())."',".
							"'".$object->getNumeric()."',".
							"'".$object->getGoal()."',".
							"'".$object->getCircleId()."',".
							"'".$object->getRoleId()."',".	
							($object->getUserId()>0?"'".$object->getUserId()."'":"NULL").",".	
							"'".$object->getRecurrenceId()."')";	
						if (!mysql_query($query, $this->_dbh)) {
							return "Erreur : ".$query;
						}	
						
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
						$object->setManager($this);
						$object->setModified(false);
						$this->_metric[$id]=$object;
					}
					return;
				case "holacracy\\Project" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_project set proj_showcircle='".$object->isShowCircle()."', proj_visibility=".$object->getVisibility().", proj_position=".$object->getPosition().", ".($object->getStatusDate()!=""?"proj_dateStatus='".$object->getStatusDate()->format("Y-m-d H:i:s")."',":"")." proj_title='".str_replace("'","\'",$object->getTitle())."', proj_description='".str_replace("'","\'",$object->getDescription())."', prst_id='".$object->getStatusId()."', role_id='".$object->getRoleId()."', user_id='".$object->getUserId()."', proj_dateModif=NOW(), typr_id='".$object->getTypeId()."' where proj_id=".$object->getId();
						//echo $query;
						$object->setManager($this);
						$object->setModified(false);							
						mysql_query($query, $this->_dbh);
						// Modifié et remplacé par une autre méthode - DDr 12.1.2015
						//$query="update t_project set proj_position=proj_position+1 where proj_position>=0".$object->getPosition()." and role_id=0".$object->getRoleId()." and prst_id=0".$object->getStatusId();
						//mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_project (user_id_proposer, role_id_proposer, proj_showcircle, proj_visibility, proj_dateStatus, proj_title,proj_description,prst_id, role_id, user_id, user_id_creator, typr_id) values ('".$object->getProposerId()."','".$object->getProposerRoleId()."','".$object->isShowCircle()."',".$object->getVisibility().",".($object->getStatusDate()!=""?"'".$object->getStatusDate()->format("Y-m-d H:i:s")."'":"NULL").", '".str_replace("'","\'",$object->getTitle())."','".str_replace("'","\'",$object->getDescription())."','".$object->getStatusId()."','".$object->getRoleId()."','".$object->getUserId()."','".(isset($_SESSION["currentUser"])?$_SESSION["currentUser"]->getId():"")."',".($object->getTypeId()!=""?$object->getTypeId():\holacracy\Project::PROJECT).")";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setManager($this);
						$object->setModified(false);						
						$object->setId($id);
						return $id;
					}
					return;
				case "holacracy\\ActionMoi" :    
					if ($object->getInsert()>0) {					
						// L'object existe déjà, le sauve	
						$query="update t_actionmoi set act_title='".str_replace("'","\'",$object->getTitle())."', act_description='".str_replace("'","\'",$object->getDescription())."', proj_id='".$object->getProjectId()."', user_id='".$object->getIdUserFocus()."', role_id='".$object->getRoleId()."', acst_id='".$object->getStatusId()."', act_timestampdelete='".$object->getTimeStampDelete()."' where act_id='".$object->getId()."'";
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé	
						
						$query="insert into t_actionmoi (act_id, act_insert, act_title,act_description,proj_id,role_id,user_id,acst_id,act_timestamp) values ('".$object->getId()."', '1', '".str_replace("'","\'",$object->getTitle())."','".str_replace("'","\'",$object->getDescription())."','".$object->getProjectId()."','".$object->getRoleId()."','".$object->getIdUserFocus()."','".$object->getStatusId()."','".$object->getTimeStamp()."')";
						mysql_query($query, $this->_dbh);
						mysql_insert_id($this->_dbh);
					}
					return;
				case "holacracy\\Accountability" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_accountability set acco_active='".$object->isActive()."', acco_description='".str_replace("'","\'",$object->getDescription())."', role_id='".$object->getRole()->getId()."' where acco_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_accountability (acco_active,role_id,acco_description) values ('".$object->isActive()."','".$object->getRoleId()."', '".str_replace("'","\'",$object->getDescription())."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;
				case "holacracy\\Scope" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_scope set scop_description='".str_replace("'","\'",$object->getDescription())."', scop_politiques='".str_replace("'","\'",$object->getPolitiques())."', role_id='".$object->getRoleId()."' where scop_id=".$object->getId();
						//echo $query;
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_scope (role_id,scop_description) values ('".$object->getRoleId()."', '".str_replace("'","\'",$object->getDescription())."')";
						//echo $query;
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;
				case "holacracy\\Tension" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_tension set tens_nom='".str_replace("'","\'",$object->getDescription())."', reun_id='".$object->getMeeting()->getId()."' where tens_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_tension (reun_id,tens_nom) values ('".$object->getMeeting()->getId()."', '".str_replace("'","\'",$object->getDescription())."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;
				case "holacracy\\TensionMoi" :
					if ($object->getId()>0) {
						//L'object existe déjà, le sauve
						$query="update t_tension_moi set tmoi_name='".str_replace("'","\'",$object->getName())."', tmoi_description='".str_replace("'","\'",$object->getDescription())."', tmoi_type='".$object->getType()."', role_id='".$object->getRoleId()."' where tmoi_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_tension_moi (tmoi_name, tmoi_description, user_id, orga_id, tmoi_type,role_id, circle_id) values ('".str_replace("'","\'",$object->getName())."', '".str_replace("'","\'",$object->getDescription())."','".$object->getUserId()."','".$object->getOrgId()."','".$object->getType()."', '".$object->getRoleId()."', '".$object->getCircleId()."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					} 
					return;
				case "holacracy\\Bug" :
					if ($object->getId()>0) {
						//L'object existe déjà, le sauve
						$query="update t_bug set bug_title='".str_replace("'","\'",$object->getTitle())."', bug_description='".str_replace("'","\'",$object->getDescription())."', bug_priority='".$object->getPriority()."' where bug_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_bug (bug_title, bug_description, user_id_creation, tybu_id, bug_priority) values ('".str_replace("'","\'",$object->getTitle())."', '".str_replace("'","\'",$object->getDescription())."','".$object->getAuthorId()."','".$object->getBugTypeId()."','".$object->getPriority()."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;
				case "holacracy\\BugStatus" :

						$query="insert into t_statusbug_user (stbu_id, bug_id, user_id, sbus_comment) values ('".$object->getId()."','".$object->getBugId()."','".$object->getUserId()."','".str_replace("'","\'",$object->getComment())."')";
						mysql_query($query, $this->_dbh);
					return;
				case "holacracy\\Gouvernance" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_reunion set reun_date='".$object->getDate()."' where reun_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_reunion (tyre_id, role_id_cercle, reun_date) values ('1','".$object->getCircle()->getId()."','".$object->getDate()."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;
				case "holacracy\\Action" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_action set acty_id='".$object->getActionType()->getId()."', tens_id='".$object->getTension()->getId()."', acti_objetsource_id='".$object->getSourceId()."', acti_objetbackup_id='".$object->getBackupId()."' where acti_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_action (acty_id, tens_id, acti_objetsource_id, acti_objetbackup_id) values ('".$object->getActionType()->getId()."','".$object->getTension()->getId()."','".$object->getSourceId()."','".$object->getBackupId()."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;
				case "holacracy\\Circle" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_role set orga_id='".$object->getOrganisationId()."', roty_id='".$object->getType()."', role_name='".str_replace("'","\'",$object->getName())."', role_purpose='".str_replace("'","\'",$object->getPurpose())."', role_strategy='".str_replace("'","\'",$object->getStrategy())."', role_id_superCircle='".$object->getSuperCircleId()."', role_active='".$object->isActive()."', user_id=".($object->getUserId()!=""?"'".$object->getUserId()."'":"NULL")." where role_id=".$object->getId();
						//echo $query;
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_role (orga_id, roty_id, role_name, role_purpose, role_strategy, role_id_superCircle, user_id) values ('".$object->getOrganisationId()."', '".$object->getType()."','".str_replace("'","\'",$object->getName())."','".str_replace("'","\'",$object->getPurpose())."','".str_replace("'","\'",$object->getStrategy())."','".$object->getSuperCircleId()."', ".($object->getUserId()!=""?"'".$object->getUserId()."'":"NULL").")";
						//echo $query;
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;
				case "holacracy\\Organisation" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_organisation set orga_public='".$object->getVisibility()."', orga_name='".str_replace("'","\'",$object->getName())."', orga_shortname='".str_replace("'","\'",$object->getShortName())."', orga_description='".str_replace("'","\'",$object->getDescription())."'".
						", orga_vision='".str_replace("'","\'",$object->getVision())."', orga_visiontxt='".str_replace("'","\'",$object->getVisionDescription())."'".
						", orga_mission='".str_replace("'","\'",$object->getMission())."', orga_missiontxt='".str_replace("'","\'",$object->getMissionDescription())."'".
						", orga_purpose='".str_replace("'","\'",$object->getPurpose())."', orga_purposetxt='".str_replace("'","\'",$object->getPurposeDescription())."'".
						", orga_website='".str_replace("'","\'",$object->getWebSite())."' where orga_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_organisation (orga_public, orga_name, orga_shortname, orga_description, orga_vision, orga_visiontxt,orga_mission, orga_missiontxt,orga_purpose, orga_purposetxt, orga_website) values ('".$object->getVisibility()."', '".str_replace("'","\'",$object->getName())."', '".str_replace("'","\'",$object->getShortName())."','".str_replace("'","\'",$object->getDescription())."',".
						"'".str_replace("'","\'",$object->getVision())."', '".str_replace("'","\'",$object->getVisionDescription())."',".
						"'".str_replace("'","\'",$object->getMission())."', '".str_replace("'","\'",$object->getMissionDescription())."',".
						"'".str_replace("'","\'",$object->getPurpose())."', '".str_replace("'","\'",$object->getPurposeDescription())."',".
						" '".str_replace("'","\'",$object->getWebSite())."')";
						
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;				
				case "holacracy\\Role" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_role set roty_id='".$object->getType()."', role_name='".str_replace("'","\'",$object->getName())."', role_purpose='".str_replace("'","\'",$object->getPurpose())."', role_id_superCircle='".$object->getSuperCircleId()."', role_active='".$object->isActive()."', role_id_source=".($object->getSourceId()!=""?"'".$object->getSourceId()."'":"NULL").", user_id=".($object->getUserId()!=""?"'".$object->getUserId()."'":"NULL").", circ_id_source=".($object->getSourceCircleId()!=""?"'".$object->getSourceCircleId()."'":"NULL").", role_id_master=".($object->getMasterId()!=""?"'".$object->getMasterId()."'":"NULL")." where role_id=".$object->getId();
						//echo $query;
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_role (roty_id, role_name, role_purpose, role_id_superCircle, role_id_source, circ_id_source, user_id, role_id_master) values ('".$object->getType()."','".str_replace("'","\'",$object->getName())."','".str_replace("'","\'",$object->getPurpose())."','".$object->getSuperCircleId()."',".($object->getSourceId()!=""?$object->getSourceId():"NULL").",".($object->getSourceCircleId()!=""?$object->getSourceCircleId():"NULL").",".($object->getUserId()!=""?$object->getUserId():"NULL").",".($object->getMasterId()!=""?$object->getMasterId():"NULL").")";

						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					return;
				case "holacracy\\RoleFiller" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_rolefiller set user_id='".$object->getUserId()."', rofi_focus='".str_replace("'","\'",$object->getFocus())."' where rofi_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_rolefiller (role_id, user_id, rofi_focus) values ('".$object->getRoleId()."','".$object->getUserId()."','".str_replace("'","\'",$object->getFocus())."')";
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}				
				case "holacracy\\Member" :
				case "holacracy\\User" :
					if ($object->getId()>0) {
						// L'object existe déjà, le sauve
						$query="update t_user set user_code='".($object->getCode()!=""?$object->getCode():"")."', user_isActive='".$object->isActive()."', user_lastConnexionDate='".(is_object($object->getLastConnexion())?$object->getLastConnexion()->format("Y-m-d H:i:s"):"")."', user_lang='".$object->getUserLangue()."', user_firstName='".str_replace("'","\'",$object->getFirstName())."', user_lastName='".str_replace("'","\'",$object->getLastName())."', user_userName='".str_replace("'","\'",$object->getUserName())."', user_email='".str_replace("'","\'",$object->getEmail())."'  where user_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					} else {
						// L'objet doit être créé
						$query="insert into t_user (user_code, user_isActive, user_firstName, user_lastName, user_userName, user_email) values ('".($object->getCode()!=""?$object->getCode():"")."','".$object->isActive()."','".str_replace("'","\'",$object->getFirstName())."','".str_replace("'","\'",$object->getLastName())."','".str_replace("'","\'",$object->getUserName())."','".str_replace("'","\'",$object->getEmail())."')";
						//echo $query;
						mysql_query($query, $this->_dbh);
						$id=mysql_insert_id($this->_dbh);
						$object->setId($id);
					}
					// Mise à jour du mot de passe si nécessaire
					if ($object->getPassword()!="") {
						$query="update t_user set user_password='".md5($object->getPassword())."' where user_id=".$object->getId();
						mysql_query($query, $this->_dbh);
					}
					return;
				default:
					trigger_error ("SAVE not possible on ".get_class($object)." object", E_USER_ERROR );
					return ;
				
				 
			}
	}
	
}
?>
