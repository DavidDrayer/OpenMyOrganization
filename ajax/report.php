<?php
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("../include.php");
	
	if (isset($_SESSION["currentUser"])) { //si l'utilisateur est connect�
	//echo "ds le ajax pour envoer le formulaire rempli";
	
	//si c'est pas vide
	if (!empty($_POST["bug"]) || !empty($_POST["suggestion"])){
			
	$bug = $_POST["bug"];
	$sug = $_POST["suggestion"];
	$messageadmin = "Bonjour,<br/><br/>Vous avez recu une nouvelle notification de reporting pour un bug ou une suggestion pour OpenMyOrganization Beta : <br/><br/><h3>Bug(s) :</h3>".$bug."<br/><br/><h3>Suggestion(s) :</h3>".$sug."<br/><br/>Merci de rentrer ces indications dans le fichier 'produit OMO' du google drive";
	
	//Pour le user
	$to =$_SESSION["currentUser"]->getEmail();
	$prenom = $_SESSION["currentUser"]->getFirstName();
	$nom = $_SESSION["currentUser"]->getLastName();
	$subject = "[OMO] Merci !";
	$message = "<html>Bonjour,<br/><br/>Merci pour votre participation � l'�volution du logiciel OMO !<br/><br/>L'�quipe OMO</html>";
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
	$headers .= 'From: noreply@openmyorganisation.com' . "\r\n" ;
	
	//Pour l'admin
	$admin = "kevindalton86@gmail.com";
	$subjectadmin = "[OMO] Notification report Bugs ou Suggestions";
	$messageadmin = "<html>Bonjour,<br/><br/>Vous avez recu une nouvelle notification de ".$prenom." ".$nom." pour un reporting de bug ou une suggestion pour OpenMyOrganization Beta : <br/><br/><h3>Bug(s) :</h3>".$bug."<br/><br/><h3>Suggestion(s) :</h3>".$sug."<br/><br/>Merci de rentrer ces indications dans le fichier 'produit OMO' du google drive.</html>";
	
	if (mail($to, $subject, $message, $headers) && mail($admin, $subjectadmin, $messageadmin, $headers)) { 
	echo "ok"; 
	}
	
	}
	
	else {
			// G�n�re un message d'erreur
			echo "Merci de remplir un des 2 champs";
			
		}
	
	
	
	}
?>