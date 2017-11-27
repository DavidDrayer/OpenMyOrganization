<?php
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donn�e
	$manager=new \datamanager\SqlManager($dbh);
	// Instantiation d'un manager graphic adapt� au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();

	// Chargement la liste des organisations
	if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->getId()>1) {
		// Ici en incluant celles dont l'utilisateur courant fait partie (et pas seul�ement les visibles)
		$organisations=$manager->loadOrganisation($_SESSION["currentUser"]);
		$mainDisplay=$graphicManager->getBrowser($organisations);
		$mainDisplay->display();
	} else {
		// Affiche l'�cran d'accueil
		$mainDisplay=new \widget\wg_Login();
		$mainDisplay->display(); 
	}
?>
