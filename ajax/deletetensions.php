<?
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destin� � �tre inclus, qui utilisera le manager du niveau sup�rieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Supprime la tension
	$tension=$_SESSION["currentManager"]->loadTensionMoi($_GET["tid"]);
	$_SESSION["currentManager"]->delete($tension);
	
	echo "<script>$('#dialogStdContent').load('/formulaires/form_tension.php?id=".$_GET["id"]."');</script>";
?>
    