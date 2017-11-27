<html>
<head>
<title>Document sans nom</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../dialogue/onglet.css" rel="stylesheet" type="text/css">
</head>

<body style="margin:0px; padding:0px">
<?
		include ("../db.php");
	if (isset($_GET["dico_txt"])) {

		$query="update tm_dico set dico_txt='".addslashes(stripslashes($_GET["dico_txt"]))."' where dico_id='".addslashes(stripslashes($_GET["id"]))."' and lang_id=".$_GET["langue"];
	
		mysql_query($query,$dbh);
	}


		$query="Select * from tm_dico where dico_id='".addslashes(stripslashes($_GET["id"]))."' and lang_id=".$_GET["langue"];
		$result=mysql_query($query, $dbh);
		if ($result>0) {
			echo "<form method='GET'>Traduction pour <b>".str_replace("_"," ",substr ($_GET["id"],strpos($_GET["id"],"_")+1))."</b> <span style='display:none' id='modif'>(modifications non enregistées)</span><br/>" ;
			echo "<input type='hidden' name='id' value='".$_GET["id"]."'><input type='hidden' name='langue' value='".$_GET["langue"]."'><textarea name='dico_txt' style='width:570px; height:160px' onKeyDown='document.getElementById(\"modif\").style.display=\"\"'>";
			if (mysql_num_rows($result)>0) echo mysql_result($result, 0,"dico_txt");
			echo "</textarea><input type='submit' value='Enregistrer'></form>";
		} else {
			echo "Problème de sélection";
		}

?>
</body>
</html>
