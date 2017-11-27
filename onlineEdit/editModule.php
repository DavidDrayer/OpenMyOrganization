<?
	include_once ("db.php");
?>
<html>
<head>
<title>Paramétrage du module</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="dialogue/onglet.css" rel="stylesheet" type="text/css">

</head>
<script language="javascript" src="../onlineEdit/dialogue/palette.js">
</script>

<script>
	function showHide(elem) {
		if ((elem.type=='checkbox' && elem.checked)  || (elem.value=="user_defined")) {
			document.getElementById(elem.id+"_complement").style.display=''
			} else {
			document.getElementById(elem.id+"_complement").style.display='none'
			}
	}
	function validate() {
		if (parent.srcElemBeforPopup!=null) {
			parent.srcElemBeforPopup.src='/onlineEdit/module.php?name=<?=$_GET["name"]?>'+getParamStr();
			
		} else {
			parent.replaceSelection('<IMG class=module src=\'/onlineEdit/module.php?name=<?=$_GET["name"]?>' + getParamStr() + '\' unselectable=\'on\'/>');
		}
		parent.closePopup();
		
		//if (window.dialogArguments.src!=null) {
		//window.dialogArguments.src='/onlineEdit/module.php?name=<?=$_GET["name"]?>'+getParamStr(); window.close();
		//} else {
		//parent.parent.insertHtml('<IMG class=module src=\'/onlineEdit/module.php?name=<?=$_GET["name"]?>' + getParamStr() + '\' unselectable=\'on\'/>'); window.close();	
		//}
	}
</script>
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
<body>
<div class="ongletLegende">Paramètrage du module : <?=$_GET["name"]?>.</div>
<div class="ongletContent">
<div style="height:357px; overflow:auto">

<?

  // inclus l'interface de modification
$toto = @include('../modules/'.$_GET["name"].'Edit.php');

if (!$toto) {

// Pas d'interface de paramétrage, paramétrage par défaut:

// fonction d'interface?
		if (!isset($GLOBALS[$_GET["name"].'INCLUDED'])) {
				@include ('../modules/'. $_GET["name"].'.php');}

if (function_exists($_GET["name"].'_getParams')) {
	eval ( '$listeParam='.$_GET["name"].'_getParams();');
		$javascriptValidation="<script language='javascript'>function getParamStr() {\n"."	return ";

		function insertElem($val) {
			switch ($val["type"]) {
			case "button":
				echo "<input type='button' onclick='' value=''>";
				break;
			case "checkbox":
				$GLOBALS["javascriptValidation"].="'&".$val["nom"]."='+document.formulaire.".$val["nom"].".checked+" ;
				echo "<input onclick='showHide(this)' type='checkbox' id='".$val["nom"]."' name='".$val["nom"]."' ";
				if (isset($_GET[$val["nom"]])&& $_GET[$val["nom"]]=="true") {echo "checked> <span id='".$val["nom"]."_complement'>";}
				else {echo "><span id='".$val["nom"]."_complement' style='display:none'>";} 
				if (isset($val["complement"])) {insertElem($val["complement"]);}
				echo "</span>";
				break;

			case "int":
				echo "<input type='text' id='".$val["nom"]."' name='".$val["nom"]."' value='";
				if (isset($_GET[$val["nom"]])) {echo $_GET[$val["nom"]];};
				echo "'>"; 
				$GLOBALS["javascriptValidation"].="'&".$val["nom"]."='+document.formulaire.".$val["nom"].".value+" ;
				break;
			case "string":
				echo "<input type='text' id='".$val["nom"]."' name='".$val["nom"]."' value=\"";
				if (isset($_GET[$val["nom"]])) {echo htmlentities($_GET[$val["nom"]]);};
				echo "\">"; 
				$GLOBALS["javascriptValidation"].="'&".$val["nom"]."='+escape(document.formulaire.".$val["nom"].".value)+" ;
				break;
			case "enum":
				$GLOBALS["javascriptValidation"].="'&".$val["nom"]."='+document.formulaire.".$val["nom"].".value+" ;


				echo "<select onchange='showHide(this)' id='".$val["nom"]."' name='".$val["nom"]."'>";
				  if (isset($val["src"])) {
				  		// Charge les données en HTML
				  		ini_set('user_agent','Mozilla: (compatible; Windows XP)');

				  	$listeOption = fread(fopen($val["src"], 'rb'), 10000);
						if (isset($_GET[$val["nom"]])) {$listeOption=str_replace("value='".$_GET[$val["nom"]]."'","value='".$_GET[$val["nom"]]."' selected",$listeOption);}
						echo $listeOption;
						
						
				  //	require ($val["src"]);
				  		
				  		
				  } else {
					foreach($val["values"] as $valeur) {
						echo "<option ";
						if (isset($_GET[$val["nom"]]) && $_GET[$val["nom"]]==$valeur["value"]) {echo " selected ";};

						echo "value='".$valeur["value"]."'>".$valeur["label"]."</option>";
					}
					}
				
				
				if (isset($val["other"])) {
				  $display='none';
					echo "<option value='user_defined'";
					if (isset($_GET[$val["nom"]]) && $_GET[$val["nom"]]=='user_defined') {echo " selected" ; $display='';}
					echo ">Autre fichier...</option>";
					echo "</select><span id='".$val["nom"]."_complement' style='display:$display'>"; 
					insertElem($val["other"]);
				} else {
					echo "</select><span id='".$val["nom"]."_complement' style='display:none'>"; 
				}
				echo "</div>";

				break;

		}
		}

		echo "<form  method='post' name='formulaire'><table>";
		foreach($listeParam as $key => $val) {
		echo "<tr><td>".$val["label"]." : </td><td>";
		insertElem($val);
	
		echo "</td></tr>";
	}
	$javascriptValidation.="'';"."}\n"."</script>\n";
	echo "</table></form>";
	echo $javascriptValidation;
	$toto=true;
	// Anciennement boutons
} else 


if (count($_GET)>2) {
echo "<script language='javascript'>\n";
echo "function getParamStr() {\n";
echo "	return ";

foreach ($_GET as $key => $value) {
	if ($key!="page" && $key!="name") {
	   echo "'&".$key."='+escape(document.formulaire.".$key.".value)+" ;
	}


}
echo "'&novar=0';";
echo "}\n";
echo "</script>\n";
echo "<div class='titre'>Paramètre par défaut du module ".$_GET["name"]."</div>";
echo "<form  method='post' name='formulaire'><table>";
$toto=true;
foreach ($_GET as $key => $value) {
	if ($key!="page" && $key!="name") {
	   echo "<tr><td>".$key." : </td><td><input id='".$key."' name='".$key."' value='".$value."' type='text'></td></tr>" ;
	}
	
}
echo "</table></form>";

	// Anciennement boutons

} else {
	echo 'Désolé, ce module ne peut pas être paramétré'; 
	// Anciennement boutons
}
} else {
	// Anciennement boutons
}

?>
</div>
<div class="bottomButton">
<input type="button" onClick="validate();" value="Valider" <?if (!$toto) { echo 'disabled';}?>>
<input type="button" onClick="parent.closePopup();" value="Annuler">
</div>
<table width="100%" style="background-color:#CED7E8"><tr><td><form name="form2" method="POST" action="translateModule.php">Traduire le module en : <input type="hidden" name="name" value="<?=$_GET["name"]?>">
<?
		echo "<select name='langueEdit' style='width:150px;'>";
		// Affichage de la liste des langues
		$query="select * from t_langue order by lang_nom";
		$result2=mysql_query($query, $GLOBALS["dbh"]);
		if ($result2>0 && mysql_num_rows($result2)>0) {
		for ($i=0;$i< mysql_num_rows($result2);$i++) {
			echo "<option value='".mysql_result($result2,$i,"lang_id")."'";
			if (!isset($_POST["langueEdit"])) $_POST["langueEdit"]=$_SESSION["langue"];
			if (mysql_result($result2,$i,"lang_id")==@$_POST["langueEdit"]) { echo " selected";}
			echo ">".mysql_result($result2,$i,"lang_nom")."</option>";
		}
		
	}			
		echo "</select>"; 
	?>
	<input type="submit" value="Go"></form></td></tr></table>

</div>
</body>
</html>
