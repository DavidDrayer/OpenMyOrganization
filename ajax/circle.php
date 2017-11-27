<?
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destin� � �tre inclus, qui utilisera le manager du niveau sup�rieur si possible
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


