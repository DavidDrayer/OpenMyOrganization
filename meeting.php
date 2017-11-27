<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	
		// Si aucun cercle n'est défini, charge par défaut le numéro 2 (à supprimer en prod)
	if (isset($_GET["id"])) {
		$id=$_GET["id"];
	} else {
		header('Location: index.php'); 
		break;
	}
?>

<?php
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($dbh);
	// Chargement de l'élément cercle sélectionné
	$meeting=$manager->loadMeeting($id);
	// Instantiation d'un manager graphic adapté au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un cercle
	$mainEditor=$graphicManager->getBrowser($meeting);
	// Affichage d browser
	$mainEditor->display();
	
?>

