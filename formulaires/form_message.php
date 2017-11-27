<?
	include_once("../include.php");
	
	// Charge les infos sur le destinataire  - DDr 5.6.2014
	$dest=$_SESSION["currentManager"]->loadUser($_POST["msg_to"]);
	$exp=$_SESSION["currentManager"]->loadUser($_POST["msg_from"]);
	
	// Base du message  - DDr 5.6.2014
	$title=utf8_decode($_POST["msg_title"]);
	$text=utf8_decode($_POST["msg_text"]);

	// Formate le contenu en fonction des rôles  - DDr 5.6.2014
	if (isset($_POST["msg_toRole"])  && $_POST["msg_toRole"]>0) {
		$destRole=$_SESSION["currentManager"]->loadRole($_POST["msg_toRole"]);
		$org=$destRole->getOrganisation();
	}
	if (isset($_POST["msg_fromRole"]) && $_POST["msg_fromRole"]>0) {
		$expRole=$_SESSION["currentManager"]->loadRole($_POST["msg_fromRole"]);
		$org=$expRole->getOrganisation();
	}
	
	if (isset($org)) {
		if ($org->getShortName()!="")
			$entete="[".$org->getShortName()."] [";
		else
			$entete="[".$org->getName()."] [";
		if (isset($expRole)) 
			$entete.=$expRole->getName();
		else
			$entete.=$exp->getUserName();
		
		$entete.=" > ";
		if (isset($destRole)) 
			$entete.=$destRole->getName();
		else
			$entete.=$dest->getUserName();

		$entete.="] ";
	} else {
		$entete="[O.M.O] ";
	}
	
	// Envoie le message  - DDr 5.6.2014
	$dest->sendMessage($entete.$title, str_replace("\n","<br>",$text),$exp);
	
	

?>
Votre message a bien été envoyé.
