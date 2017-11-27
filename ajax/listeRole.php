<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");

	if (isset($_GET["idCircle"])) {
			// Charge le cercle
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["idCircle"]);
			// Charge la liste des Users
			$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE);
			// L'affiche selon le format demandé
			foreach($roles as $role) {
				echo "<option value=\"".$role->getId()."\"";
				echo ">".$role->getName()."</option>";
			}
	}

?>
