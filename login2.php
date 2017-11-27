<!-- Yopla -->
<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($dbh);
	// Chargement la liste des organisations
	if (isset($_SESSION["currentUser"])) {
		// Ici en incluant celles dont l'utilisateur courant fait partie (et pas seuléement les visibles)
		$organisations=$manager->loadOrganisation($_SESSION["currentUser"]);
	} else {
		$organisations=$manager->loadOrganisation();
	}
	// Instantiation d'un manager graphic adapté au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un cercle
	$mainDisplay=$graphicManager->getBrowser($organisations);
	// Affichage d browser
	if (isset($mainDisplay)) 
		$mainDisplay->display(); 
	else {
		// Affiche l'écran d'accueil
		$mainDisplay=new \widget\wg_OrganisationBrowser($organisations);
		$mainDisplay->display(); 
	}
	
?>
