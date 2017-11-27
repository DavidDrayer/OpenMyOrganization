<?
		session_start();
		//Parcours toutes les pages et les affiche
		include("../db.php");
		// et se connecte à la base de données
		$dbh =  connectDb(); 	
?>
<html>
<head>
	<title>Choisissez un lien</title>
	<link href="onglet.css" rel="stylesheet" type="text/css">
	<link href="tree.css" rel="stylesheet" type="text/css">
</head>

<script language="javascript" src="palette.js"></script>
<script language="javascript" src="onglet.js"></script>
<script language="javascript" src="lib/tree.js"></script>

<script language="javascript">

function insertLink (value, target) {
	 target = typeof target !== 'undefined' ? target : "";
	parent.removeLink();
	parent.replaceSelection("<a target='"+target+"' href='"+value+"'>[-selection-]</a>");
	//parent.document.execCommand('CreateLink', false, value);
	parent.closePopup();
}
function setLink (url) {
	urlLocal.value="index.php?page="+url;
	}
	function selectImage (image) {
		fileName.value=image; 
		bigImg.innerHTML="<img src='../../images/uploads/"+image+"' onload='if (this.width&gt;this.height) {this.width=280} else {this.height=280}'><br/>"+image;
	}
	function selectFile (file) {
		fileName2.value=file; 
		bigImg2.innerHTML=file;
	}
</script>
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
<body  onLoad="initTree(myThirdTree)">


    <div class="menuHeader" style='background:#5555A9; border-bottom:1px solid #5555A9; width:100%'> <a href="#" onClick="return afficherOnglet(this,'domaine',1);">Image</a><a href="#" onClick="return afficherOnglet(this,'domaine',2);">Page</a><a href="#" onClick="return afficherOnglet(this,'domaine',3);">Fichier</a><a href="#" onClick="return afficherOnglet(this,'domaine',4);">Autre site</a></div>

	<div id="domaine1" class="ongletContenuVisible">
          <div class="onglet">Image</div>
			<div class="ongletLegende">Créer un lien sur une des images du site.</div>
			<div class="ongletContent">
			<table cellspacing=0 cellpadding=0><tr><td width='150'>
			<? include "lib/imageChooser.php"; ?>
			 </td><td align='center' valign="middle" bgcolor='#EEEEEE' width='400'><div id='bigImg'></div></td></table>
						<div class="bottomButton">
	<input type="button" value="Lier" onclick="if (fileName.value!='') {insertLink('/images/uploads/'+fileName.value)} else {alert ('Veuillez sélectionner l\'image à insérer.')}"> &nbsp; <input type="button" value="Fermer" onclick="parent.closePopup();"><input type="hidden" id="fileName" name="fileName"/>
	</div>	
			</div>			

	</div>
	
	<div id="domaine2" class="ongletContenuCache">
			
          <div class="onglet">Page</div>
			<div class="ongletLegende">Créer un lien sur une des pages locale du site.</div>
			<div class="ongletContent">
			<? 
				$height=400;
				include "lib/pageChooser.php"; 
			?>
			
	<div class="bottomButton"><input Type="hidden" name="urlLocal" id="urlLocal"/> <input type="button" onclick="insertLink(urlLocal.value)" value="Lier"> &nbsp; <input type="button" value="Fermer" onClick="parent.closePopup();"></div>
</div>			

	</div>
	
	<div id="domaine3" class="ongletContenuCache">
          <div class="onglet">Fichier</div>
			<div class="ongletLegende">Créer un lien sur un fichier.</div>
			<div class="ongletContent">
			<table cellspacing=0 cellpadding=0><tr><td width='150'>
			<? include "lib/fileChooser.php"; ?>
			 </td><td align='center' valign="top" bgcolor='#EEEEEE' width='300'><div id='bigImg2'></div></td></table>
						<div class="bottomButton">
<input type="button" value="Nouveau fichier" onclick="document.location='newFile.php'"> &nbsp; 
	
	<input type="button" value="Lier" onclick="if (fileName2.value!='') {insertLink('documents/'+fileName2.value)} else {alert ('Veuillez sélectionner le fichier à lier.')}"> &nbsp; <input type="button" value="Fermer" onclick="parent.closePopup();"><input type="hidden" id="fileName2" name="fileName2"/>
	</div>	
			</div>			

	</div>
	
	<div id="domaine4" class="ongletContenuCache">
          <div class="onglet"></div>
			<div class="ongletLegende">Créer un lien vers un site externe.</div>
			<div class="ongletContent">
		<div style="height:406px">


		<div class="label">Lien sur un site externe:</div> 
	
				<select name="typeLien" id="typeLien">
					<option value="http://">http://</option>
					<option value="ftp://">ftp://</option>
					<option value="mailto:">mailto:</option>
				</select><input type="text" id="fileName3" name="fileName3" style="width:350px;"/>
				<div class="label">Page sur laquelle le lien doit s'ouvrir:</div> 
				<select id="destLien" name="destLien">
					<option value="_self">Même fenêtre</option>
					<option value="_blank">Nouvelle fenêtre</option>
					<option value="toujourslameme">Toujours la même fenêtre externe</option>
				</select>
				</div>
	<div class="bottomButton">		
	<input type="button" value="Lier" onclick="if (fileName3.value!='') {insertLink(typeLien.value+fileName3.value,destLien.value);} else {alert ('Veuillez entrer le lien.')}"> &nbsp; <input type="button" value="Fermer" onclick="parent.closePopup();">
	</div>			
			</div>				
	</div>
</body>
</html>
