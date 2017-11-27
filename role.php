<!DOCTYPE html>
<?php

		
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("include.php");
	if (isset($_GET["id"])) {
		$id=$_GET["id"];
	} else {
		$id=0; //ID d'un r�le par defaut => Page erreur plut�t � mettre si l'ID n'existe pas
	}


	// Instantiation du gestionnaire de base de donn�e
	$manager=new \datamanager\sqlManager($dbh);	
	// Chargement de l'�l�ment role s�lectionn�
	$role=$manager->loadRole($id,1);
	// Instantiation d'un manager graphic adapt� au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un role
	$mainDisplay=$graphicManager->getBrowser($role);
	// Affichage d browser
	$mainDisplay->display();

?>
