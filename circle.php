<!DOCTYPE html>
<?php	
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	if (isset($_GET["id"]) && $_GET["id"]>0) {
		// Chargement de l'élément cercle sélectionné
		$circle=$manager->loadCircle($_GET["id"]);
		// Instantiation d'un manager graphic adapté au support (browser, PDA, etc...)
		$graphicManager=new \displaymanager\GraphicManager((isset($_GET["display"])?$_GET["display"]:1));
		// Chargement d'un browser pour un cercle
		$mainDisplay=$graphicManager->getBrowser($circle);
		// Affichage d browser
		$mainDisplay->display();
	} else {
		header("location:index.php");
		exit;
	}
?>
