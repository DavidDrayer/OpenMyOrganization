<?
	$page=0;
	$page=$_GET["page"];
?>
<html>
<head>

<title>Outils</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<style>
a.menuTools {text-decoration:none; display:block; margin-bottom:2px; margin-top:2px; background-color:#7DC0F2; color:#FFFFFF; font-size:11px; font-weight:bold; font-family:Arial, Helvetica, sans-serif; height:25px; padding:5px; padding-top:9px;}
.titreRubrique {margin:2px; font-size:11px; font-weight:bold; font-family:Arial, Helvetica, sans-serif;color:#FFFFFF;border:2px solid #76B6E5; padding:3px;background:url(../images/bkg_points.gif)}
a.menuTools:hover {background-color:#FFFF99; color:#000000;}
.menuTools img {
	float:left;
	vertical-align:middle;
	border:0px;
	margin-right:3px;
	margin-top:-4px;
}
</style>
<script language="javascript" src="palette.js">
</script>
<body style="background-color:#679FC8;">
				<div class="titreRubrique">Site</div>
				<a class="menuTools" href="#" title="G�rer les pages du site (atteindre une page, en ajouter ou en supprimer)."  onClick="popupModal('/onlineEdit/dialogue/popup.php?url=page.php&title=Gestion des pages',500,485)"/><img src="../images/btn_pages.gif">G�rer les pages</a>
				<div class="titreRubrique">Page</div>
				<a class="menuTools" href="#" title="Modifier les propri�t�s de la page."  onClick="popupModal('/onlineEdit/dialogue/popup.php?url=newPage.php?pageId=<?=$page ?>&title=Edition des propri�t�s',500,485)"/><img src="../images/btn_pageProperty.gif">Propri�t�s de la page</a>
				<a class="menuTools" href="#" title="Affiche les statistiques de fr�quentation de la page."  onClick="popupModal('/onlineEdit/dialogue/popup.php?url=viewStat.php?pageId=<?=$page ?>&title=Statistiques de la page',500,533);"/><img src="../images/btn_stat.gif">Statistiques de la page</a>
				<div class="titreRubrique">Ins�rer</div>
				<a class="menuTools" href="#" title="Ins�rer un module de fonction."  onClick="if (checkActive()) {popupModal('/onlineEdit/dialogue/popup.php?url=insertModule.php&title=S�lecteur de module',597,510);}"/><img src="../images/btn_insertImage.gif">Ins�rer un module</a>
				<a class="menuTools" href="#" title="Ins�rer une image ou en t�l�charger une nouvelle."  onClick="if (checkActive()) {popupModal('/onlineEdit/dialogue/popup.php?url=selectImage.php&title=S�lecteur d\'images',597,510);}"/><img src="../images/btn_insertImage.gif">Ins�rer une image</a>
				<a class="menuTools" href="#" title="Ins�re un lien sur une image, un fichier ou une page."  onClick="if (checkActive() &amp;&amp; checkSelection()) {popupModal('/onlineEdit/dialogue/popup.php?url=selectLink.php&title=S�lecteur de lien',500,533);}"/><img src="../images/btn_insertLink.gif">Ins�rer un lien</a>
				<a class="menuTools" href="#" title="Ins�re un tableau."  onClick="if (checkActive()) {insertHtml('&lt;table&gt;&lt;tr&gt;&lt;td&gt;&lt;/td&gt;&lt;/tr&gt;&lt;/table&gt;')}"/><img src="../images/btn_insertTab.gif">Ins�rer un tableau</a>
				<a class="menuTools" href="#" title="Ins�re une barre de s�paation horizontale."  onClick="if (checkActive()) {insertHtml('&lt;hr/&gt;')}"/><img src="../images/btn_insertHR.gif">Ins�rer une s�paration</a>
				<div class="titreRubrique">Supprimer</div>
				<a class="menuTools" href="#" title="Supprime le formattage de la s�lection."  onClick="if (checkActive()) {window.dialogArguments.execCommand('removeFormat');}"/><img src="../images/btn_clearFormating.gif">Supprime le formatage</a>
				<a class="menuTools" href="#" title="Supprime tous les liens de la s�lection."  onClick="window.dialogArguments.execCommand('Unlink')"/><img src="../images/btn_clearLink.gif">Supprime les liens</a>
</body>
</html>
