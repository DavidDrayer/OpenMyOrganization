<?
	// Inclus les �l�ments partag�s entre plusieurs pages: base de donn�e, instanciation de classe, etc...
	include_once("../include.php");
	
	// Formulaire destin� � �tre inclus, qui utilisera le manager du niveau sup�rieur si possible
	if (!isset($manager)) {
		$manager=new \datamanager\sqlManager($dbh);
	}
	
	// Supprime le metric
	$action=$_SESSION["currentManager"]->loadActions($_GET["id"]);
	$id=$action->getId();
	$_SESSION["currentManager"]->delete($action);
	

?>
    deleteAction(<?=$id?>);

