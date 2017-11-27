<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");

	if (isset($_GET["idCircle"])) {
			// Charge le cercle
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["idCircle"]);
			// Charge la liste des Users
			$users=$circle->getMembers();
			// L'affiche selon le format demandé
			echo "<option value=''>1er lien</option>";
			foreach($users as $user) {
				echo "<option value=\"".$user->getId()."\">".$user->getUserName()."</option>";
			}
	}
	if (isset($_POST["user_find"])) {
			$filter=new \holacracy\Filter();
			$filter->addCriteria("all",$_POST["user_find"]);
			$users= $_SESSION["currentManager"]->findUsers($filter);
			// L'affiche selon le format demandé
			foreach($users as $user) {
				echo "<option value=\"".$user->getId()."\">".$user->getFirstName()." ".$user->getLastName()." (".$user->getUserName().")</option>";
			}
	}

	if (isset($_GET["idRole"])) {
			// Charge le rôle
			$role=$_SESSION["currentManager"]->loadRole($_GET["idRole"]);
			// Charge la liste des Users
			$users=$role->getRoleFillers();
			// L'affiche selon le format demandé
			echo "<option value=''>Personne en charge du rôle</option>";
			foreach($users as $user) {
				echo "<option value=\"".$user->getUserId()."\">".$user->getUserName()."</option>";
			}
			
	}
?>
