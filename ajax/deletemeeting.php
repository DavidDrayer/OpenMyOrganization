<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Supprime la tension
	$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["id"]);
	$circle=$meeting->getCircle()->getId();
	$_SESSION["currentManager"]->delete($meeting);
	
	echo "$('#dialogStdContent').load('/formulaires/form_meeting.php?circle=".$circle."');";
?>
    