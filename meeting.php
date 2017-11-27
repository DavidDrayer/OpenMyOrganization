<?php
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("include.php");
	
		// Si aucun cercle n'est d�fini, charge par d�faut le num�ro 2 (� supprimer en prod)
	if (isset($_GET["id"])) {
		$id=$_GET["id"];
	} else {
		header('Location: index.php'); 
		break;
	}
?>

<?php
	// Instantiation du gestionnaire de base de donn�e
	$manager=new \datamanager\SqlManager($dbh);
	// Chargement de l'�l�ment cercle s�lectionn�
	$meeting=$manager->loadMeeting($id);
	// Instantiation d'un manager graphic adapt� au support (browser, PDA, etc...)
	$graphicManager=new \displaymanager\GraphicManager();
	// Chargement d'un browser pour un cercle
	$mainEditor=$graphicManager->getBrowser($meeting);
	// Affichage d browser
	$mainEditor->display();
	
?>

