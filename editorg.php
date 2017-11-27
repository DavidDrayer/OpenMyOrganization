<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($dbh);
	// Chargement de l'élément organisation sélectionné
	if (isset($_GET["id"])) {
		$organisation=$manager->loadOrganisation($_GET["id"]);
	} else {
		$organisation=new \holacracy\Organisation($dbh);
	}
	// Instantiation d'un manager graphic adapté au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un editeur pour un cercle
	$mainEditor=$graphicManager->getEditor($organisation);
	// Affichage de l'éditeur
	$mainEditor->display();
	
?>

