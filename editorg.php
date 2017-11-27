<?php
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donn�e
	$manager=new \datamanager\SqlManager($dbh);
	// Chargement de l'�l�ment organisation s�lectionn�
	if (isset($_GET["id"])) {
		$organisation=$manager->loadOrganisation($_GET["id"]);
	} else {
		$organisation=new \holacracy\Organisation($dbh);
	}
	// Instantiation d'un manager graphic adapt� au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un editeur pour un cercle
	$mainEditor=$graphicManager->getEditor($organisation);
	// Affichage de l'�diteur
	$mainEditor->display();
	
?>

