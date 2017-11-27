<?
include ("db.php");
?>
<html>
<head>
<title>Paramétrage du module</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="dialogue/onglet.css" rel="stylesheet" type="text/css">
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
<script>
	nbTime=-1;
</script>
</head>

<body>
<div class="ongletLegende">Traduction du module : <?=$_POST["name"]?>.</div>
<div class="ongletContent">
<?
// Séletion de tous les éléments à traduire
$query="Select * from tm_dico where dico_id like '".$_POST["name"]."_%' and lang_id=".$_POST["langueEdit"]." order by dico_id";
$result=mysql_query($query, $dbh);

if ($result>0 && mysql_num_rows($result)>0) {
	echo "Elements à traduire en ";
	$query="select * from t_langue where lang_id=".$_POST["langueEdit"];
	$result2=mysql_query($query, $dbh);
	echo "<b>".mysql_result($result2,0,"lang_nom")."</b>";
	echo ":<br/><select style='width:570px; order:1px solid black' size='10' onchange='nbTime=nbTime-1; document.getElementById(\"iframe\").src=\"dialogue/translate.php?langue=".$_POST["langueEdit"]."&id=\"+this.value'>";
	for ($i=0; $i<mysql_num_rows($result);$i++) {
		echo "<option value='".mysql_result($result,$i,"dico_id")."'>";
		echo str_replace("_"," ",substr (mysql_result($result,$i,"dico_id"),strlen($_POST["name"])+1)) ;
		echo "</option>";
	}
	echo "</select>";
} else {
	echo "Aucun élément à traduire pour ce module";
}

?>
<br/><iframe frameborder="0" marginwidth="0" marginheight="0" id="iframe" name="iframe" style=" width:570px; height:240px" ></iframe>
<div class="bottomButton">
<input type="button" onClick="parent.closePopup();" value="Fermer">
</div>
</body>
</html>
