<?
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destin� � �tre inclus, qui utilisera le manager du niveau sup�rieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Supprime le metric
	$checklist=$_SESSION["currentManager"]->loadChecklist($_GET["id"]);
	$circleId=$checklist->getCircleId();
	
	// Ajoute la valeur (pas tr�s propre, devrait passer par la classe
	$query="insert into t_checklist_date (chli_id) values ('".$_GET["id"]."')";
	mysql_query($query, $dbh);
	
	//$_SESSION["currentManager"]->delete($checklist);
	

?>
    // $( "#dialogStd" ).dialog("close");
    refreshChecklist(<?=$circleId?>);
