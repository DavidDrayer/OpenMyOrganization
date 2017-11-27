<?
if (isset($_POST["btn_del"])) {
	// Supprime l'image
   	unlink("../../images/uploads/".$_POST["fileName"]); 
}

?>
<html>
<head>
<title>Choisissez une image</title>
<link href="onglet.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="javascript" src="palette.js">
</script>
<script language="javascript">
	function selectImage (image) {
		document.getElementById('fileName').value=image; 
		bigImg.innerHTML="<img src='../../images/uploads/"+image+"' onload='if (this.width&gt;this.height) {this.width=380} else {this.height=380}'>"
	}
		

</script>
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
<body>
		<div class="ongletLegende">Choisissez une image.</div>
			<div class="ongletContent">
<form method="post">
<?


echo "<table><tr><td width='150'>";

include "lib/imageChooser.php";
 
 echo "</td><td align='center' bgcolor='#EEEEEE' width='400'><div id='bigImg'></div></td></table>";
	
?>
<div class="bottomButton">
<input type="button" value="Nouvelle image..." onclick="document.location='newImage.php'"> &nbsp; 
<input type="submit" name="btn_del" value="Supprimer" onclick="if (document.getElementById('fileName').value=='') {alert ('Veuillez sélectionner une image.'); return false;}"> &nbsp; 
	
	<input type="button" value="Insérer" onclick="if (document.getElementById('fileName').value!='') {parent.replaceSelection('&lt;img src=\'/images/uploads/'+ document.getElementById('fileName').value + '\'/&gt;'); parent.closePopup();} else {alert ('Veuillez sélectionner l\'image à insérer.')}"> &nbsp; <input type="button" value="Fermer" onclick="parent.closePopup();"><input type="hidden" id="fileName" name="fileName"/>
	</div></form>
	</div>
</body>
</html>
