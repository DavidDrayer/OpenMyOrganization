<?php 

	include_once("../include.php");
	
	// ********************************************************************
	// ********* Zone POST : formulaire envoyé et données à manipuler  ****
	// ********************************************************************
    if (isset($_POST["action"]) && !empty($_POST["action"])) {
    $action = $_POST["action"];
    switch($action) { //Switch de l'action puis de l'appel des bonnes fonctions
      	  
	  case "GetRoles": 
	  GetRoles($_POST);
	  break;
	  case "GetRoles2": 
	  GetRoles($_POST,"idSubRole");
	  break;
	  case "GetProjects": 
	  GetProjects($_POST);
	  break;
	  case "GetFiller": 
	  GetFiller($_POST);
	  break;
	  case "GetMembers": 
	  GetMembers($_POST);
	  break;
	  }
	}
	
	// ****************************************************************
	// ***********  Fonctions pour le formulaire triage AJAX *******
	// ****************************************************************
	
	function getProjects($post) {
		$role = $_SESSION["currentManager"]->loadRole($post["RoleID"]);
		$projects = $role->getProjects(\holacracy\Project::CURRENT_PROJECT | \holacracy\Project::BLOCKED_PROJECT);
		if (count($projects)>0) {//Si plusieurs focus on fait
		echo "<select name='idProjectProposer_".$post["SelectformID"]."' id='idProjectProposer"."_".$post["SelectformID"]."'>";
		echo "<option value=''>choisissez...</option>";
			foreach ($projects as $project) {
					echo "<option value='".$project->getId()."'>".$project->getTitle()."</option>";
				}
			
		echo "</select>";	
		} 
	}
	
	function getRoles($post, $name='idRoleProposer') {
		$circle = $_SESSION["currentManager"]->loadCircle($post["CircleID"]);

		if (isset($post["ProposerID"])) {
			$member = $_SESSION["currentManager"]->loadUser($post["ProposerID"]);
			$roles = $member->getRoles($circle,255,true);
		} else {
			$roles = $circle->getRoles();
		}
		if (count($roles)==0) {echo "-1";return;}
		echo "<select name='".$name."_".$post["SelectformID"]."' id='".$name."_".$post["SelectformID"]."'>";
		echo "<option value=''>choisissez...</option>";
		if (count($roles)>0) {//Si plusieurs focus on fait
			foreach ($roles as $role) {
					echo "<option value='".$role->getId()."'";
					if (isset($post["selected"]) && $post["selected"]==$role->getId()) echo " selected";
					echo ">".$role->getName()."</option>";
				}
		} 	
		echo "</select>";	
	}
	
	function GetMembers($post) {
		$role = $_SESSION["currentManager"]->loadRole($post["RoleID"]);
		//echo "<select name='idRoleFocus_".$post["SelectformID"]."' id='idRoleFocus"."_".$post["SelectformID"]."'>";
		echo "<select id='idRoleFocus"."_".$post["SelectformID"]."' name='idRoleFocus_".$post["SelectformID"]."[]' data-placeholder='Choisissez un ou plusieurs administrateurs...' class='chosen-select' multiple style='width:100%'>";

		$members = $role->getMembers();
		if (count($members)>0) {//Si plusieurs focus on fait
			foreach ($members as $member) {
					echo "<option value='".$member->getId()."'>".$member->getUserName()."</option>";
				}
		} 	
		echo "</select>";	
	}
	
	function GetFiller($post){ //Récupération des fillers si il existe
	$role = $_SESSION["currentManager"]->loadRole($post["RoleID"]);
	$display = array();
 	echo "<select name='idRoleFocus_".$post["SelectformID"]."' id='idRoleFocus"."_".$post["SelectformID"]."'>";
	if (isset($post["default"]) && $post["default"]!="") {
		echo "<option value=''>".$post["default"]."</option>";
	}
	echo "<optgroup label='Personne(s) en charge'>";
	if ($role->getUserId()>0) {
		echo "<option value='".$role->getUserId()."'";
    			if (isset($post["selected"]) && $post["selected"]==$role->getUserId()) echo " selected";
    			echo ">".$role->getUser()->getUserName()."</option>"; //non affecté
		$display[]=$role->getUserId();
	} else {
		echo "<option value=''>Affect&eacute; par d&eacute;faut au Premier Lien</option>";
	}
	if (!($role->getType() & (\holacracy\Role::CIRCLE | \holacracy\Role::STRUCTURAL_ROLES))) {
	$roleFillers = $_SESSION["currentManager"]->loadRoleFillers($post["RoleID"]);
	if (count($roleFillers)>0) {//Si plusieurs focus on fait
		foreach ($roleFillers as $filler) {
				$display[]=$filler->getUserId();
    			echo "<option value='".$filler->getUserId()."'";
    			if (isset($post["selected"]) && $post["selected"]==$filler->getUserId()) echo " selected";
    			echo ">".$filler->getUserName()." (focus ".$filler->getFocus().")</option>";
			}
	} 
	}
	echo "</optgroup>";
	if (!isset($post["more"]) || $post["more"]==1) {
		echo "<optgroup label='Autres membres du cercle'>";
		
		$members=$role->getSuperCircle()->getMembers();
		foreach ($members as $member) {
			if (!in_array($member->getId(),$display)) {
				echo "<option value='".$member->getId()."'";
					if (isset($post["selected"]) && $post["selected"]==$member->getId()) echo " selected";
				echo ">".$member->getUserName()."</option>";
			}
		}
							
							
		echo "</optgroup>";
	}

		echo "</select>";

	}
