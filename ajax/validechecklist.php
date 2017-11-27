<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Supprime le metric
	$checklist=$_SESSION["currentManager"]->loadChecklist($_GET["id"]);
	$circleId=$checklist->getCircleId();
	
	// Ajoute la valeur (pas très propre, devrait passer par la classe
	$query="insert into t_checklist_date (chli_id) values ('".$_GET["id"]."')";
	mysql_query($query, $dbh);
	
	//$_SESSION["currentManager"]->delete($checklist);
	

?>
    // $( "#dialogStd" ).dialog("close");
    refreshChecklist(<?=$circleId?>);
