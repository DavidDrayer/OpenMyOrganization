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
	$_SESSION["currentManager"]->delete($checklist);
	

?>
    $( "#dialogStd" ).dialog("close");
    refreshChecklist(<?=$circleId?>);