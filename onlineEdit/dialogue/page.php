<?
		session_start();
		//Parcours toutes les pages et les affiche
		include("../db.php");
		// et se connecte à la base de données
		$dbh =  connectDb(); 	
		
		// Formulaire posté
		if (isset($_POST["btn_del"])) {
			echo $_POST["urlLocal"];
			$query="delete from t_page where page_id=".$_POST["urlLocal"];
			mysql_query ($query, $dbh);
			$query="delete from t_pageinfo where page_id=".$_POST["urlLocal"];
			mysql_query ($query, $dbh);
			$query="delete from t_zone where zone_id like '".$_POST["urlLocal"]."_%";
			mysql_query ($query, $dbh);
		}
?>
<html>
<head>
<title>Pages</title>
<link href="tree.css" rel="stylesheet" type="text/css">
<link href="onglet.css" rel="stylesheet" type="text/css">
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
</head>
<script language="javascript" src="palette.js"></script>
<script language="javascript" src="lib/tree.js"></script>

<script>
	function setLink (url) {
		document.formulaire.urlLocal.value=url;
		
	}

</script>
<body onLoad="initTree(myThirdTree)">
		<div class="ongletLegende">Liste des pages du site Web.</div>
			<div class="ongletContent">
<? 	$height=383;
	include "lib/pageChooser.php"; 
?>
	
	<form method="post" name="formulaire" action="newPage.php"><div class="bottomButton"><input Type="hidden" name="urlLocal" id="urlLocal"/> <input type="submit" value="Nouvelle page..." onclick="if (document.formulaire.urlLocal.value=='') {alert('Sélectionnez la page parente.'); return false;}"> 
	    <input type="submit" name="btn_del" value="Supprimer" onclick="if (document.formulaire.urlLocal.value=='') {alert('Sélectionnez une page.'); return false;}; if (confirm('Etes-vous sûr de vouloir supprimer cette page?\nCette opération est irréversible.')) {document.formulaire.action='page.php'} else {return false;}"> <input type="button" onclick="if (document.formulaire.urlLocal.value!='') {parent.location='../../index.php?page='+document.formulaire.urlLocal.value+'&edit=true'; window.close();} else {alert('Veuillez sélectionnez une page.')}" value="Afficher"> <input type="button" value="Fermer" name="fermer" onClick="parent.closePopup();"/></div></form></div>
</body>
</html>
