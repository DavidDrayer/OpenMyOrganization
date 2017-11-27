<?php 
// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// ********************************************************************
	// ********* Zone POST : formulaire envoyé et données à manipuler  ****
	// ********************************************************************
    if (isset($_GET["action"]) && !empty($_GET["action"])) {
    $action = $_GET["action"];
    switch($action) { //Switch de l'action puis de l'appel des bonnes fonctions
      	  
	  case "check": 
		// Set cookie
		setcookie("noCrowdfunding", 1,0,"/");
	  break;

    }
	}
	
	echo isset($_COOKIE["noCrowdfunding"]);

?>
