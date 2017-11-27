<?
		//Parcours toutes les pages et les affiche
		include("../db.php");
		// et se connecte à la base de données
		$dbh =  connectDb(); 	
		$pageId = $_GET["pageId"];	
?>
<html>
<head>
<title>Statistiques d'une page</title>
<link href="onglet.css" rel="stylesheet" type="text/css">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<style>
h2 {padding:0px; font-size:120%; margin-top:10px; margin-bottom:5px;}
td {background-color:#CCCCCC;}
</style>
<style>
	body {padding:0px; margin:0px;}
	.bottomButton {text-align:right;  background:#CED7E8;  padding:5px; margin-bottom:2px;}
</style>
<body>
		<div class="ongletLegende">Statistiques de la page.</div>
			<div class="ongletContent">
 <div style="height:385px; overflow:auto"> 
 <?  
 	// nombre de visualisations de la page
	$query = "select count(*) from t_statistique where page_id=".$pageId;
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) $viewPage=mysql_result($result,0,0);

	// nombre total de visualisations
	$query = "select count(*) from t_statistique";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) $totalViewPage=mysql_result($result,0,0);
	
	// nombre total de pages
	$query = "select count(*) from t_page where page_active=1";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) $totalPage=mysql_result($result,0,0);
	
	 $query = "select distinct stat_ip from t_statistique where page_id=".$pageId;
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) $numVisiteur= mysql_num_rows($result);
	
	$query = "select distinct stat_ip from t_statistique";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) $totalNumVisiteur= mysql_num_rows($result);

	echo '<h2>Statistiques globale pour le site:</h2>';
	echo "<div>".$totalViewPage." visualisations de pages</div>";
	echo "<div>".$totalPage." pages au total</div>";
	echo "<div>".$totalNumVisiteur." visiteurs au total</div>";

	echo '<h2>Statistiques de visite pour la page</h2>';
	echo "<div>".$viewPage." visualisations (".(number_format($viewPage/$totalViewPage*100,1))."%)</div>";
	echo $numVisiteur." visiteurs (".(number_format($numVisiteur/$totalNumVisiteur*100,1))."%)";
 ?>
 
   <h2>On vient sur cette page depuis</h2>
 <?  
	$query = "select stat_reference, count(*) as cpt from t_statistique where page_id=".$pageId." group by stat_reference order by cpt desc";
	$result = mysql_query($query, $dbh);
				
	if ($result>0 && mysql_num_rows($result)>0) {
		echo "<table >";			

		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo "<tr><td><div style='width:410px; overflow:hidden;'>";
			
			// si c'est une référence locale, affiche le nom local
			echo "<a href='".mysql_result($result,$i,0)."' target='_blank'>".mysql_result($result,$i,0)."</a>";
			
			echo "</div></td><td>".mysql_result($result,$i,1)."</td></tr>";
		}
		echo "</table>";		
	}
 ?></p>
 
    <p>Et on repart sur:
 <?  
 	if ($pageId==1) {
		$query = "select count(t_statistique.page_id) as cpt,  t_statistique.page_id from t_statistique where stat_reference like '%page=".$pageId."' or stat_reference='http://".$_SERVER['HTTP_HOST']."/' group by t_statistique.page_id order by cpt desc";
	} else {
		$query = "select count(t_statistique.page_id) as cpt,  t_statistique.page_id from t_statistique where stat_reference like '%page=".$pageId."' group by t_statistique.page_id order by cpt desc";
	}
	$result = mysql_query($query, $dbh);
	echo "<table>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($i=0; $i<mysql_num_rows($result); $i++) {
		$result2=getPageInfo(mysql_result($result,$i,"page_id"));
			if ($result2>0 && mysql_num_rows($result2)>0) {
			echo "<tr><td><div style='width:410px; overflow:hidden;'><a href='viewStat.php?pageId=".mysql_result($result,$i,"page_id")."'>(".mysql_result($result2,0,"page_id").") ".mysql_result($result2,0,"page_nom")."</a></div></td><td>".mysql_result($result,$i,"cpt")."x</td></tr>";
			} else {
			echo "<tr><td><div style='width:410px; overflow:hidden;'>Page supprimée (".mysql_result($result,$i,"page_id").")</div></td><td>".mysql_result($result,$i,"cpt")."x</td></tr>";
			}
		}
	}
	echo "</table>";			

 ?></p>
 
     <p><strong>Accès par heure:</strong>
 <?  
	$query = "select HOUR(stat_heure) as jour, count(t_statistique.page_id) as cpt from t_statistique where page_id=".$pageId." and stat_heure<>'' group by jour order by jour";
	$result = mysql_query($query, $dbh);
	echo "<table><tr><td valign='bottom' nowrap>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($cpt=$max=0;$cpt<mysql_num_rows($result); $cpt++) {if ($max<mysql_result($result,$cpt,1)) $max=mysql_result($result,$cpt,1);}
		for ($i=0; $i<24; $i++) {
			for ($cpt=0;$cpt<mysql_num_rows($result) && mysql_result($result,$cpt,0)!=$i; $cpt++);
			if ($cpt<mysql_num_rows($result)) {
				echo "<img border=1 src='images/pxRed.gif' width='16' height='".(60/$max*mysql_result($result,$cpt,1))."' alt='".mysql_result($result,$cpt,1)." visites'/>";
			} else {
				echo "<img border=0 src='images/px.gif' width='18' height='0'/>";
			}
		}
	}
	echo "</td></tr></table>";			
	echo "<table width='437'><tr><td align='center' width='4%'>1</td><td align='center' width='4%'>2</td><td align='center' width='4%'>3</td><td align='center' width='4%'>4</td><td align='center' width='4%'>5</td><td align='center' width='4%'>6</td><td align='center' width='4%'>7</td><td align='center' width='4%'>8</td><td align='center' width='4%'>9</td><td align='center' width='4%'>10</td><td align='center' width='4%'>11</td><td align='center' width='4%'>12</td><td align='center' width='4%'>13</td><td align='center' width='4%'>14</td><td align='center' width='4%'>15</td><td align='center' width='4%'>16</td><td align='center' width='4%'>17</td><td align='center' width='4%'>18</td><td align='center' width='4%'>19</td><td align='center' width='4%'>20</td><td align='center' width='4%'>21</td><td align='center' width='4%'>22</td><td align='center' width='4%'>23</td><td align='center' width='4%'>24</td></tr></table>";

 ?></p>
      <p><strong>Accès par jour:</strong>
 <?  
	$query = "select DAYOFWEEK(stat_heure) as jour, count(t_statistique.page_id) as cpt from t_statistique where page_id=".$pageId." and stat_heure<>'' group by jour order by jour";
	$result = mysql_query($query, $dbh);
	echo "<table><tr><td valign='bottom' nowrap>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($cpt=$max=0;$cpt<mysql_num_rows($result); $cpt++) {if ($max<mysql_result($result,$cpt,1)) $max=mysql_result($result,$cpt,1);}
		for ($i=1; $i<8; $i++) {
			for ($cpt=0;$cpt<mysql_num_rows($result) && mysql_result($result,$cpt,0)!=$i; $cpt++);
			if ($cpt<mysql_num_rows($result)) {
				echo "<img border=1 src='images/pxRed.gif' width='60' height='".(60/$max*mysql_result($result,$cpt,1))."' alt='".mysql_result($result,$cpt,1)." visites'/>";
			} else {
				echo "<img border=0 src='images/px.gif' width='62' height='0'/>";
			}
		}
	}
	echo "</td></tr></table>";			
	echo "<table width='439'><tr><td align='center' width='14%'>Dimanche</td><td align='center' width='14%'>Lundi</td><td align='center' width='14%'>Mardi</td><td align='center' width='14%'>Mercredi</td><td align='center' width='14%'>Jeudi</td><td align='center' width='14%'>Vendredi</td><td align='center' width='14%'>Samedi</td></tr></table>";

 ?></p>
 
      <p><strong>Accès les 30 derniers jours:</strong>
 <?  
		$query = "select DATE_FORMAT(stat_heure,'%Y%m%d') as jour, count(t_statistique.page_id) as cpt from t_statistique where page_id=".$pageId." and stat_heure<>'' and stat_heure>DATE_SUB(CURDATE(),INTERVAL 30 DAY) and stat_heure<now() group by jour order by jour";
	$result = mysql_query($query, $dbh);
	echo "<table border='0'><tr><td>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($cpt=$max=0;$cpt<mysql_num_rows($result); $cpt++) {if ($max<mysql_result($result,$cpt,1)) $max=mysql_result($result,$cpt,1);}
		for ($i=30; $i>=0; $i--) {
			for ($cpt=0;$cpt<mysql_num_rows($result) && mysql_result($result,$cpt,0)!=date("Ymd",time()-$i*60*60*24); $cpt++);
			if ($cpt<mysql_num_rows($result)) {
				echo "<img border=1 src='images/pxRed.gif' width='12' height='".(60/$max*mysql_result($result,$cpt,1))."' alt='".mysql_result($result,$cpt,1)." visites'/>";
			} else {
				echo "<img border=0 src='images/px.gif' width='14' height='0'/>";
			}
		}	
	}
	echo "</td></tr></table>";			
	echo "<table width='440'><tr><td align='left'>".date("d.m.Y",time()-30*60*60*24)."</td><td align='center'>".date("d.m.Y",time()-15*60*60*24)."</td><td align='right'>".date("d.m.Y",time())."</td></tr></table>";

 ?></p>
</div>
<? echo '<div class="bottomButton"><input type="button" value="Fermer" name="fermer" onClick="parent.closePopup();"/></div>';?></div>
</body>
</html>
