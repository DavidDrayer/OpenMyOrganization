<?
	if (isset($_GET["role"])) {
		// Affichage du formulaire d'édition du cercle, le même que pour la création
		include("form_gouvernance/form_addcircle.php");
	} else {
	echo "<select name='idRole"."_".$_GET["id"]."' id='idRole"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez un rôle...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE | \holacracy\Role::LINK_ROLE | \holacracy\Role::STRUCTURAL_ROLES);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
    	echo "</select>";
?>
<script>
	$("#idRole_<?=$_GET["id"]?>").on('change',function() {
		// Remplacement du contenu du TAB
		
		$(this).closest("div.ui-tabs-panel").load( "/formulaires/form_gouvernance.php?circle=<?=$_GET["circle"]?>&action=getForm&param=<?=$_GET["param"]?>&id=<?=$_GET["id"]?>&role="+$(this).val(), function(response, status, xhr) {
		  $(this).html(response);
		});
		$(this).closest("div.ui-tabs-panel").html("Chargement...");
	});
</script>
<?
	}
?>
