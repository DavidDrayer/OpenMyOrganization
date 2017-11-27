<?
	header('Content-Type: text/html; charset=iso-8859-1',true);
	session_start();
	include("db.php");
	$dbh =  connectDb(); 
	$lettre = array("'","é","è","à");
	$code = array("\'","&eacute;","&egrave;","&agrave;");
	
	if (ereg ("<zone id=\"([0-9]+)_([0-9]+)\">((.|\n|\r)*)</zone>", $HTTP_RAW_POST_DATA, $regs)) {
	 
		// Sauve les informations dans la base de donnée
		$strConvert = str_replace($lettre, $code, utf8_decode($regs[3]));
		$query = sprintf("delete from t_zone where zone_id='%s' and lang_id=%s",$regs[1].'_'.$regs[2],$_SESSION["langue"]);
		mysql_query($query, $dbh);
		$query = sprintf("insert t_zone (zone_contenu, zone_id, zone_dateModification, lang_id) values ('%s','%s',now(),%s)",$strConvert,$regs[1].'_'.$regs[2],$_SESSION["langue"]);
		$result = mysql_query($query, $dbh);
		if ($result>0) 
			echo ("Les modifications ont été enregistrées.");
			//echo utf8_encode("Les modifications ont été enregistrées.");
		else
			echo $query;
		//echo ("Les modifications ont été enregistrées.");
	
	} else {
	
	if (ereg ("<POSTIT id=([0-9]+).*>((.|\n|\r)*)</POSTIT>", $HTTP_RAW_POST_DATA, $regs)) {
		
		$strConvert = str_replace($lettre, $code, utf8_decode($regs[2]));
		$query = sprintf("update t_postit set post_contenu='%s' where post_id=%s",$strConvert,$regs[1]);
		$result = mysql_query($query, $dbh);
		// echo utf8_encode("Le postit a été enregistrées.");
		echo ("Le postit a été enregistrées.");
	
	} else {	
	
	echo "Ceci est une version de demonstration.\nVous n'avez pas les droits pour sauver des informations.";
	}}

?>
