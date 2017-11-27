<!DOCTYPE html>
<?php

		
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	if (isset($_GET["id"])) {
		$id=$_GET["id"];
	} else {
		$id=0; //ID d'un rôle par defaut => Page erreur plutôt à mettre si l'ID n'existe pas
	}


	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\sqlManager($dbh);	
	// Chargement de l'élément role sélectionné
	$role=$manager->loadRole($id,1);
	// Instantiation d'un manager graphic adapté au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un role
	$mainDisplay=$graphicManager->getBrowser($role);
	// Affichage d browser
	$mainDisplay->display();

?>
