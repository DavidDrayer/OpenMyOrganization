<!DOCTYPE html>
<?php	
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donn�e
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	// Chargement de l'�l�ment cercle s�lectionn�
	$circle=$manager->loadCircle($_GET["id"]);
	// Instantiation d'un manager graphic adapt� au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un cercle
	$mainDisplay=$graphicManager->getBrowser($circle);
	// Affichage d browser
	$mainDisplay->display();
?>
