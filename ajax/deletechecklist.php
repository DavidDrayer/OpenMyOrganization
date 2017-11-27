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
	$_SESSION["currentManager"]->delete($checklist);
	

?>
    $( "#dialogStd" ).dialog("close");
    refreshChecklist(<?=$circleId?>);