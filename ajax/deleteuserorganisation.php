<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/plugins/libMiniature.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}

	if (isset($_GET['action'])) {
		$user = $_GET['user'];
		$organisation = $_GET['orga'];
		$manager->delMemberOrganisation($user,$organisation);
	}
	
	$organisation=$manager->loadOrganisation($_GET['orga']);
	echo "<div>";
	foreach ($organisation->getMembers() as $member) {

		echo "<div style=' margin-right:5px;margin-bottom:5px;' class='omo-user-block user_draggable ui-corner-all'>";
		
		if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isAdmin($organisation))) {
			echo "<a class='omo-delete omo-removeOrganisationMember' href='ajax/deleteuserorganisation.php?action=delete&user=".$member->getId()."&orga=".$organisation->getId()."' alt=\"Supprimer le membre de l'organisation\">&nbsp;</a>";
	
		} 
		if (checkMini("/images/user/".$member->getId().".jpg",30,30,"mini",1,5)) {
			echo "<a href='/user.php?id=".$member->getId()."&organisation=".$organisation->getId()."' class='dialogPage omo-user-img";
			if ($member->getLastConnexion()->getTimestamp()<time()-(2*7*24*60*60)) echo " usr_danger";
			echo "' alt='".$member->getFullName()."'><img src='/images/user/mini/".$member->getId().".jpg'/></a>";
		} else {
			echo "<a href='/user.php?id=".$member->getId()."&organisation=".$organisation->getId()."' class='dialogPage omo-user-img";
			if ($member->getLastConnexion()=="" || $member->getLastConnexion()->getTimestamp()<time()-(2*7*24*60*60)) echo " usr_danger";
			echo "' alt='".$member->getFullName()."'><img src='/images/user/mini/0.jpg'/></a>";
		}

		echo "<b>".$member->getFullName()." </b>";
		if ($member->getLastConnexion()) {
		echo "<br/><i>Connexion le ".$member->getLastConnexion()->format("d.m.Y")."</i>";
	} else {
			echo "<br/><i>Jamais connecté</i>";
	}
		echo "</div>";
	}
	echo "</div>";
	if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isAdmin($organisation))) {
			echo "<a id='addOrganisationMember' href='formulaires/form_adduser.php?orga=".$organisation->getId()."' class='dialogPage' alt=\"Ajouter un membre dans l'organisation\">Ajouter un membre</a>";

	} 
	
	
?>	
<script>
		$(".omo-removeOrganisationMember").click(function() {
			// Envoie l'URL en ajax
			if (prompt("Voulez-vous vraiment supprimer ce membre de l'organisation?\n\n Ecrivez DELETE dans le champ ci-dessous pour confirmer.")=="DELETE")
			{$("#organisation_members").load($(this).attr("href"));}
			return false;
		});
	
		$("#addOrganisationMember").button();
</script>
