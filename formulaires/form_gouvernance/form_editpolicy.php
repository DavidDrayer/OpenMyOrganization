<?
	if (isset($_GET["policy"])) {
		// Affichage du formulaire d'édition du cercle, le même que pour la création
		include("form_gouvernance/form_addpolicy.php");
	} else {
	echo "<select name='policy"."_".$_GET["id"]."' id='policy"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez une politique...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$policies=$circle->getPolicy();
    		// Affiche la liste
			foreach ($policies as $policy) {
    			echo "<option value='".$policy->getId()."'>".$policy->getTitle()."</option>";
			}
			
			$liste=$circle->getLinks();
			
			foreach($liste as $entry) {
    			echo "<option value='LT".$entry->getId()."' ";
				if ($entry->getSourceCircleId()!=$circle->getId()) echo "disabled";
				echo ">";
				echo "Politique de lien transverse de [".$entry->getSourceCircle()->getName()."] vers [".$entry->getSuperCircle()->getName()."]";
				echo "</option>";
			}			
			
    	echo "</select>";
?>
<script>
	$("#policy_<?=$_GET["id"]?>").on('change',function() {
		// Remplacement du contenu du TAB
		
		$(this).closest("div.ui-tabs-panel").load( "/formulaires/form_gouvernance.php?circle=<?=$_GET["circle"]?>&action=getForm&param=<?=$_GET["param"]?>&id=<?=$_GET["id"]?>&policy="+$(this).val(), function(response, status, xhr) {
		  $(this).html(response);
		});
		$(this).closest("div.ui-tabs-panel").html("Chargement...");
	});
</script>
<?
	}
?>