<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Supprime le metric
	$metric=$_SESSION["currentManager"]->loadMetric($_GET["id"]);
	$circleId=$metric->getCircleId();
	$_SESSION["currentManager"]->delete($metric);
	

?>
    $( "#dialogStd" ).dialog("close");
    refreshMetrics(<?=$circleId?>);