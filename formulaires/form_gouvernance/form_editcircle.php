<?
	if (isset($_GET["subcircle"])) {
		// Affichage du formulaire d'édition du cercle, le même que pour la création
		include("form_gouvernance/form_addcircle.php");
	} else {
	echo "<select name='idCircle"."_".$_GET["id"]."' id='idCircle"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez un cercle...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getRoles(\holacracy\Role::CIRCLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
    	echo "</select>";
?>
<script>
	$("#idCircle_<?=$_GET["id"]?>").on('change',function() {
		// Remplacement du contenu du TAB
		
		$(this).closest("div.ui-tabs-panel").load( "/formulaires/form_gouvernance.php?circle=<?=$_GET["circle"]?>&action=getForm&param=<?=$_GET["param"]?>&id=<?=$_GET["id"]?>&subcircle="+$(this).val(), function(response, status, xhr) {
		  $(this).html(response);
		});
		$(this).closest("div.ui-tabs-panel").html("Chargement...");
	});
</script>
<?
	}
?>