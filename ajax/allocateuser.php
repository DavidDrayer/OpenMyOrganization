<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<?php
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destin� � �tre inclus, qui utilisera le manager du niveau sup�rieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}

	$roleID = $_POST['roleid']; //le role ID
	$nom = $_POST['memberadd']; //Decouper avec un slip DavidD_3
	$splitnom = explode("_", $nom);
	$username = $splitnom[0]; 
	$userID = $splitnom[1]; 
	$manager->addMemberFillerRole($roleID,$userID);
	
?>	
