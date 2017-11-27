<?
	// Option pour choisir si c'est un tout nouveau cercle ou la transformation d'un rôle en cercle
	if (!isset($_GET["subcircle"]) && !isset($_GET["role"])) {
		echo "<div>Mode de supression : <select name='modeSuppression_".$_GET["id"]."' id='modeSuppression_".$_GET["id"]."' style='width:100%'><option value='1'>Supprimer le rôle</option><option value='2'>Fusioner le rôle avec un autre rôle</option></select></div>";
	}

	echo "Nom: <select id='idRole"."_".$_GET["id"]."' style='width:100%' name='idRole"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez un rôle...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
    	echo "</select>";
 		
 	echo "<div id='option2_".$_GET["id"]."' style='display:none'>";
 	echo "Fusionner avec: <select id='idRole2"."_".$_GET["id"]."' style='width:100%' name='idRole2"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez un rôle...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
    	echo "</select>";
 		
 	echo "</div><div id='option1_".$_GET["id"]."'>";
	
    // Que faire avec ses projets, indicateurs et checklistes
 		echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Options</span><div id='mask2'></div></legend>";
     	echo "<div>Projets : ";
    	echo "<select name='optProject_".$_GET["id"]."' id='optProject_".$_GET["id"]."'><option value=0>Attribuer au 1er Lien</option><option value=1>Supprimer</option></select></div>";
     	echo "<div>Indicateurs : ";
    	echo "<select name='optInd_".$_GET["id"]."' id='optInd_".$_GET["id"]."'><option value=0>Attribuer au 1er Lien</option><option value=1>Supprimer</option></select></div>";
     	echo "<div>Check-listes : ";
    	echo "<select name='optCheck_".$_GET["id"]."' id='optCheck_".$_GET["id"]."'><option value=0>Attribuer au 1er Lien</option><option value=1>Supprimer</option></select></div>";
    	echo "<div>Documents : ";
    	echo "<select name='optDoc_".$_GET["id"]."' id='optDoc_".$_GET["id"]."'><option value=0>Attribuer au 1er Lien</option><option value=1>Supprimer</option></select></div>";
    	echo "</fieldset>";    
    echo "</div>";
?>
<script>
	$("#idRole_<?=$_GET["id"]?>").on('change',function() {
		// Changement du menu
		<? echo "$('#tab_gouv_".$_GET["id"]."').html('Supprimer le rôle<br/><b>'+$(this).find('option:selected').text()+'</b>');"; ?>


	});
	$("#modeSuppression_<?=$_GET["id"]?>").one('focus', function () {
		// Store the current value on focus and on change
		previous = this.value;
	}).change( function() {
		$("#option"+previous+"_<?=$_GET["id"]?>").css("display","none");
		$("#option"+this.value+"_<?=$_GET["id"]?>").css("display","");
		previous = this.value;
	});
</script>
