<?php
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("include.php");

	// Instantiation du gestionnaire de base de donn�e
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	// Chargement de l'�l�ment cercle s�lectionn�
	
	$UserMoi[1] = $_SESSION["currentUser"]; //Recup le User
	$UserMoi[2] = $manager->loadOrganisation($_GET["id"]);	//Recup l'ORG
	
	//$UserMoi[3] = $manager->loadTensoinMoiList($UserMoi[2],$UserMoi[1],NULL); // Recup les tensions
	
//$_SESSION["currentUser"]->getMoi($UserMoi[2],$UserMoi[3]); //
	
	// Instantiation d'un manager graphic adapt� au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un cercle
	$mainDisplay=$graphicManager->getBrowser($UserMoi);
	// Affichage d browser
	$mainDisplay->display();
		
?>
