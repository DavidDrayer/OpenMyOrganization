<?
		//Parcours toutes les pages et les affiche
		include("../db.php");
		// et se connecte à la base de données
		$dbh =  connectDb(); 
?>
<html>
<head>
<title>Nouvelle image</title>
<link href="onglet.css" rel="stylesheet" type="text/css">
</head>
<style>
.option {margin-left:15px; background-color:#DDDDDD; padding:5px;}
</style>
<script>
	function setImageSize(elem) {
	predefinie.style.display="none";
	personnalisee.style.display="none";
		if (elem.value==1) {predefinie.style.display=""};
		if (elem.value==3) {personnalisee.style.display=""};
	}
	function checkValidity(form) {
		// Le nom du fichier est-il rensigné?
		if (form.userfile.value=="") {
			alert("Veuillez choisir un nom de fichier.");
			form.userfile.focus();
			return false;
		}
		// Est-ce un fichier gif ou jpeg?
	
		// Le nouveau nom est-il précisé?
		if ((form.changeName.checked==true) && (form.newName.value=="")) {
			alert("Choisissez le nouveau nom du ficher.");
			form.newName.focus();
			return false;
		}
		
		// La taille de la miniature est-elle précisée?
		if ((form.vignette.checked==true) && (form.tailleVignette.value=="0")) {
			alert("Sélectionnez une taille pour la vignette.");
			form.tailleVignette.focus();
			return false;
		}
		
		// La taille de l'image est-elle précisée?
		if ((form.changeSize.checked==true) && (form.tailleImage.value=="1") && (form.predefini.value=="0")) {
			alert("Sélectionnez une taille pour l'image.");
			form.predefini.focus();
			return false;
		}
		if ((form.changeSize.checked==true) && (form.tailleImage.value=="3") && (isNaN(form.tailleY.value) || (form.tailleY.value=="")) && (isNaN(form.tailleX.value) || (form.tailleX.value=="")) ) {
			alert("Sélectionnez une taille pour l'image.");
			form.tailleX.focus();
			return false;
		}
		form.envoyer.disabled=true;
	}
</script>

<body bgcolor="#CCCCCC">
		<div class="ongletLegende">Charger une nouvelle image.</div>
			<div class="ongletContent">
<form enctype="multipart/form-data" action="uploadImage.php" method="post" onSubmit="return checkValidity(this);">			<div style="height:406px">

 <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
<div class="label">Sélectionnez un fichier sur votre ordinateur:</div> <input name="userfile" type="file" style="width:450px"/><br/>
<div class="label"><input type="checkbox" name="changeSize" value="1" onClick="if (this.checked==true) taille.style.display=''; else taille.style.display='none';" checked> Modifier la taille de l'image</div><div class="option" id="taille"><select name="tailleImage" onChange="setImageSize(this)">
<option value="1">Taille prédéfinie</option>
<option value="3">Taille personnalisée</option>
</select>
<span id="predefinie">
<select name="predefini">
	<option value="0">-- Choisissez le format de l'image --</option>
<?
	$query = "select * from t_tailleimage";
	$result = mysql_query($query, $dbh);
				
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo '<option value="'.mysql_result($result,$i,"taim_id").'">'.mysql_result($result,$i,"taim_nom").'</option>';
		}
	}
	
?>
	
</select>
</span>
<span id="personnalisee" style="display:none">
Horizontale: <input type="text" name="tailleX" size="4"> Verticale: <input type="text" name="tailleY" size="4">
</span>
</div>
<div class="label"><input type="checkbox" name="changeName" value="1" onclick="if (this.checked==true) nouveauNom.style.display=''; else nouveauNom.style.display='none';"> Modifier le nom de l'image</div>
<div class="option" id="nouveauNom" style="display:none">
<input type="text" name="newName">
</div>
<div class="label"><input type="checkbox" name="vignette" value="1" onclick="if (this.checked==true) divTailleVignette.style.display=''; else divTailleVignette.style.display='none';"> Créer une vignette de l'image</div>
<div class="option" id="divTailleVignette" style="display:none">
<select name="tailleVignette">
	<option value="0">-- Choisissez la longeur de la plus longue arrête --</option>
<?
	$query = "select * from t_taillevignette";
	$result = mysql_query($query, $dbh);
				
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo '<option value="'.mysql_result($result,$i,"tavi_taille").'">'.mysql_result($result,$i,"tavi_taille").' pixels</option>';
		}
	}
?>

</select>
</div>
<div class="label">
Important:
</div><div><ul>
  <li>La taille de l'image doit &ecirc;tre inf&eacute;rieure &agrave; 5000 kilobytes.</li>
  <li>L'image doit &ecirc;tre au format GIF ou JPEG.</li>
  <li>Si l'image doit &ecirc;tre affich&eacute;e sur le site, s'assurer qu'elle correspond &agrave; la charte graphique et aux r&egrave;gles de pr&eacute;sentation du site. </li>
  <li>Par &eacute;gard envers les internautes, pensez &agrave; optimiser la taille, les couleurs et le poids d'une image.</li>
</ul>
</div>
</div>
<div align="right"><input type="submit" value="Envoyer" title="Envoyer l'image sur le site Internet"/ name="envoyer"> <input type="button" title="Retourner à la liste des images" value="Annuler" onClick="document.location='selectImage.php'"/> <input title="Fermer ce dialogue" type="button" value="Fermer" onClick="window.close()"/></div>
</form>
</div>
</body>
</html>
