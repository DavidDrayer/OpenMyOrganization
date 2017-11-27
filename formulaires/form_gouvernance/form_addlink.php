<?

	function listeCircle($circle,$indent="") {
	    	$roles=$circle->getRoles(\holacracy\Role::CIRCLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
				listeCircle($role,$indent."  ");
			}
	}

	echo "<div>Cercle source</div>";
	echo "<select id='idSourceCircle"."_".$_GET["id"]."' name='idSourceCircle"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez le cercle source...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des r�les qui peuvent �tre modifi�s
 			listeCircle($circle);
    	echo "</select>";
	echo "<div>Cercle cible</div>";
	echo "<select id='idTargetCircle"."_".$_GET["id"]."' name='idTargetCircle"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez le cercle destination...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des r�les qui peuvent �tre modifi�s
    		$roles=$circle->getRoles(\holacracy\Role::CIRCLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
    	echo "</select>";
?>
<script>
	$("#idCircle_<?=$_GET["id"]?>").on('change',function() {
		// Changement du menu
		<? echo "$('#tab_gouv_".$_GET["id"]."').html('Supprimer le cercle<br/><b>'+$(this).find('option:selected').text()+'</b>');"; ?>


	});
</script>