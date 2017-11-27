<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destiné à être inclus, qui utilisera le manager du niveau supérieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Supprime le metric
	$action=$_SESSION["currentManager"]->loadActions($_GET["id"]);
	$id=$action->getId();
	$_SESSION["currentManager"]->delete($action);
	

?>
    deleteAction(<?=$id?>);

