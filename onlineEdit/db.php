<?php
	
	$db_INCLUDED=true;
	global $dbh;
	
	include_once($_SERVER["DOCUMENT_ROOT"]."/site_config.php");
	
	$dbh=@mysql_connect ("localhost", $db_user, $db_password);
	if ($dbh) {
		mysql_select_db ($db_name); 
	} else {
		$dbh=mysql_connect ("localhost", "root", "") or die ('I cannot connect to the database because: ' . mysql_error());
		mysql_select_db ($db_name); 
	}
	
	// Si un changement de langue est requis
	if (isset($_POST['langue'])) {
		$_SESSION['langue']=$_POST['langue'];
		setcookie ("langue", $_POST['langue'],time()+60*60*24*365);
	} else
	if (isset($_GET['langue'])) {
		$_SESSION['langue']=$_GET['langue'];
		setcookie ("langue", $_GET['langue'],time()+60*60*24*365);
	} else
	
	// Choisi la langue si elle n'est pas définie
	if (!isset($_SESSION['langue'])) {
		// Lire le cookie client
		if (isset($_COOKIE["langue"])) {
			$_SESSION['langue']=$_COOKIE["langue"];
		} else {
			// Sinon lit la langue du navigateur dans la DB
			/* $query="select * from t_langue where lang_abreviation='".substr($_SERVER["HTTP_ACCEPT_LANGUAGE"],0,2)."'";
			$result=mysql_query($query, $dbh);
			if ($result>0 && mysql_num_rows($result)>0) {
					$_SESSION['langue']=mysql_result($result,0,"lang_id");
			} else { 
				$query="select * from t_langue where lang_default=1";
				$result=mysql_query($query, $dbh);
				if ($result>0 && mysql_num_rows($result)>0) {
					$_SESSION['langue']=mysql_result($result,0,"lang_id");
				} else {
					$_SESSION['langue']=1;
				}
			}*/
			$_SESSION['langue']=1;
		}
		setcookie ("langue", $_SESSION['langue'],time()+60*60*24*365);
	} 
	
	function addURL($txt) {
		$pattern = '/(www\.[a-z,A-Z,0-9,\-,_,\.]*\.[a-z,A-Z]+)/i';
		$replacement =  '<a href="http://\\1" target="_blank">\\1</a>';
		$txt = preg_replace($pattern, $replacement, $txt);
		return $txt;
	}
	
	function translate($txt, $default=NULL, $rajouter=true, $langue=NULL) {
		// Recherche le terme dans le dico
		if ($langue==NULL) $langue=$_SESSION['langue'];
		$query="select * from tm_dico where dico_id='".$txt."' and lang_id=".$langue."";
		$result=mysql_query($query, $GLOBALS["dbh"]);
		// Si le trouve, renvoie le texte (uniquement si différent de null)
		if ($result>0 && mysql_num_rows($result)>0) {
			if (mysql_result($result,0,"dico_txt")!=NULL) {
				return addURL(mysql_result($result,0,htmlentities("dico_txt")));
			} else {
				if ($default!=NULL) {
					return addURL($default);
				} else {
					return "";
				}			
			}
		} else {
			if ($rajouter) {
				// Rajoute l'entrée dans le dico
				$query="insert into tm_dico (dico_id, lang_id, dico_txt) values ('".$txt."',".$langue.",NULL)";
				$result=mysql_query($query);
			}
			// Sinon, renvoie le texte par défaut
			if ($default!=NULL) {
				return addURL($default);
			} else {
				return "";
			}
			
		}
	}
	
	function setDico ($cle, $langue, $txt=NULL) {
	if ($txt!=NULL) $txt='"'.addslashes(stripslashes($txt)).'"';

		$query="select * from tm_dico where dico_id='".$cle."' and lang_id=".$langue."";
		$result=mysql_query($query, $GLOBALS["dbh"]);
		if ($result>0 && mysql_num_rows($result)>0) {
			$query="update tm_dico set dico_txt=".$txt." where dico_id='".$cle."' and lang_id=".$langue."";
			$result=mysql_query($query);

		} else {
			$query="insert into tm_dico (dico_id, lang_id, dico_txt) values ('".$cle."',".$langue.",".$txt.")";
			$result=mysql_query($query);
		}
	}
	
	function connectDb()
	{
		return $GLOBALS["dbh"];
	}
	
	function getPageInfo($noPage) {
		if (!isset($_SESSION["langue"])) {$_SESSION["langue"]=1;}
		$query="select * from t_pageinfo where page_id=".$noPage." and lang_id=".$_SESSION["langue"];
		$resulttmp = mysql_query($query, $GLOBALS['dbh']);
		if ($resulttmp==0 || mysql_num_rows($resulttmp)==0) {
			$query=str_replace("and lang_id=".$_SESSION["langue"],"",$query);
			$resulttmp = mysql_query($query, $GLOBALS['dbh']);
			// Pour les anciennes versions
			if (mysql_num_rows($resulttmp)==0) {
				$resulttmp = mysql_query("select * from t_page where page_id=".$noPage, $GLOBALS['dbh']);
			}
		}
		return $resulttmp;
	}
?>
