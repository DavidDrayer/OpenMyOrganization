<?
	$sent = headers_sent(); 
	if (!$sent) header("Content-type:text/xml;charset:UTF-8");
	include_once ($_SERVER["DOCUMENT_ROOT"]."/onlineEdit/db.php");
	$dbh =  connectDb(); 
	
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n\n"; 


	$query="select * from t_page where page_active=1";
	$result=mysql_query($query,$dbh);
	echo "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";
	if ($result>0 && mysql_num_rows($result)>0) {
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo "<url>\n";
			if (mysql_result($result,$i,"page_shortcut")!="") {
				echo "<loc>http://".$_SERVER['SERVER_NAME']."/".mysql_result($result,$i,"page_shortcut")."</loc>\n";
			} else {
				echo "<loc>http://".$_SERVER['SERVER_NAME']."/index.php?page=".mysql_result($result,$i,"page_id")."</loc>\n";
			}
			$query="select *, DATE_FORMAT(zone_dateModification,'%Y-%m-%d') as d1, (TO_DAYS(NOW()) - TO_DAYS(zone_dateModification)) as dt from t_zone where zone_id like '".mysql_result($result,$i,"page_id")."_%' order by zone_dateModification desc";
			$result2=mysql_query($query, $dbh);
			if ($result2>0 && mysql_num_rows($result2)>0) {
				echo "<lastmod>".mysql_result($result2,0,"d1")."</lastmod>\n";
				
				if (mysql_result($result2,0,"dt")<3) {
					$changement="daily";
				} else if (mysql_result($result2,0,"dt")<10) {
					$changement="weekly";
				} else if (mysql_result($result2,0,"dt")<45) {
					$changement="monthly";
				} else if (mysql_result($result2,0,"dt")<400) {
					$changement="yearly";
				} else {
				$changement="never";
				}
				
				echo "<changefreq>".$changement."</changefreq>\n";
				echo "<priority>".number_format((mysql_result($result,$i,"page_priority")/100),1)."</priority>\n";
			} else {
				echo "<priority>0.0</priority>\n";
			}
			echo "</url>\n";
		}
		
	}
	
	// Affichge spécifique des rubriques
	$query="select * from tm_rubrique where rubr_actif=1";
	$result=mysql_query($query,$dbh);

	if ($result>0 && mysql_num_rows($result)>0) {
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo "<url>\n";
			echo "<loc>index.php?page=3&amp;rubrId=".mysql_result($result,$i,"rubr_id")."</loc>\n";
			$query="select *, DATE_FORMAT(arti_dateMiseAJour,'%Y-%m-%d') as d1 from tm_artiste left join tm_artiste_rubrique on (tm_artiste.arti_id=tm_artiste_rubrique.arti_id) where rubr_id =".mysql_result($result,$i,"rubr_id")." order by arti_dateMiseAJour desc";
			$result2=mysql_query($query, $dbh);
			if ($result2>0 && mysql_num_rows($result2)>0) {
				echo "<lastmod>".mysql_result($result2,0,"d1")."</lastmod>\n";
				echo "<priority>0.8</priority>\n";
			} else {
				echo "<priority>0.0</priority>\n";
			}
			echo "</url>\n";
		}
		
	}	
	
		// Affichge spécifique des rubriques
	$query="select *, DATE_FORMAT(arti_dateMiseAJour,'%Y-%m-%d') as d1  from tm_artiste";
	$result=mysql_query($query,$dbh);

	if ($result>0 && mysql_num_rows($result)>0) {
		for ($i=0; $i<mysql_num_rows($result); $i++) {
			echo "<url>\n";
			echo "<loc>index.php?page=7&amp;artiId=".mysql_result($result,$i,"arti_id")."</loc>\n";
			echo "<lastmod>".mysql_result($result,$i,"d1")."</lastmod>\n";
			echo "<priority>0.8</priority>\n";
			echo "</url>\n";
		}
		
	}	
	
	echo "</urlset>\n";

?>
