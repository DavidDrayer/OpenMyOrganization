<?php
	// Le script cron.php est executé tous les matins à 2H30

	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	
	//Rapport de CRON
	$rapport = array();

	//Archivage des actionsMOI terminées (statut 16) depuis plus de X jours
	$actionsfinished =$manager->loadActionsMoi(16); 
	$timestamp = time(); //Timestamp actuel
	$cmptactiondelete = 0;
	foreach($actionsfinished as $actionfinished){
		if($timestamp > $actionfinished->getTimeStampDelete()){
		//On archive l'actionMOI
		$manager->delete($actionfinished); 
		$cmptactiondelete++;
		}
	}
	//Donnée pour le rapport
	$rapport['actionMoiArchived'] = $cmptactiondelete;
	

	
	// Rapport du CRON JOURNALIER
	$subject = "[OMO] - Rapport du CRON journalier";
	// Dans le cas où nos lignes comportent plus de 70 caractères, nous les coupons en utilisant wordwrap()
	$message = "Total des actionsMoi archivées : ".$rapport['actionMoiArchived'];
	// Envoi du mail
	mail('kevin@ennoia.ch',$subject, $message);
?>