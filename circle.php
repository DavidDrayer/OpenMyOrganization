<!DOCTYPE html>
<?php	
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donn�e
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	if (isset($_GET["id"]) && $_GET["id"]>0) {
		// Chargement de l'�l�ment cercle s�lectionn�
		$circle=$manager->loadCircle($_GET["id"]);
		// Instantiation d'un manager graphic adapt� au support (browser, PDA, etc...)
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
