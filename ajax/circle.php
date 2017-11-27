<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	if (isset($_REQUEST["action"])) {
		if (isset($_REQUEST["action"]) && $_REQUEST["action"]=="sendChat") {
			$chat=new \holacracy\Chat ();
			$chat->setText(utf8_decode($_REQUEST["txt"]));
			$chat->setUserId($_SESSION["currentUser"]->getId());
			$chat->setCircleId($_REQUEST["id"]);
			
			$_SESSION["currentManager"]->save($chat);
		}
	}
	
?>


