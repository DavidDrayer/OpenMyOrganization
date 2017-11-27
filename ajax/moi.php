<?php 
// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// ********************************************************************
	// ********* Zone POST : formulaire envoyé et données à manipuler  ****
	// ********************************************************************
    if (isset($_POST["action"]) && !empty($_POST["action"])) {
    $action = $_POST["action"];
    switch($action) { //Switch de l'action puis de l'appel des bonnes fonctions
      	  
	  case "AddAction": 
	  AddAction($_POST);
	  break;
	  
	  case "AddProject": 
	  AddProject($_POST);
	  break;
	  
	  case "EditProject": 
	  EditProject($_POST["ID"],$_POST);
	  break;
	  	  
	  case "EditAction": 
	  EditAction($_POST["ID"],$_POST);
	  break;
	  
	  case "EditItemBox": 
	  $iditem = $_POST["ID"];
	  $title = $_POST["title"];
	  $title = html_entity_decode($title,ENT_NOQUOTES, "ISO-8859-1");
	  EditItemBox($iditem,$title);
	  break;
	     
	  case "TransformActionProjectInActionSolo": 
	  TransformActionProjectInActionSolo($_POST["ID"],$_POST["ROLE"]);
	  break;
	  
	  case "TransformActionSoloInActionProject": 
	  TransformActionSoloInActionProject($_POST["ID"],$_POST["PROJET"]);
	  break;
	  
	  case "DeleteAction": 
	  DeleteAction($_POST["ID"]);
	  break;
    }
	}
	
	// ****************************************************************
	// ***********  Fonctions pour le MOI pour les actions AJAX *******
	// ****************************************************************
		
	function AddProject($post){ //Ajout d'un projet
	$title = T_("Nouveau projet");
	$projet=new \holacracy\Project();
	$projet->setTitle(utf8_decode($title));
	$projet->setDescription(utf8_decode($post["proj_description"]));
	$projet->setRole($post["role_id"]);
	$projet->setUser($post["user_id"]);
	$projet->setStatus($post["acst_id"]);
	$projet->setStatusDate(new DateTime());

	$returnidprojet = $_SESSION["currentManager"]->save($projet);
	echo $returnidprojet; //renvoi en ajax le numéro du projet
	}
	
	function AddAction($post){ //Ajout d'une action rattaché à un projet
	$title = T_("Nouvelle action");
	$timestamp = time();
	$id = $timestamp."moi".$_SESSION["currentUser"]->getID();

    $actionMoi=new \holacracy\ActionMoi();
	$actionMoi->setId($id);
	$actionMoi->setTitle(utf8_decode($title));
	$actionMoi->setDescription(utf8_decode($post["act_description"]));
	$actionMoi->setProjectId($post["proj_id"]);
	$actionMoi->setRoleId($post["role_id"]); //
	if($post["role_id"]!= 0){//Si c'est une action solo rôle
	$fillers=$_SESSION["currentManager"]->loadRoleFillers($post["role_id"]);
		if (count($fillers)>0) {//Si il y a plusieurs focus
		$actionMoi->setIdUserFocus($_SESSION["currentUser"]->getID());	
		} 
	}
	
	$actionMoi->setStatus($post["acst_id"]);
	$actionMoi->setInsert(0); //pour insérer l'action et non editer
	$actionMoi->setTimeStamp($timestamp);

	$_SESSION["currentManager"]->save($actionMoi);
	
	echo $id; //renvoi en ajax le numéro d'action
	}
	
	function EditProject($id,$post){ //Edition d'un Projet et Action Solo
    $projet = $_SESSION["currentManager"]->loadProjects($id);
	if (isset($post["title"]) && !empty($post["title"])){ $projet->setTitle(utf8_decode($post["title"]));}
	if (isset($post["statut"]) && !empty($post["statut"])){ $projet->setStatus($post["statut"]);}
	if (isset($post["notes"]) && !empty($post["notes"])){ $projet->setDescription(utf8_decode($post["notes"]));}
	if (isset($post["role"]) && !empty($post["role"])){ $projet->setRole($post["role"]);}
	$_SESSION["currentManager"]->save($projet);
	}
	
	function EditAction($id,$post){
	$actionMoi = $_SESSION["currentManager"]->loadActionsMoi($id);
	if (isset($post["title"]) && !empty($post["title"])){ $actionMoi->setTitle(utf8_decode($post["title"]));}
	if (isset($post["statut"]) && !empty($post["statut"])){ 
	$actionMoi->setStatus($post["statut"]);
		if($post["statut"] == 16){
		$actionMoi->setTimeStampDelete();
		}
	}
	if (isset($post["notes"]) && !empty($post["notes"])){ $actionMoi->setDescription(utf8_decode($post["notes"]));}
	if (isset($post["project"]) && !empty($post["project"])){ $actionMoi->setProjectId($post["project"]);}
	if (isset($post["role"]) && !empty($post["role"])){ $actionMoi->setRoleId($post["role"]);}
	$_SESSION["currentManager"]->save($actionMoi);
	}
	
	function EditItemBox($id,$title){
	//Changer le titre de l'ID ITEM pour la Inbox
	mail("wakanda.kevin@gmail.com","EditItemBox",$id.$title);
	}
	
	function TransformActionProjectInActionSolo($idaction,$roletarget){
	$actionMoi=$_SESSION["currentManager"]->loadActionsMoi($idaction);
	$actionMoi->setProjectId(0);
	$actionMoi->setRoleId($roletarget); //
	$_SESSION["currentManager"]->save($actionMoi);
	}
	
	function TransformActionSoloInActionProject($idactionsolo,$projetarget){
	$actionMoi=$_SESSION["currentManager"]->loadActionsMoi($idactionsolo);
	$actionMoi->setProjectId($projetarget);
	$actionMoi->setRoleId(0); //
	$_SESSION["currentManager"]->save($actionMoi);
	}
	
	//Pour les actions supprime via le menu et non coché
	function DeleteAction($id){ //Suppresion d'une action d'un projet
	$actionMoi=$_SESSION["currentManager"]->loadActionsMoi($id);
	$_SESSION["currentManager"]->delete($actionMoi);
	}

?>