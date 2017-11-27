<?
	$page=1;
 	if (isset($_GET["page"])) {
		$page=$_GET["page"];
	} else if (isset ($_SESSION['currentPage'])) {
		$page=$_SESSION['currentPage'];
	}
?>
<html>
<head>
<title>Format de texte</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<script language="javascript" src="palette.js">
</script>
<script language="javascript">
var toolWindow, styleWindow; 
function showTable() {
	document.all("hideTable").style.display="none";
}
function hideTable() {
	document.all("hideTable").style.display="";
}
function showStyle() {
	styleWindow = popupModeless('style.php?page=<?=$page ?>',170,350,window.screenLeft+window.dialogArguments.body.clientWidth-173, window.screenTop+80, window.dialogArguments);
}
function showTools() {
	toolWindow = popupModeless('tools.php?page=<?=$page ?>',160,550,window.screenLeft-3, window.screenTop+80, window.dialogArguments);
}
function openOtherWindows() {
	showStyle();
	showTools();
}

function checkOtherWindows() {
	if (toolWindow.closed) {document.all("hideTools").style.display="none"} else {document.all("hideTools").style.display=""};
	if (styleWindow.closed) {document.all("hideStyle").style.display="none"} else {document.all("hideStyle").style.display=""};
}
</script>
<body style="background:url(../images/logo_lwe.gif) no-repeat left top #679FC8;" onLoad="openOtherWindows(); setInterval('checkOtherWindows()',100)">
<table align="right"><tr><td nowrap="">
				  <input title="Gras" type="image" src="../images/btn_gras.gif" onClick="if (checkActive()) {window.dialogArguments.execCommand('Bold');}"/>
				  <input title="Italique" type="image" src="../images/btn_italique.gif" onClick="if (checkActive()) {window.dialogArguments.execCommand('Italic');}"/>
				  <img src="../images/btn_separation.gif"/>
				   <input title="Aligné à gauche" type="image" src="../images/btn_alignerGauche.gif" onClick="if (checkActive()) {window.dialogArguments.execCommand('justifyLeft');}"/>
				   <input title="Aligné au centre" type="image" src="../images/btn_alignerCentre.gif" onClick="if (checkActive()) {window.dialogArguments.execCommand('justifyCenter');}"/>
				   <input title="Aligné à droite" type="image" src="../images/btn_alignerDroit.gif" onClick="if (checkActive()) {window.dialogArguments.execCommand('justifyRight');}"/>
				   <input title="Justifié" type="image" src="../images/btn_justifier.gif" onClick="if (checkActive()) {window.dialogArguments.execCommand('justifyFull');}"/>
				   <img src="../images/btn_separation.gif"/></td><td nowrap=""><select name="lcm_style" onChange="if (checkActive()) {applyStyle(this.value); this.selectedIndex=0;}">
				     <option value="">-- Choisissez le style --</option>
				     <option value="normal">Normal</option>
				     <option value="&lt;H1&gt;">Titre</option>
				     <option value="&lt;H2&gt;">Sous titre</option>
				     <option value="&lt;H3&gt;">Encadré</option>
				     <option value="&lt;H4&gt;">En évidence</option>
				     <option value="&lt;H5&gt;">Remarque</option>
				   </select>
				  </td><td nowrap=""><input  title="Efface le formattage de la sélection" type="image" src="../images/btn_clearFormating.gif" onClick="if (checkActive()) {window.dialogArguments.execCommand('removeFormat');}"/></td><td><img src="../images/btn_separation.gif"/>
				    <input name="image3" type="image" title="Insérer une liste numérotée" onClick="if (checkActive()) {window.dialogArguments.execCommand('InsertOrderedList');}" src="../images/btn_listeNo.gif"/>
				    <input name="image4" type="image" title="Insérer une liste à puce" onClick="if (checkActive()) {window.dialogArguments.execCommand('InsertUnorderedList');}" src="../images/btn_listePuce.gif"/>
				    <input name="image5" type="image" title="Indenter négativement" onClick="if (checkActive()) {window.dialogArguments.execCommand('Outdent');}" src="../images/btn_indentMoins.gif"/>
				    <input name="image6" type="image" title="Indener positivement" onClick="if (checkActive()) {window.dialogArguments.execCommand('Indent');}" src="../images/btn_indentPlus.gif"/></td>
				<td align="right" nowrap=""><input name="image" type="image" onClick="popupModal('onlineEdit/dialogue/popup.php?url=aide.php?pageId=<?=$page ?>&title=Aide en ligne',700,585)" alt="Manuel utilisateur de l'outils d'édition en ligne" src="../images/btn_aide.gif"/>
			    <input name="image2" type="image" onClick="window.dialogArguments.location='../../index.php?page=<?=$page?>'; " alt="Fermer l'éditeur de contenu" src="../images/btn_fermer.gif"/></td></tr>
  <tr>
    <td nowrap=""><div id="hideTable" style="background-color:#679FC8; filter: alpha(opacity:60) ; width:400px; height:50px; position:absolute; "></div>
	<input name="image7" type="image" title="Supprimer la ligne" onClick="if (checkActive()) {window.dialogArguments.parentWindow.execMyCommand('removeRow');}" src="../images/btn_ligneMoins.gif"/>
    <input name="image8" type="image" title="Ajouter une ligne après" onClick="if (checkActive()) {window.dialogArguments.parentWindow.execMyCommand('addRow')}" src="../images/btn_lignePlus.gif"/>
    <input name="image10" type="image" title="Supprimer la colonne" onClick="if (checkActive()) {window.dialogArguments.parentWindow.execMyCommand('removeColumn');}" src="../images/btn_colMoins.gif"/>
    <input name="image9" type="image" title="Ajouter une colonne après" onClick="if (checkActive()) {window.dialogArguments.parentWindow.execMyCommand('addColumn');}" src="../images/btn_colPlus.gif"/>
<!--    <input name="image11" type="image" title="Fusionner horizontalement" onClick="if (checkActive()) {window.dialogArguments.execCommand('mergeHorizontal');}" src="../images/btn_gras.gif"/>
    <input name="image12" type="image" title="Fusionner verticalement" onClick="if (checkActive()) {window.dialogArguments.execCommand('mergeVertical');}" src="../images/btn_gras.gif"/>
-->  </td>
    <td nowrap="">&nbsp;</td>
    <td nowrap="">&nbsp;</td>
    <td>	<img src="../images/btn_separation.gif"/> <div id="hideTools" style="background-color:#679FC8; filter: alpha(opacity:60) ; width:32px; height:50px; position:absolute; " ></div><input name="image8" type="image" title="Ouvrir la palette d'outils" onClick="if (toolWindow.closed) showTools();" src="../images/btn_windowTools.gif"/> <div id="hideStyle" style="background-color:#679FC8; filter: alpha(opacity:60); width:32px; height:50px; position:absolute; " ></div><input name="image7" type="image" title="Ouvrir la palette de styles" onClick="if (styleWindow.closed) showStyle();" src="../images/btn_windowStyle.gif"/>
    
</td>
    <td align="right" nowrap="">&nbsp;</td>
  </tr>
</table>
</body>
</html>
