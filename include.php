<?php
	// Encodage des caractères
	header('Content-type: text/html; charset=ISO-8859-1');
	
	//Pour le système de langue
	$basepath = "/var/www/beta"; 
	include_once('plugins/gettext/gettext.inc');
	
	// Temporise la sortie
	ob_start();
	
	// Affichage des erreurs (en version debug)
	 error_reporting(E_ALL);
	 ini_set("display_errors", 1);

   // Chargement à la demande des classes non instanciées
	spl_autoload_register(function ($class) {
	    include dirname(__FILE__)."/".'class/' . str_replace("\\","/",strtolower($class)) . '.class.php';
	});

	// Démarrage de la session
	session_start();

	// Connection à la base de données (d'abord test, puis dev local en cas d'échec)

		$dbh=@mysql_connect ("localhost", "web296", "uPOsQH7C5!");
		if ($dbh) {
			mysql_select_db ("usr_web296_8"); 
		} else {
				$dbh=@mysql_connect ("127.0.0.1", "root", "") or die ('I cannot connect to the database because: ' . mysql_error());
				if ($dbh) {
					mysql_select_db ("omo_beta"); 
				} else {
					$dbh=@mysql_connect ("db2393.1and1.fr", "dbo320814076", "lowanka!7");
					mysql_select_db ("db320814076"); 
			} 
		}

	$_SESSION["currentDB"]=$dbh;
	$_SESSION["currentManager"]=new \datamanager\SqlManager($_SESSION["currentDB"]);	

	// Chargement de la langue de l'utilisateur
	if (isset($_SESSION["currentUser"])){ 
		// recharge l'utilisateur pour mise à jour
		if ($_SESSION["currentUser"]->getId()>0) {
			$_SESSION["currentUser"]=$_SESSION["currentManager"]->loadUser($_SESSION["currentUser"]->getId());
		}
		//Si la session existe on active la langue
		$languser = $_SESSION["currentUser"]->getUserLangue();
		$_SESSION["currentUser"]->ActiveLanguage($languser,$basepath);
		
		//
		$_SESSION["template"]=$_SESSION["currentUser"]->GetPreference("template");
	} 

	
	
	if (!isset($_SESSION["currentUser"]) && isset($_COOKIE["RememberUser"]) && $_COOKIE["RememberUser"]!="") { 
		$filter=new \holacracy\Filter();
		$filter->addCriteria("userId",$_COOKIE["RememberUser"]);
		$users= $_SESSION["currentManager"]->findUsers($filter);
		if (count($users)==1) {
			$_SESSION["currentUser"]=$users[0];	
			// Sauve la date
			$_SESSION["currentUser"]->setLastConnexion();
			$_SESSION["currentManager"]->save($_SESSION["currentUser"]);

			include_once('plugins/gettext/gettext.inc');
			//Si la session existe on active la langue
			$languser = $_SESSION["currentUser"]->getUserLangue();
			$_SESSION["currentUser"]->ActiveLanguage($languser,$basepath);
		} else {
			
			exit;
		}
	}
	if (!isset($_SESSION["template"]) || $_SESSION["template"]=="")
		$_SESSION["template"]="new";

?>
