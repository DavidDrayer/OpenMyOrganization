<!DOCTYPE html>
<?php	
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	// Chargement de l'élément cercle sélectionné
	$circle=$manager->loadCircle($_GET["id"]);
	// Instantiation d'un manager graphic adapté au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un cercle
	$mainDisplay=$graphicManager->getBrowser($circle);
	// Affichage d browser
	$mainDisplay->display();
?>
