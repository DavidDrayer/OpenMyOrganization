<!DOCTYPE html>
<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");

	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($dbh);
	if (isset($_GET["id"])) {
		// Chargement de l'élément cercle sélectionné
		$organisations=$manager->loadOrganisation($_GET["id"]);
	} else {
		
		if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->getId()>1) {
			// Ici en incluant celles dont l'utilisateur courant fait partie (et pas seuléement les visibles)
			$organisations=$manager->loadOrganisation($_SESSION["currentUser"]);
		} else {
			$organisations=$manager->loadOrganisation();
		}
	}
	// Instantiation d'un manager graphic adapté au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un cercle
	$mainEditor=$graphicManager->getBrowser($organisations);
	// Affichage d browser
	$mainEditor->display();
	
?>
