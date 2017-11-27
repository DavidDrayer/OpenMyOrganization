<?
	echo "<select id='policy"."_".$_GET["id"]."' name='policy"."_".$_GET["id"]."'>";
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
				if ($entry->getMasterId()!=$circle->getId()) echo "disabled";
				echo ">";
				echo "Politique de lien transverse de [".@$entry->getSourceCircle()->getName()."] vers [".@$entry->getSuperCircle()->getName()."]";
				echo "</option>";
			}
			
    	echo "</select>";
?>
<script>
	$("#policy_<?=$_GET["id"]?>").on('change',function() {
		// Changement du menu
		<? echo "$('#tab_gouv_".$_GET["id"]."').html('Supprimer la politique<br/><b>'+$(this).find('option:selected').text()+'</b>');"; ?>


	});
</script>
