<?
	header('Content-Type: text/html; charset=iso-8859-1',true);
	session_start();
	include("db.php");
	include_once ("onlineEdit.php");
	$dbh =  connectDb(); 
	$lettre = array("'","é","è","à","ô");
	$code = array("\'","&eacute;","&egrave;","&agrave;","o");
	
	if (@ereg ("<zone id=\"([0-9]+)_([0-9]+)\">((.|\n|\r)*)</zone>", $HTTP_RAW_POST_DATA, $regs)) {
	 
		// Sauve les informations dans la base de donnée
		$query = sprintf("select * from t_zone where zone_id='%s' and lang_id=%s",$regs[1].'_'.$regs[2],$_SESSION["langue"]);
		$result=mysql_query($query, $dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			$txt=mysql_result($result,0,"zone_contenu");
			if ($regs[3]=="converti") {
					$txt = convertZone($txt, $dbh);
			}
			echo $txt;
			//echo utf8_encode($txt);
		}
		else {
				echo "";

		//echo ("Les modifications ont été enregistrées.");
		}
	}

?>
