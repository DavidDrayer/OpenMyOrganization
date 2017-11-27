<?
			// Charge le cercle en cours
	$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
	echo "<div>D�placer depuis</div>";
	echo "<select id='idCircle2"."_".$_GET["id"]."' name='idCircle2"."_".$_GET["id"]."'>";
	echo "<option value='".$_GET["circle"]."'>Ce cercle</option>";
			// Charge la liste des r�les qui peuvent �tre modifi�s
    		$roles=$circle->getRoles(\holacracy\Role::CIRCLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'";
    			if (count($role->getRoles(\holacracy\Role::STANDARD_ROLE))==0) echo " disabled ";
    			echo ">".$role->getName()."</option>";
			}
    	echo "</select>";
 	echo "<div><div>R�le � d�placer</div>";

	echo "<select class='idRole' id='idRole"."_".$_GET["id"]."' name='idRole"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez un r�le...</option>";
			// Charge la liste des r�les qui peuvent �tre modifi�s
    		$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
    	echo "</select></div>";

	echo "<div>Vers</div>";
	echo "<select id='idCircle"."_".$_GET["id"]."' name='idCircle"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez le cercle de destination...</option>";
	if ($circle->getSuperCircleId()>0) echo "<option value='".$circle->getSuperCircleId()."'>Cercle sup�rieur</option>";
	echo "<option value='".$_GET["circle"]."'>Ce cercle</option>";
			
			// Charge la liste des r�les qui peuvent �tre modifi�s
    		$roles=$circle->getRoles(\holacracy\Role::CIRCLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
    	echo "</select>";
    	
    	// Que faire avec ses projets, indicateurs et checklistes
    	echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Options</span><div id='mask2'></div></legend>";
      	echo "<div>Projets : ";
    	echo "<select name='optProject_".$_GET["id"]."' id='optProject_".$_GET["id"]."'><option value=0>D�placer avec le r�le</option><option value=1>Attribuer au 1er Lien du cercle source</option><option value=2>Supprimer</option></select></div>";
     	echo "<div>Indicateurs : ";
    	echo "<select name='optInd_".$_GET["id"]."' id='optInd_".$_GET["id"]."'><option value=0>D�placer avec le r�le</option><option value=1>Attribuer au 1er Lien du cercle source</option><option value=2>Supprimer</option></select></div>";
     	echo "<div>Check-listes : ";
    	echo "<select name='optCheck_".$_GET["id"]."' id='optCheck_".$_GET["id"]."'><option value=0>D�placer avec le r�le</option><option value=1>Attribuer au 1er Lien du cercle source</option><option value=2>Supprimer</option></select></div>";
    	echo "</fieldset>";    

?>
<script>
	$("#idRole_<?=$_GET["id"]?>").on('change',function() {
		// Changement du menu
		<? echo "$('#tab_gouv_".$_GET["id"]."').html('D�placer le r�le<br/><b>'+$(this).find('option:selected').text()+'</b>');"; ?>


	});
	$("#idCircle2_<?=$_GET["id"]?>").on('change',function() {
		// Rafrechi la liste des r�les
			
				$(this).next().find('.idRole option').remove();
				$tmp=$(this).next().find('.idRole');
					$.ajax({ type: "GET",   
						 url: "/ajax/listeRole.php?idCircle="+($(this).val())+"&format=select",   
						 async: false,
						 success : function(text)
						 {
							 
							 //response= decodeURIComponent(escape(text));
							 $tmp.append("<option value=''>Choisissez un r�le...</option>"+text);
						 }
					});
					//$(this).next().find('.idRole').append(response);
	

	});
</script>
