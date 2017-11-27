<?


	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);

	// Crée une action à partir de rien
	$action = new \holacracy\Action();
	$action->setCircle(329);
	$action->setTitle("Routine de test - Action");
	
	// Y ajoute un check
	$action->addUser(3);
	
	// sauve le tout
	$manager->save($action, true);
	
?>
