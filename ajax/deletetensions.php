<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Supprime la tension
	$tension=$_SESSION["currentManager"]->loadTensionMoi($_GET["tid"]);
	$_SESSION["currentManager"]->delete($tension);
	
	echo "<script>$('#dialogStdContent').load('/formulaires/form_tension.php?id=".$_GET["id"]."');</script>";
?>
    