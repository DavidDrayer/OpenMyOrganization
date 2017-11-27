<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// ********************************************************************
	// ********* Validation ou refus d'une suggestion de projet        ****
	// ********************************************************************
	
	if ((isset($_GET["id"])) && (!empty($_GET["id"])) && (isset($_GET["circle"])) && (!empty($_GET["circle"]))&& (isset($_GET["check"])) && (!empty($_GET["check"]))) {
	//echo "on va accepter ou pas le projet => ".$_GET["id"]." avec ".$_GET["check"];
	//echo "on fais ensuite un reload vers le cercle ".$_GET["circle"];
	if (!isset($manager)) {
			$manager=new \datamanager\sqlManager($dbh);
			}
	$projet = $manager->loadProjects($_GET["id"]); //On recupère le user affilié
	if(!empty($projet)){
	$statut = $projet->getStatus(); //on recupere le statut existant
	//Si le projet existe on fait
		if($_GET["check"] == "false" && $statut == 0){ $manager->delete($projet);}
		if($_GET["check"] == "true" && $statut == 0){ $projet->setStatus(1); //On passe le statut à en cours
			$manager->save($projet); //On save le projet
			}
		$url = "http://".$_SERVER["HTTP_HOST"]."/circle.php?id=".$_GET["circle"]."#tabs-6";
		header("location:".$url.""); 
	}
	}
	else{
	header("location:http://".$_SERVER["HTTP_HOST"]); 
	}
?>