<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Affiche les choix de différents formats
	echo "Format JS";
	echo "Format Json";
	echo "Format Facebook";
	
	// Affiche le champ à copier
	echo "<div style='border:1px solid black; padding:5px;'>".htmlentities("<script src='http://dev.openmyorganization.com/getMetric.php?id=".$_GET["id"]."&format=js'></script>")."</pre>";
	
?>
