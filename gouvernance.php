<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	
		// Si aucun cercle n'est défini, charge par défaut le numéro 2 (à supprimer en prod)
	if (isset($_GET["id"])) {
		$id=$_GET["id"];
	} else {
		$id=2;
	}
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<!-- Chargement des scripts et styles pour jquery et jquery-ui -->
		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
	<link rel="stylesheet" href="/plugins/jquery-ui-1.10.3.custom/css/custom-theme/jquery-ui-1.10.4.custom.css" />	
	<link rel="stylesheet" href="/plugins/jquery-ui-1.10.3.custom/css/custom-theme/special_omo.css" />	
	
	<!-- Editeur de textes -->
	<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	
	
	<!-- Chargement des styles propre au site -->
<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/omo.css" />
	
	<!-- Info sur la page -->
	<title>O.M.O &gt; Gouvernance </title>
</head>
<body>
<?php
	// Instantiation du gestionnaire de base de donnée
	$manager=new datamanager\SqlManager($dbh);
	
	// Un id est-il spécifié?
	if (isset($_GET["id"])) {
		// Si oui,charge une réunion de gouvernance existante
		$gouvernance=$manager->loadGouvernance($_GET["id"]);
	} else {
		// Si non, regarde si un id de cercle est spécifié
		if (isset($_GET["circleId"])) {
			// Si oui, crée une nouvelle réunion de gouvernance pour ce cercle
			$circle=$manager->loadCircle($_GET["circleId"]);
			$gouvernance=new holacracy\Gouvernance;
			$gouvernance->setDate(date("Y-m-d H:i:s"));
			$gouvernance->attachTo($circle);
			$manager->save($gouvernance);  
			   
			// Provisoirement, crée également une première tension par défaut
			$tension=new holacracy\Tension;
			$tension->setDescription("Default Tension");
			$tension->attachTo($gouvernance);
			$manager->save($tension);
			
			// Redirige sur la page adéquat
			header('Location: gouvernance.php?id='.$gouvernance->getId());
			exit;
            
		} else {
			// Si non, génère une erreur
			exit;
		}
	}
	// Instantiation d'un manager graphic adapté au support (browser, PDA, etc...)
	$graphicManager=new displaymanager\GraphicManager();
	// Chargement d'un browser pour un cercle
	$mainEditor=$graphicManager->getEditor($gouvernance);
	// Affichage de l'éditeur
	$mainEditor->display();
	
?>
</body>
</html>