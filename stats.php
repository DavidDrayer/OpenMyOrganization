<?
		session_start();
		include_once ($_SERVER["DOCUMENT_ROOT"]."/onlineEdit/db.php");
		// et se connecte à la base de données
		$dbh =  connectDb(); 	
		

?>
<html>
<head>
<title>Statistiques générales</title>
<link href="onlineEdit/dialogue/onglet.css" rel="stylesheet" type="text/css">

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<style>
h2 {padding:0px; font-size:120%; margin-top:10px; margin-bottom:5px;}
td {background-color:#CCCCCC;}
</style>
<script>
		function popupWindow (url,tailleX,tailleY) {
					window.open(url, window.dialogArguments,'height=' + tailleY + '; width=' + tailleX + '');
			}
</script>
<body>
<body bgcolor="#CCCCCC">
		<div class="ongletLegende">Statistiques globale du site pour les 90 derniers jours.</div>
			<div class="ongletContent">
 <?  
	// nombre total de visualisations
	$query = "select count(*) from t_statistique";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) $totalViewPage=mysql_result($result,0,0);
	
	// nombre total de pages
	$query = "select count(*) from t_page where page_active=1";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) $totalPage=mysql_result($result,0,0);
	
	$query = "select distinct stat_ip,DATE_FORMAT(stat_heure,'%Y%m%d') from t_statistique";
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) $totalNumVisiteur= mysql_num_rows($result);
	

	echo '<h2>Statistiques globale pour le site:</h2>';
	echo "<div>".$totalViewPage." visualisations de pages</div>";
	echo "<div>".$totalPage." pages au total</div>";
	echo "<div>".$totalNumVisiteur." visiteurs au total</div>";

	// Super informations statistiques
	$query="SELECT to_days( max( stat_heure ) ) - to_days( min( stat_heure )) AS nbJour, MAX( stat_heure ) AS max1, min( stat_heure ) AS max2, COUNT( page_id ) AS nbPage, COUNT( DISTINCT stat_ip, DATE_FORMAT( stat_heure, '%Y%m%d' ) ) AS nbVisite FROM t_statistique ";
	$result=mysql_query($query, $dbh);
	$query="SELECT to_days( max( stat_heure ) ) - to_days( min( stat_heure )) AS nbJour, MAX( stat_heure ) AS max1, min( stat_heure ) AS max2, COUNT( page_id ) AS nbPage, COUNT( DISTINCT stat_ip, DATE_FORMAT( stat_heure, '%Y%m%d' ) ) AS nbVisite FROM t_statistique  WHERE stat_heure < DATE_SUB( NOW( ) , INTERVAL 7 DAY ) and stat_heure >= DATE_SUB( NOW( ) , INTERVAL 14 DAY ) ";
	$result2=mysql_query($query, $dbh);
	$query="SELECT to_days( max( stat_heure ) ) - to_days( min( stat_heure )) AS nbJour, MAX( stat_heure ) AS max1, min( stat_heure ) AS max2, COUNT( page_id ) AS nbPage, COUNT( DISTINCT stat_ip, DATE_FORMAT( stat_heure, '%Y%m%d' ) ) AS nbVisite FROM t_statistique  WHERE stat_heure >= DATE_SUB( NOW( ) , INTERVAL 7 DAY )";
	$result3=mysql_query($query, $dbh);
	echo "<div>Nombre de jour du site: ".mysql_result($result2,0,"nbJour")."</div>";
	echo "<table><tr><th></th><th>Global</th><th>La semaine passée</th><th>Cette semaine</th><th>Progression</th></tr>";
	echo "<tr><td>Nombre de visiteurs</td><td>".(mysql_result($result,0,"nbVisite"))."</td><td>".(mysql_result($result2,0,"nbVisite"))."</td><td>".(mysql_result($result3,0,"nbVisite"))."</td></tr>";
	echo "<tr><td>Moyenne de visiteurs</td><td>".(mysql_result($result,0,"nbVisite")/(mysql_result($result,0,"nbJour")))."</td><td>".(mysql_result($result2,0,"nbVisite")/(mysql_result($result2,0,"nbJour")))."</td><td>".(mysql_result($result3,0,"nbVisite")/(mysql_result($result3,0,"nbJour")))."</td></tr>";
	echo "<tr><td>Nombre de pages vues</td><td>".(mysql_result($result,0,"nbPage"))."</td><td>".(mysql_result($result2,0,"nbPage"))."</td><td>".(mysql_result($result3,0,"nbPage"))."</td></tr>";
	echo "<tr><td>Moyenne de pages vues</td><td>".(mysql_result($result,0,"nbPage")/(mysql_result($result,0,"nbJour")))."</td><td>".(mysql_result($result2,0,"nbPage")/(mysql_result($result2,0,"nbJour")))."</td><td>".(mysql_result($result3,0,"nbPage")/(mysql_result($result3,0,"nbJour")))."</td></tr>";
	echo "<tr><td>Moyenne de pages par visiteur</td><td>".(mysql_result($result,0,"nbPage")/(mysql_result($result,0,"nbVisite")))."</td><td>".(mysql_result($result2,0,"nbPage")/(mysql_result($result2,0,"nbVisite")))."</td><td>".(mysql_result($result3,0,"nbPage")/(mysql_result($result3,0,"nbVisite")))."</td></tr>";
	echo "</table>";
 ?>
    <h2>Les pages du site le plus visité</h2>
 <div style="background-color:#EEEEEE; width:560px; height:200px; overflow:auto">
 <?  
	$query = "select page_id, count(*) AS total, sum( stat_heure < DATE_SUB( NOW( ) , INTERVAL 7 DAY ) AND stat_heure >= DATE_SUB( NOW( ) , INTERVAL 14 DAY ) ) AS avant, sum( stat_heure >= DATE_SUB( NOW( ) , INTERVAL 7 DAY ) ) AS now  from t_statistique group by page_id order by now desc, avant desc, total desc";
	$result = mysql_query($query, $dbh);
				
	if ($result>0 && mysql_num_rows($result)>0) {
		echo "<table >";			
		echo "<tr><th>Page du site</th><th>7 jours</th><th>14 jours</th><th>Total</th></tr>";

		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo "<tr><td><div style='width:380px; overflow:hidden;'>";
					$result2=getPageInfo(mysql_result($result,$i,0));
					if (mysql_num_rows($result2)==0) {
						echo "Page supprimée (".mysql_result($result,$i,0).")";
					} else {
					echo "<a href=\"javascript:popupWindow('onlineEdit/dialogue/viewStat.php?pageId=".mysql_result($result,$i,0)."',500,457)\" >".mysql_result($result2,0,"page_nom")."</a>";
					}
				
			
			echo "</div></td><td style='width:50px;'>".mysql_result($result,$i,"now")."</td><td style='width:50px;'>".mysql_result($result,$i,"avant")."</td><td style='width:50px;'>".mysql_result($result,$i,"total")."</td></tr>";
		}
		echo "</table>";		
	}
?>
 </div>
 <i>Cliquez sur le nom de la page pour obtenir le détail.</i>
   <h2>Les sites depuis lesquels on vient sur le site</h2>
   <div style="background-color:#EEEEEE; width:560px; height:200px; overflow:auto">
 <?  
	$query = "SELECT stat_reference, count( * ) AS total, sum( stat_heure < DATE_SUB( NOW( ) , INTERVAL 7 DAY ) AND stat_heure >= DATE_SUB( NOW( ) , INTERVAL 14 DAY ) ) AS avant, sum( stat_heure >= DATE_SUB( NOW( ) , INTERVAL 7 DAY ) ) AS now FROM t_statistique WHERE stat_reference not like 'http://".substr($_SERVER['HTTP_HOST'],0,8)."%' group by stat_reference having now>0 or avant>0 or total>1 order by now desc, avant desc, total desc";
	$result = mysql_query($query, $dbh);
				
	if ($result>0 && mysql_num_rows($result)>0) {
		echo "<table >";			
		echo "<tr><th>URL</th><th>7 jours</th><th>14 jours</th><th>Total</th></tr>";
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo "<tr><td><div style='width:380px; overflow:hidden;'>";
			
			// si c'est une référence locale, affiche le nom local
				if (mysql_result($result,$i,0)=="") {echo "Accès direct";} else
				{
					echo "<a href='".mysql_result($result,$i,0)."' target='_blank'>".mysql_result($result,$i,0)."</a>";
				}
			
			echo "</div></td><td style='width:50px;'>".mysql_result($result,$i,"now")."</td><td style='width:50px;'>".mysql_result($result,$i,"avant")."</td><td style='width:50px;'>".mysql_result($result,$i,"total")."</td></tr>";
		}
		echo "</table>";		
	}
 ?></div></p>
 
     <p><strong>Accès par heure:</strong>
 <?  
	$query = "select HOUR(stat_heure) as jour, count(t_statistique.page_id) as cpt from t_statistique where stat_heure<>'' group by jour order by jour";
	$result = mysql_query($query, $dbh);
	echo "<table><tr><td valign='bottom' nowrap>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($cpt=$max=0;$cpt<mysql_num_rows($result); $cpt++) {if ($max<mysql_result($result,$cpt,1)) $max=mysql_result($result,$cpt,1);}
		for ($i=0; $i<24; $i++) {
			for ($cpt=0;$cpt<mysql_num_rows($result) && mysql_result($result,$cpt,0)!=$i; $cpt++);
			if ($cpt<mysql_num_rows($result)) {
				echo "<img border=1 src='/onlineEdit/dialogue/images/pxRed.gif' width='21' height='".(60/$max*mysql_result($result,$cpt,1))."' title='".mysql_result($result,$cpt,1)." visites'/>";
			} else {
				echo "<img border=0 src='/onlineEdit/dialogue/images/px.gif' width='23' height='0'/>";
			}
		}
	}
	echo "</td></tr></table>";			
	echo "<table width='557'><tr><td align='center' width='4%'>1</td><td align='center' width='4%'>2</td><td align='center' width='4%'>3</td><td align='center' width='4%'>4</td><td align='center' width='4%'>5</td><td align='center' width='4%'>6</td><td align='center' width='4%'>7</td><td align='center' width='4%'>8</td><td align='center' width='4%'>9</td><td align='center' width='4%'>10</td><td align='center' width='4%'>11</td><td align='center' width='4%'>12</td><td align='center' width='4%'>13</td><td align='center' width='4%'>14</td><td align='center' width='4%'>15</td><td align='center' width='4%'>16</td><td align='center' width='4%'>17</td><td align='center' width='4%'>18</td><td align='center' width='4%'>19</td><td align='center' width='4%'>20</td><td align='center' width='4%'>21</td><td align='center' width='4%'>22</td><td align='center' width='4%'>23</td><td align='center' width='4%'>24</td></tr></table>";

 ?></p>
      <p><strong>Accès par jour:</strong>
 <?  
	$query = "select DAYOFWEEK(stat_heure) as jour, count(t_statistique.page_id) as cpt from t_statistique where stat_heure<>'' group by jour order by jour";
	$result = mysql_query($query, $dbh);
	echo "<table><tr><td valign='bottom' nowrap>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($cpt=$max=0;$cpt<mysql_num_rows($result); $cpt++) {if ($max<mysql_result($result,$cpt,1)) $max=mysql_result($result,$cpt,1);}
		for ($i=1; $i<8; $i++) {
			for ($cpt=0;$cpt<mysql_num_rows($result) && mysql_result($result,$cpt,0)!=$i; $cpt++);
			if ($cpt<mysql_num_rows($result)) {
				echo "<img border=1 src='/onlineEdit/dialogue/images/pxRed.gif' width='77' height='".(60/$max*mysql_result($result,$cpt,1))."' title='".mysql_result($result,$cpt,1)." visites'/>";
			} else {
				echo "<img border=0 src='/onlineEdit/dialogue/images/px.gif' width='79' height='0'/>";
			}
		}
	}
	echo "</td></tr></table>";			
	echo "<table width='559'><tr><td align='center' width='14%'>Dimanche</td><td align='center' width='14%'>Lundi</td><td align='center' width='14%'>Mardi</td><td align='center' width='14%'>Mercredi</td><td align='center' width='14%'>Jeudi</td><td align='center' width='14%'>Vendredi</td><td align='center' width='14%'>Samedi</td></tr></table>";

 ?></p>
 
      <p><b>Nombre de pages vues les 90 derniers jours:</b>
 <?  
		$query = "select DATE_FORMAT(stat_heure,'%Y%m%d') as jour, count(t_statistique.page_id) as cpt from t_statistique where stat_heure<>'' and stat_heure>DATE_SUB(CURDATE(),INTERVAL 90 DAY) and stat_heure<now() group by jour order by jour";
	$result = mysql_query($query, $dbh);
	echo "<table border='0'><tr><td>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($cpt=$max=0;$cpt<mysql_num_rows($result); $cpt++) {if ($max<mysql_result($result,$cpt,1)) $max=mysql_result($result,$cpt,1);}
		for ($i=90; $i>=0; $i--) {
			for ($cpt=0;$cpt<mysql_num_rows($result) && mysql_result($result,$cpt,0)!=date("Ymd",time()-$i*60*60*24); $cpt++);
			if ($cpt<mysql_num_rows($result)) {
				echo "<img border=1 src='/onlineEdit/dialogue/images/pxRed.gif' width='4' height='".(60/$max*mysql_result($result,$cpt,1))."' title='".substr(mysql_result($result,$cpt,"jour"),6,2).".".substr(mysql_result($result,$cpt,"jour"),4,2).".".substr(mysql_result($result,$cpt,"jour"),0,4)." : ".mysql_result($result,$cpt,1)." visites'/>";
			} else {
				echo "<img border=0 src='/onlineEdit/dialogue/images/px.gif' width='6' height='0'/>";
			}
		}	
	}
	echo "</td></tr></table>";	
	echo "<table width='552'><tr><td align='left'>".date("d.m.Y",time()-90*60*60*24)."</td><td align='center'>".date("d.m.Y",time()-45*60*60*24)."</td><td align='right'>".date("d.m.Y",time())."</td></tr></table>";
	echo "<i>Gardez le pointeur sur une barre pour obtenir la date et le nombre de visite exacte.</i>";		

 ?></p>      <p><b>Nombre de visiteurs à 3 pages ou plus par jour:</b>
 <?  
		mysql_query ("CREATE TEMPORARY TABLE temp_union select DATE_FORMAT(stat_heure,'%Y%m%d') as jour, stat_ip, count(stat_ip) as cpt from t_statistique where stat_heure<>'' and stat_heure>DATE_SUB(CURDATE(),INTERVAL 90 DAY) and stat_heure<now() group by jour, stat_ip having cpt>2 ",$dbh);

				$query="select jour, count(distinct stat_ip) as cpt  FROM temp_union group by jour order by jour";


$result2 = mysql_query($query, $dbh);

mysql_query("DROP TABLE temp_union;", $dbh);

		$query = "select DATE_FORMAT(stat_heure,'%Y%m%d') as jour, count(distinct t_statistique.stat_ip) as cpt from t_statistique where stat_heure<>'' and stat_heure>DATE_SUB(CURDATE(),INTERVAL 90 DAY) and stat_heure<now() group by jour order by jour";
	$result = mysql_query($query, $dbh);

	echo "<table border='0'><tr><td><table cellspacing=0 cellpadding=0><tr>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($cpt=$max=$cpt2=0;$cpt<mysql_num_rows($result); $cpt++) {if ($max<mysql_result($result,$cpt,1)) $max=mysql_result($result,$cpt,1);}
		for ($i=90; $i>=0; $i--) {
			for ($cpt=0;$cpt<mysql_num_rows($result) && mysql_result($result,$cpt,0)!=date("Ymd",time()-$i*60*60*24); $cpt++);
			echo "<td style='vertical-align:bottom'>";
			if ($cpt<mysql_num_rows($result)) {

		if ($cpt2<mysql_num_rows($result2) && mysql_result($result,$cpt,0)==mysql_result($result2,$cpt2,0)) {
			echo "<img border=1 src='/onlineEdit/dialogue/images/pxRed.gif' width='4' height='".((60/$max*mysql_result($result,$cpt,1))-(60/$max*mysql_result($result2,$cpt2,1)))."' title='".substr(mysql_result($result,$cpt,"jour"),6,2).".".substr(mysql_result($result,$cpt,"jour"),4,2).".".substr(mysql_result($result,$cpt,"jour"),0,4)." : ".mysql_result($result,$cpt,1)." visiteurs'/>";
		
					echo "<br><img style='border:1px solid black; border-top:0px' src='/onlineEdit/dialogue/images/pxBlue.gif' width='4' height='".(60/$max*mysql_result($result2,$cpt2,1))."'			title='".substr(mysql_result($result2,$cpt2,"jour"),6,2).".".substr(mysql_result($result2,$cpt2,"jour"),4,2).".".substr(mysql_result($result2,$cpt2,"jour"),0,4)." : ".mysql_result($result2,$cpt2,1)." visiteurs'/>";
				$cpt2=$cpt2+1;}
				else {
					echo "<img border=1 src='/onlineEdit/dialogue/images/pxRed.gif' width='4' height='".(60/$max*mysql_result($result,$cpt,1))."' title='".substr(mysql_result($result,$cpt,"jour"),6,2).".".substr(mysql_result($result,$cpt,"jour"),4,2).".".substr(mysql_result($result,$cpt,"jour"),0,4)." : ".mysql_result($result,$cpt,1)." visiteurs'/>";
			
			}
			} else {
				echo "<img border=0 src='/onlineEdit/dialogue/images/px.gif' width='6' height='0'/>";
			}
			echo "</td>";
		}	
	}
	echo "</tr></table></td></tr></table>";	
	echo "<table width='552'><tr><td align='left'>".date("d.m.Y",time()-90*60*60*24)."</td><td align='center'>".date("d.m.Y",time()-45*60*60*24)."</td><td align='right'>".date("d.m.Y",time())."</td></tr></table>";
	echo "<i>Gardez le pointeur sur une barre pour obtenir la date et le nombre de visiteur.</i>";		

 ?></p>      <p><b>Ratio pages/visiteurs les 90 derniers jours:</b>
 <?  
		$query = "select DATE_FORMAT(stat_heure,'%Y%m%d') as jour, count(t_statistique.stat_ip)/count(distinct t_statistique.stat_ip) as cpt from t_statistique where stat_heure<>'' and stat_heure>DATE_SUB(CURDATE(),INTERVAL 90 DAY) and stat_heure<now() group by jour order by jour";
	$result = mysql_query($query, $dbh);
	echo "<table border='0'><tr><td>";			
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($cpt=$max=0;$cpt<mysql_num_rows($result); $cpt++) {if ($max<mysql_result($result,$cpt,1)) $max=mysql_result($result,$cpt,1);}
		for ($i=90; $i>=0; $i--) {
			for ($cpt=0;$cpt<mysql_num_rows($result) && mysql_result($result,$cpt,0)!=date("Ymd",time()-$i*60*60*24); $cpt++);
			if ($cpt<mysql_num_rows($result)) {
				echo "<img border=1 src='/onlineEdit/dialogue/images/pxRed.gif' width='4' height='".(60/$max*mysql_result($result,$cpt,1))."' title='".substr(mysql_result($result,$cpt,"jour"),6,2).".".substr(mysql_result($result,$cpt,"jour"),4,2).".".substr(mysql_result($result,$cpt,"jour"),0,4)." : ".mysql_result($result,$cpt,1)." pages par visiteur'/>";
			} else {
				echo "<img border=0 src='/onlineEdit/dialogue/images/px.gif' width='6' height='0'/>";
			}
		}	
	}
	echo "</td></tr></table>";	
	echo "<table width='552'><tr><td align='left'>".date("d.m.Y",time()-90*60*60*24)."</td><td align='center'>".date("d.m.Y",time()-45*60*60*24)."</td><td align='right'>".date("d.m.Y",time())."</td></tr></table>";
	echo "<i>Gardez le pointeur sur une barre pour obtenir la date et le nombre de pages vues.</i>";		

 ?></p>
<? echo '<div align="right"><input type="button" value="Fermer" name="fermer" onClick="window.close();"/></div>';?></div>
</body>
</html>
