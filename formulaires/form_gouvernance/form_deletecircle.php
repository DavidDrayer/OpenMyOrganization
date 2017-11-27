<?
	// Option pour choisir si c'est un tout nouveau cercle ou la transformation d'un rôle en cercle
	if (!isset($_GET["subcircle"]) && !isset($_GET["role"])) {
		echo "<div>Mode de supression : <select name='modeSuppression_".$_GET["id"]."' id='modeSuppression_".$_GET["id"]."' style='width:100%'><option value='1'>Supprimer le cercle</option><option value='2'>Transformer le cercle en rôle</option></select></div>";
	}

	echo "Nom: <select id='idCircle"."_".$_GET["id"]."' style='width:100%' name='idCircle"."_".$_GET["id"]."'>";
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
    	
    	
    	// Que faire avec les rôles de ce cercle?
    	
    	
    	    // Que faire avec ses rôles, projets, indicateurs et checklistes
		echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Options</span><div id='mask2'></div></legend>";
    	echo "<div>Rôles et sous-cercles : ";
    	echo "<select name='optRole_".$_GET["id"]."' id='optRole_".$_GET["id"]."'><option value=0>Remonter dans le cercle courant</option><option value=1>Supprimer</option></select></div>";
		echo "<div id='option1_".$_GET["id"]."'>";

    	echo "<div>Projets : ";
    	echo "<select name='optProject_".$_GET["id"]."' id='optProject_".$_GET["id"]."'><option value=0>Attribuer au 1er Lien</option><option value=1>Supprimer</option></select></div>";
     	echo "<div>Indicateurs : ";
    	echo "<select name='optInd_".$_GET["id"]."' id='optInd_".$_GET["id"]."'><option value=0>Attribuer au 1er Lien</option><option value=1>Supprimer</option></select></div>";
     	echo "<div>Check-listes : ";
    	echo "<select name='optCheck_".$_GET["id"]."' id='optCheck_".$_GET["id"]."'><option value=0>Attribuer au 1er Lien</option><option value=1>Supprimer</option></select></div>";
		echo "</div>";
    	echo "</fieldset>";    
?>
<script>
	$("#idCircle_<?=$_GET["id"]?>").on('change',function() {
		// Changement du menu
		<? echo "$('#tab_gouv_".$_GET["id"]."').html('Supprimer le cercle<br/><b>'+$(this).find('option:selected').text()+'</b>');"; ?>


	});
				var previous;


	$("#modeSuppression_<?=$_GET["id"]?>").one('focus', function () {
		// Store the current value on focus and on change
		previous = this.value;
	}).change( function() {
		$("#option"+previous+"_<?=$_GET["id"]?>").css("display","none");
		$("#option"+this.value+"_<?=$_GET["id"]?>").css("display","");
		previous = this.value;
	});
</script>
