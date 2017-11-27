<?	
  	// récupère le path
 	$badUrl= $_SERVER['REQUEST_URI'] ;
 	//Est-ce une page atteinte avec une langue
 	
 	
 	// Est-ce un shortcut?
 	// Supprime la querystring si nécessaire
 	if (strpos($badUrl,"?")>0) {
 		$shortcut=substr($badUrl,0,strpos($badUrl,"?"));
 		$querystring=substr($badUrl,strpos($badUrl,"?")+1);
	} else {
		$shortcut=$badUrl;
		$querystring="";
	}  
	// Recrée les GET
	if ($querystring!="") {
		$params=split("&",$querystring);
		foreach ($params as $param) {
			$tmp=split("=",$param);
			$_GET[$tmp[0]]=$tmp[1];
		}
		
	}

	if (!isset($db_INCLUDED)) {
		include_once ($_SERVER["DOCUMENT_ROOT"]."/onlineEdit/db.php");
		$dbh =  connectDb(); 
	}
	
 	$query = "select * from t_page where page_shortcut='".substr($shortcut,1)."'";
	$result = mysql_query($query, $dbh);
		if ($result>0 && mysql_num_rows($result)>0) {
			ob_start();
			$redirectedPage=mysql_result($result,0,"page_id");
			header($_SERVER["SERVER_PROTOCOL"]." 200 OK",true,200);
			header("Status: 200 OK", true, 200);
			include 'index.php';
			ob_end_flush();
			exit;
			//header('Location: index.php?page='.mysql_result($result,0,"page_id"));
			//exit;
		} else {
  
  	// Le cherche dans la base de données
	$query = "select * from t_404 where 404_mauvais='".$badUrl."'";
	$result = mysql_query($query, $dbh);
	
	// S'il a été trouvé, incrémente le compteur et redirige
	if ($result>0 && mysql_num_rows($result)>0) {
		$newUrl=mysql_result($result,0,"404_redirection");

		$query = "update t_404 set 404_compteur=".(mysql_result($result,0,"404_compteur")+1).", 404_dernier=now(), 404_reference='".$_SERVER['HTTP_REFERER']."' where 404_mauvais='".mysql_result($result,0,"404_mauvais")."'";
		$result = mysql_query($query, $dbh);
		if ($newUrl!=null) {
			header("Status: 301 Moved Permanently", false, 301);
			header('Location: '.$newUrl);
			exit;
		}
	} else {	
		// Sinon ajoute une ligne dans la table
		$query = "insert t_404 (404_mauvais, 404_compteur, 404_dernier, 404_reference) VALUES ('$badUrl', 1, now(), '".$_SERVER['HTTP_REFERER']."')";
		$result = mysql_query($query, $dbh);
}
  }
header("HTTP/1.0 404 Not Found");
  ?>
<html>
<head>
<title>Fichier non trouvé (erreur 404)</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

</head>

<body>
<div align="center">
  <h2>&nbsp;</h2>
  <h2>&nbsp;</h2>
  <h2>Le fichier n'a pas été trouvé
        </h2>
        <?
        	echo $badUrl;
        ?>
  <p><a href="index.php">Cliquez ici pour vous rendre sur la page d'accueil du site.</a>
    </p>
</div>
</body>
</html>
