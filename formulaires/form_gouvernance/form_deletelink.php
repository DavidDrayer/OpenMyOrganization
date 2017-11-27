<?
	echo "<select id='idRole"."_".$_GET["id"]."' name='idRole"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez un rôle...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getAllRoles(\holacracy\Role::LINK_ROLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
    	echo "</select>";
?>
<script>
	$("#idRole_<?=$_GET["id"]?>").on('change',function() {
		// Changement du menu
		<? echo "$('#tab_gouv_".$_GET["id"]."').html('Supprimer le rôle<br/><b>'+$(this).find('option:selected').text()+'</b>');"; ?>


	});
</script>