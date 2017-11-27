<?
		session_start();
		//Parcours toutes les pages et les affiche
		include("../db.php");
		// et se connecte à la base de données
		$dbh =  connectDb(); 	
		
		@$nom=$_POST["nom"];
		@$shortcut=$_POST["shortcut"];
		@$titre=$_POST["titre"];
		@$description=$_POST["description"];
		@$motCle=$_POST["motCle"];
		@$urlLocal=$_POST["urlLocal"];
		@$gabarit=$_POST["gabarit"];
		@$style=$_POST["style"];
		@$actif=$_POST["actif"];
		@$priority=$_POST["priority"];
		@$cache=$_POST["cache"];
		@$menu=$_POST["menu"];
		@$page=$_POST["page"];
		// modification d'une page
		if (isset($_GET["pageId"])) {
			// récupération des infos de la base de données
			$query = "select * from t_page, t_pageinfo where t_page.page_id=t_pageinfo.page_id and t_pageinfo.lang_id=".$_SESSION["langue"]." and t_page.page_id=".$_GET["pageId"];
			$result = mysql_query($query, $dbh);
			
			if ($result>0 && mysql_num_rows($result)==0) {
				$query=str_replace("and t_pageinfo.lang_id=".$_SESSION["langue"],"",$query);
				$result = mysql_query($query, $GLOBALS['dbh']);
			}

			if ($result>0) {
				$nom = mysql_result($result,0,"page_nom");
				$shortcut = mysql_result($result,0,"page_shortcut");
				$titre= mysql_result($result,0,"page_titre");
				$description= mysql_result($result,0,"page_description");
				$motCle= mysql_result($result,0,"page_motCle");
				$urlLocal= mysql_result($result,0,"page_parent");
				$gabarit= mysql_result($result,0,"gaba_id");
				$style= mysql_result($result,0,"styl_id");
				$actif = mysql_result($result,0,"page_active");
				$priority = mysql_result($result,0,"page_priority");
				$cache = mysql_result($result,0,"page_cache");
				$menu = mysql_result($result,0,"page_visible");
				$page=$_GET["pageId"];
			}
		}
		
		// Création de la page
		if (isset($_POST["terminer"])) {
			$etape=$_POST["etape"];
			if ($actif!=1) $actif=0;
			if ($menu!=1) $menu=0;
			if ($page!='') {
				$query = "update t_page set gaba_id=$gabarit, styl_id=$style , page_active=$actif, page_visible=$menu, page_priority=$priority, page_cache=$cache, page_shortcut='".addslashes(stripslashes($shortcut))."' where page_id=$page";
				$result = mysql_query($query, $dbh);
				if ($result>0) {
					$etape+=2;
				}

			} else {
				$query = "insert into t_page (page_parent, gaba_id, page_active, styl_id, page_visible, page_priority, page_cache, page_shortcut) VALUES ($urlLocal,$gabarit,$actif,$style, $menu, $priority, $cache,'".addslashes(stripslashes($shortcut))."')";
				$result = mysql_query($query, $dbh);
				if ($result>0) {
					$page=mysql_insert_id($dbh);
					$etape+=1;
				}
			}
			// Supprime les anciennes informations de page, et insère les nouvelles
			$query= "delete from t_pageinfo where page_id=$page and lang_id=".$_SESSION["langue"];
			$result = mysql_query($query, $dbh);
			$query= "insert into t_pageinfo (page_id, lang_id, page_nom, page_titre, page_description, page_motCle) values ($page, ".$_SESSION["langue"].",'".addslashes(stripslashes($nom))."','".addslashes(stripslashes($titre))."','".addslashes(stripslashes($description))."','".addslashes(stripslashes($motCle))."')";
			$result = mysql_query($query, $dbh);
		}
?>
<html>
<head>
<title>Document sans nom</title>
<link href="onglet.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
th {
	background-color: #CCCCCC;
	text-align: left;
	vertical-align: top;
}
-->
</style>
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
</head>
<script language="javascript" src="palette.js"></script>

<body>
<div class="ongletLegende"><?

	if (!isset($_POST["etape"])) {
		$etape=1;
	} else {
		if (isset($_POST["precedent"])) {$etape=$_POST["etape"]-1;}
		if (isset($_POST["suivant"])) {$etape=$_POST["etape"]+1;}
	}
		
	if ($etape>6) {
		echo "Confirmation - opération effectuée";
	} else
	if (!isset($page)) {
		echo "Création d'une nouvelle page";
	} else {
		echo "Modification des propriétés";
	}

	
	echo " - Etape ".$etape." sur 5";
	
?></div><form name="formulaire" method="post" action="newPage.php">
<input type="hidden" name="urlLocal" value="<? echo $urlLocal; ?>">
<input type="hidden" name="etape" value="<? echo $etape; ?>">
<input type="hidden" name="page" value="<? echo $page; ?>">
<div class="ongletContent"><div style="height:385px; overflow:auto">
<? 
	// ************************************************************************************************
	// Informations générales
	if ($etape==1) {
?>
<div class="titre">Informations générales</div>
<div class="label">Nom abrégé de la page:</div>
<input name="nom" type="text" style="width:200px" value="<? echo stripslashes($nom); ?>">
<div class="label">Raccourci:</div>
<input name="shortcut" type="text" style="width:200px" value="<? echo stripslashes($shortcut); ?>">
<div class="label">Titre de la page:</div>
<input name="titre" type="text" style="width:460px" value="<? echo stripslashes($titre); ?>">
<div class="label">Description de la page:</div>
<textarea name="description" cols="" rows="5"  style="width:460px"><? echo stripslashes($description); ?></textarea>
<div class="label">Enumération des mots clés:</div>
<textarea name="motCle" cols="" rows="3"  style="width:460px"><? echo stripslashes($motCle); ?></textarea>
<? } else {?>
	<input name="nom" type="hidden" value="<? echo stripslashes($nom); ?>">
	<input name="shortcut" type="hidden" value="<? echo stripslashes($shortcut); ?>">
	<input name="titre" type="hidden" value="<? echo stripslashes($titre); ?>">
	<input name="description" type="hidden"  value="<? echo stripslashes($description); ?>">
	<input name="motCle" type="hidden"  value="<? echo stripslashes($motCle); ?>">	
<? 	}
	// ************************************************************************************************
	// Gabarit graphique 
	if ($etape==2) {
?>
<div class="titre">Mise en page générale</div>

<? 
	// Lecture des différents gabarits:
	$query = "select * from t_gabarit where gaba_public=1";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) {
				
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo '<table><tr><td><input id="gaba_'.mysql_result($result,$i,"gaba_id").'" name="gabarit" type="radio" value="'.mysql_result($result,$i,"gaba_id").'"';
			if ($gabarit==mysql_result($result,$i,"gaba_id") || ($gabarit=="" && $i==0)) {echo "checked";}
			echo "></td><td align='center'><label for='gaba_".mysql_result($result,$i,"gaba_id")."'>";
			if (mysql_result($result,$i,"gaba_imagePetit")!='') {
				echo "<img onclick='gaba_".mysql_result($result,$i,"gaba_id").".click()' src='../images/gabarits/".mysql_result($result,$i,"gaba_imagePetit")."' border='1'><br/>";
			}
			echo mysql_result($result,$i,"gaba_nom")."</label></td></tr></table>";
			
		}
	}

} else {?>	
	<input name="gabarit" type="hidden" value="<? echo stripslashes($gabarit); ?>">

<? 
}
	// Feuille de style
	if ($etape==3) {
?>
<div class="titre">Couleurs et styles</div>
<?
	// Lecture des différents styles:
	$query = "select t_style.* from t_style, t_styl_gaba where t_style.styl_id=t_styl_gaba.styl_id and t_styl_gaba.gaba_id=$gabarit";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) {
				
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo '<table><tr><td><input id="styl_'.mysql_result($result,$i,"styl_id").'" name="style" type="radio" value="'.mysql_result($result,$i,"styl_id").'"';
			if ($style==mysql_result($result,$i,"styl_id") || ($style=="" && $i==0)) {echo "checked";}
			echo "></td><td align='center'><label for='styl_".mysql_result($result,$i,"styl_id")."'>";
			if (mysql_result($result,$i,"styl_imagePetit")!='') {
				echo "<img onclick='styl_".mysql_result($result,$i,"styl_id").".click()' src='../images/gabarits/".mysql_result($result,$i,"styl_imagePetit")."' border='1'><br/>";
			}
			echo mysql_result($result,$i,"styl_nom")."</label></td></tr></table>";
			
		}
	} else {
		echo "Aucun style à définir pour ce gabarit de page.";
	}
?>
<? } else {?>	
	<input name="style" type="hidden" value="<? echo stripslashes($style); ?>">


<? 
}
	// Paramètres
	if ($etape==4) {
?>
<div class="titre">Paramètres de la page</div>
<input name="actif" id="actif" type="checkbox" value="1" <? if ($actif==1) {echo 'checked';}?> ><label for="actif">Page active</label><br/>
<input name="menu" id="menu" type="checkbox" value="1" <? if ($menu==1) {echo 'checked';}?> ><label for="actif">Page visible dans les menus</label><br/>
Priorité de page : <select name="priority">
<?
for ($i=0; $i<=100; $i++) {
	echo "<option";
	if ($priority==$i) echo " selected";
	echo ">".$i."</option>";
} 
?>
</select>
<br>Mise en cache : <select name="cache">
<?
for ($i=0; $i<=9; $i++) {
	echo "<option";
	if ($cache==$i) echo " selected";
	echo " value='".($i)."'>".($i)." minute(s)</option>";
} 
for ($i=1; $i<=12; $i++) {
	echo "<option";
	if ($cache==$i*10) echo " selected";
	echo " value='".($i*10)."'>".($i*10)." minute(s)</option>";
} 
?>
</select>

<? } else {?>	
	<input name="actif" type="hidden" value="<? echo stripslashes($actif); ?>">
	<input name="menu" type="hidden" value="<? echo stripslashes($menu); ?>">
	<input name="priority" type="hidden" value="<? echo stripslashes($priority); ?>">
	<input name="cache" type="hidden" value="<? echo stripslashes($cache); ?>">
<? 
	}
	// Confirmation
	if ($etape==5) {
?>
<div class="titre">Confirmation de création.</div>
Avant d'appuyer sur le bouton "Terminer", veuillez contrôler que l'ensemble des informations suivantes est correct:
<table border="0" cellspacing="5" cellpadding="0">
  <tr>
    <th nowrap>Nom de la page </th>
    <td><? echo stripslashes($nom) ?></td>
  </tr>
  <tr>
    <th nowrap>Raccourci </th>
    <td><? echo stripslashes($shortcut) ?></td>
  </tr>
  <tr>
    <th nowrap>Titre de la page </th>
    <td><? echo stripslashes($titre) ?></td>
  </tr>
  <tr>
    <th nowrap>Description</th>
    <td><? echo stripslashes($description) ?></td>
  </tr>
  <tr>
    <th nowrap>Mots-cl&eacute;s</th>
    <td><? echo stripslashes($motCle) ?></td>
  </tr>
  <tr>
    <th nowrap>Gabarit</th>
    <td><? 
	
		$query = "select * from t_gabarit where gaba_id=$gabarit";
		$result = mysql_query($query, $dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			if (mysql_result($result,0,"gaba_imagePetit")!='') {
				echo "<img src='../images/gabarits/".mysql_result($result,0,"gaba_imagePetit")."' border='1'>";
			} else {
				echo mysql_result($result,0,"gaba_nom");
			}
		}

	 ?></td>
  </tr>
  <tr>
    <th nowrap>Couleurs et styles </th>
    <td><? 
	
		$query = "select * from t_style where styl_id=$style";
		$result = mysql_query($query, $dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			if (mysql_result($result,0,"styl_imagePetit")!='') {
				echo "<img src='../images/gabarits/".mysql_result($result,0,"styl_imagePetit")."' border='1'>";
			} else {
				echo mysql_result($result,0,"styl_nom");
			}
		} else {
			echo "Aucun style défini pour cette page";
		}

	 ?></td>
  </tr>
  <tr>
    <th nowrap>Page parente </th>
    <td><? echo $urlLocal; ?></td>
  </tr>
  <tr>
    <th nowrap>Page active</th>
    <td><? if ($actif==1) {echo 'oui';} else {echo 'non';}?></td>
  </tr>
  <tr>
    <th nowrap>Param&egrave;tres</th>
    <td>aucun param&egrave;tre de d&eacute;fini </td>
  </tr>
 
</table>
 
<? } 
if ($etape==6) {
?>
	<div class="titre">La page a été créée avec succès.</div>
	Choisissez l'opération à effectuer:<br/>
	<a href="" onclick="parent.location='../../index.php?page=<?=$page?>'; parent.closePopup();">> Afficher la page créée en mode normal.</a><br/>
	<a href="" onclick="parent.location='../../index.php?page=<?=$page?>&edit=true'; parent.closePopup();">> Afficher la page créée en mode édition.</a><br/>
	<a href="" onclick="document.location='page.php'; return false;">> Retourner sur la liste des pages.</a><br/>
	<a href="" onclick="parent.closePopup();">> Fermer cette fenêtre.</a>
<?
}
if ($etape==7) {
?>
	<div class="titre">Les propriétés ont été modifiées avec succès.</div>
	Afin de garantir un affichage reflétant les modifications effectuées, il est conseillé d'actualiser l'affichage de la page.<br/>
	Choisissez une opération à effectuer:<br/>
	<a href="" onclick="parent.location='../../index.php?page=<?=$page?>&edit=true'; parent.closePopup();">> Actualiser la page et l'éditer.</a><br/>
	<a href="" onclick="parent.closePopup();">> Fermer cette fenêtre sans actualiser.</a>
<?
}
?>		</div>	
<div class="bottomButton">
<? if ($etape>1 && $etape<=5) { echo " <input type='submit' value='Précédent' name='precedent'/>"; } ?>			
<? if ($etape==5) { echo " <input type='submit' value='Terminer' name='terminer'/>"; } ?>			
<? if ($etape<5) { echo " <input type='submit' value='Suivant' name='suivant'/>"; } ?>
<? if ($etape<=5 && $page=='') {echo '<input type="button" value="Fermer" name="fermer" onClick="if (confirm(\'Etes-vous certain de vouloir abandonner la création de la page?\')) {parent.closePopup();}"/>';}			 ?>
<? if ($etape<=5 && $page!='') {echo '<input type="button" value="Fermer" name="fermer" onClick="if (confirm(\'Etes-vous certain de vouloir abandonner la modification des propriétés?\')) {parent.closePopup();}"/>';}		?>
<? if ($etape>5 ) {echo '<input type="button" value="Fermer" name="fermer" onClick="parent.closePopup();"/>';}			?>
	</div>
</div>
</form>
</body>
</html>
