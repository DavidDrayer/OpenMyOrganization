<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<?
	$nom=$raison="";
	$isSource=$isTarget=false;
	if (isset($_GET["subcircle"])) {
		// Modification d'un cercle existant
		echo "<input type='hidden' name='idCircle_".$_GET["id"]."' value='".$_GET["subcircle"]."' >";
		// Chargement de l'objet et initialisation des variables
		$circle=$_SESSION["currentManager"]->loadCircle($_GET["subcircle"]);
		$nom=$circle->getName();
		$raison=$circle->getPurpose();

	}
	if (isset($_GET["role"]) && $_GET["role"]!="") {
		// Modification d'un cercle existant
		echo "<input type='hidden' name='role_".$_GET["id"]."' value='".$_GET["role"]."' >";
		// Chargement de l'objet et initialisation des variables
		$circle=$_SESSION["currentManager"]->loadRole($_GET["role"]);
		$nom=$circle->getName();
		$raison=$circle->getPurpose();

		$isTarget=($circle->getSourceId()>0 || $circle->getSourceCircleId()>0);


	}
	echo "<div class='light'>";
	
	// Option pour choisir si c'est un tout nouveau cercle ou la transformation d'un rôle en cercle
	if (!isset($_GET["subcircle"]) && !isset($_GET["role"])) {
		echo "<div>Mode de création : <select name='modeCreation_".$_GET["id"]."' id='modeCreation_".$_GET["id"]."' style='width:100%'><option value='1'>Nouveau cercle</option><option value='2'>Transformer un rôle en cercle</option></select></div>";
	}
	echo "<div id='option1_".$_GET["id"]."'>";
	echo "<div>Nom : <input id='nomCircle_".$_GET["id"]."' type='text' name='nomCircle_".$_GET["id"]."' value=\"".str_replace("\"","&quot;",$nom)."\" style='width:100%' ".($isTarget?"disabled":"")."></div>";
	echo "<div>Raison d'être : <textarea name='purposeCircle_".$_GET["id"]."' style='width:100%' ".($isTarget?"disabled":"").">$raison</textarea></div>";

	// Accordéon pour les redevabilités et autre
	echo "<div class='accordion_".$_GET["id"]."'>";
	echo "	   <h3>Redevabilités</h3>";
	echo "	   <div>";
	
	// Affiche la liste des redevabilités
?>

		  <table id="users<?="_".$_GET["id"]?>"  style='width:100%'>

		    <tbody>
<?
	if (isset($circle)) {
		$count=1;
		foreach ($circle->getAccountabilities() as $accountability) {
			if ($accountability->getRoleId()!=$circle->getId()) {
				// Redevabilités héritées, et donc inmodifiables
				echo "<tr ><td style='width:100%'>";
				echo "<div nowrap=''><span>".$accountability->getDescription()."</span></div>";
				// Le bouton pour éditer
				echo "</td><td><button type='button' class='btn_edit' value='".$count."' disabled>Editer</button></td><td><button type='button' class='btn_delete' disabled><img src='images/delete.png' /></button>";
				echo "</td></tr>";
				
			} else {
				echo "<tr id='trline_".$_GET["id"]."_".$count."'><td style='width:100%'><input type='hidden' class='id' name='ligne_".$_GET["id"]."[]' value='".$count."'/><input type='hidden' name='id_".$_GET["id"]."_".$count."' value='".$accountability->getId()."'/>";
				echo "<div id='line_".$_GET["id"]."_".$count."' nowrap=''><span id='txt_".$_GET["id"]."_".$count."'>".$accountability->getDescription()."</span></div>";
				// Le bouton pour éditer
				echo "</td><td><button type='button' class='btn_edit' id='btn_".$_GET["id"]."_".$count."' value='".$count."'>Editer</button></td><td><button type='button' class='btn_delete' id='btnd_".$_GET["id"]."_".$count."' value='".$count."'><img src='images/delete.png' /></button>";
				echo "</td></tr>";
				$count++;
			}
		}
	}
?>
			</tbody>
		  </table>
		<button type="button" id="create-accountability<?="_".$_GET["id"]?>">Ajouter une redevabilité</button>

<?
	echo "</div></div>";
	echo "<div class='accordion_".$_GET["id"]."'>";
	echo "	   <h3>Domaines</h3>";
	echo "	   <div>";
?>

		  <table id="domains<?="_".$_GET["id"]?>"  style='width:100%'>

		    <tbody>
<?
	if (isset($circle)) {
		$count=1;
		foreach ($circle->getScopes() as $scope) {
			if ($scope->getRoleId()!=$circle->getId()) {
			
			echo "<tr><td style='width:100%'>";
				echo "<div nowrap=''><span >".$scope->getDescription()."</span></div>";
				// Le bouton pour éditer
				echo "</td><td><button type='button' class='scope_btn_edit' value='".$count."' disabled>Editer</button></td><td><button type='button' class='scope_btn_delete' disabled><img src='images/delete.png' /></button>";
				echo "</td></tr>";
				$count++;
			} else {

				echo "<tr id='scope_trline_".$_GET["id"]."_".$count."'><td style='width:100%'><input type='hidden' class='id' name='scope_ligne_".$_GET["id"]."[]' value='".$count."'/><input type='hidden' name='scope_id_".$_GET["id"]."_".$count."' value='".$scope->getId()."'/>";
				echo "<div id='scope_line_".$_GET["id"]."_".$count."' nowrap=''><span id='scope_txt_".$_GET["id"]."_".$count."'>".$scope->getDescription()."</span></div>";
				// Le bouton pour éditer
				echo "</td><td><button type='button' class='scope_btn_edit' id='scoope_btn_".$_GET["id"]."_".$count."' value='".$count."'>Editer</button></td><td><button type='button' class='scope_btn_delete' id='scope_btnd_".$_GET["id"]."_".$count."' value='".$count."'><img src='images/delete.png' /></button>";
				echo "</td></tr>";
				$count++;
			}
		}
	}
?>
			</tbody>
		  </table>
		<button type="button" id="create-scope<?="_".$_GET["id"]?>">Ajouter un domaine</button>

<?
	echo "</div></div></div>";	
	echo "<div id='option2_".$_GET["id"]."' style='display:none'>";
	echo "<br/><div>Choisissez le rôle à convertir:</div>";
	echo "<select name='idCopyRole_".$_GET["id"]."' id='idCopyRole_".$_GET["id"]."'>";
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
    	echo "<div><br/>Options: ";
    	echo "<div>Focus : ";
    	echo "<select name='optFocus_".$_GET["id"]."' id='optFocus_".$_GET["id"]."'><option value=0>Transformer en membres du cercle</option><option value=1>Transformer en rôles</option><option value=2>Supprimer</option></select></div>";
     	echo "<div>Projets : ";
    	echo "<select name='optProject_".$_GET["id"]."' id='optProject_".$_GET["id"]."'><option value=0>Conserver dans le super-cercle</option><option value=1>Dupliquer dans le sous-cercle</option><option value=2>Déplacer dans le sous-cercle</option></select></div>";
     	echo "<div>Indicateurs : ";
    	echo "<select name='optInd_".$_GET["id"]."' id='optInd_".$_GET["id"]."'><option value=0>Conserver dans le super-cercle</option><option value=1>Dupliquer dans le sous-cercle</option><option value=2>Déplacer dans le sous-cercle</option></select></div>";
     	echo "<div>Check-listes : ";
    	echo "<select name='optCheck_".$_GET["id"]."' id='optCheck_".$_GET["id"]."'><option value=0>Conserver dans le super-cercle</option><option value=1>Dupliquer dans le sous-cercle</option><option value=2>Déplacer dans le sous-cercle</option></select></div>";
    	echo "</div>";
    	
	echo "</div>";
	
	
?>

	</div> 
		<script type="text/javascript">

			<? 
			
			echo "$('.accordion_".$_GET["id"]."').accordion({collapsible: true,heightStyle: 'content', active: false});";
			
			// Mis à jour du libellé des boutons
			if (isset($_GET["subcircle"])) {
				echo "$('#tab_gouv_".$_GET["id"]."').html('Modifier le cercle<br/><div class=omo_legende_onglet>".str_replace("'","&apos;",$nom)."</div>');";
			} if (isset($_GET["role"])) {
				if ($_GET["role"]!="")
					echo "$('#tab_gouv_".$_GET["id"]."').html('Modifier le rôle<br/><div class=omo_legende_onglet>".str_replace("'","&apos;",$nom)."</div>');";
				else {
					echo "$('#nomCircle_".$_GET["id"]."').on ('change',function() {";
					echo "$('#tab_gouv_".$_GET["id"]."').html('Ajouter le rôle<br/><div class=omo_legende_onglet>'+$(this).val()+'</div>');";
					echo "});";
				}
			} else {
				echo "$('#nomCircle_".$_GET["id"]."').on ('change',function() {";
				echo "$('#tab_gouv_".$_GET["id"]."').html('Ajouter le cercle<br/><div class=omo_legende_onglet>'+$(this).val()+'</div>');";
				echo "});";
			}
			?>
			
			
			var previous;


			$("#modeCreation_<?=$_GET["id"]?>").one('focus', function () {
		        // Store the current value on focus and on change
		        previous = this.value;
		    }).change( function() {
				$("#option"+previous+"_<?=$_GET["id"]?>").css("display","none");
				$("#option"+this.value+"_<?=$_GET["id"]?>").css("display","");
				previous = this.value;
			});
						
            $('textarea.tinymce').tinymce({
                    // Location of TinyMCE script
                    script_url : 'plugins/tinymce/tinymce.min.js',
					menubar : false,
					plugins: "paste",
 					extended_valid_elements : "p/div/tr/li,br/td",
                    invalid_elements : "span, table, tr, img, button, input, form, ul, li",
					paste_auto_cleanup_on_paste : true,
					paste_remove_styles: true,
		            paste_remove_styles_if_webkit: true,
		            paste_strip_class_attributes: true,					toolbar: "undo redo | bold italic | bullist numlist outdent indent",
					statusbar : false

            });

   // Bouton pour éditer une redevabilité existante (remplace le texte par un champ éditable        
    $( ".scope_btn_edit" )
	      .button()
	      .click(function() {   
	      	// Remplace le text par un champ éditable
	      	num=$(this).attr("value");
			//Recupère le scope
			var scopeedit = $('span#scope_txt_<?=$_GET["id"]?>_'+num).html();	
			scopeedit = scopeedit.replace("'","&apos;"); //Remplace les apostrophes
	      	$( "div#scope_line<?="_".$_GET["id"]?>_"+num).replaceWith( "<input type='text' name='scope_description<?="_".$_GET["id"]?>_"+num+"' style='width:100%' value='"+scopeedit+"'/>" );
			$(this).attr("disabled", true).addClass("ui-state-disabled");
		});
   $( ".scope_btn_delete" )
	      .button()
	      .click(function() {   
	      	// Remplace le text par un champ éditable
	      	num=$(this).attr("value");
	      	$( "div#scope_line<?="_".$_GET["id"]?>_"+num).replaceWith( "<input type='text' name='scope_description<?="_".$_GET["id"]?>_"+num+"' style='width:100%' value=''/>" );
			$( "tr#scope_trline<?="_".$_GET["id"]?>_"+num).css("display","none");
			$(this).attr("disabled", true).addClass("ui-state-disabled");
		});

        // Bouton pour ajouter un domaine
		   	$( "#create-scope<?="_".$_GET["id"]?>" )
	      .button()
	      .click(function() {
	      	// Quelle est la dernière ligne
	      	tmp=$( "#domains<?="_".$_GET["id"]?> .id" ).filter(":last").val()
	      	if ($.isNumeric(tmp)) {
	      		tmp++;
	      	} else {
	      		tmp=1;
	      	}
	      	// Ajoute une ligne au tableau à éditer
	      	     $( "#domains<?="_".$_GET["id"]?> tbody" ).append( "<tr>" +
             		 "<td><input type='hidden' class='id' name='scope_ligne<?="_".$_GET["id"]?>[]' value='" + tmp + "'/>" + 
					  "<input type='hidden' name='scope_id<?="_".$_GET["id"]?>_"+tmp+"' value=''/>" +
					  "<input type='text' name='scope_description<?="_".$_GET["id"]?>_"+tmp+"' style='width:100%'/>" + "</td>" +
           		 "</tr>" );
	      });
	      
   // Bouton pour supprimer une redevabilité existante (remplace le texte par un champ éditable caché       
    $( ".btn_delete" )
	      .button()
	      .click(function() {   
	      	// Remplace le text par un champ éditable
	      	num=$(this).attr("value");
	      	$( "div#line<?="_".$_GET["id"]?>_"+num).replaceWith( "<input type='text' name='description<?="_".$_GET["id"]?>_"+num+"' style='width:100%' value=''/>" );
			$( "tr#trline<?="_".$_GET["id"]?>_"+num).css("display","none");
			$(this).attr("disabled", true).addClass("ui-state-disabled");
		});


    // Bouton pour éditer une redevabilité existante (remplace le texte par un champ éditable        
    $( ".btn_edit" )
	      .button()
	      .click(function() {   
	      	// Remplace le text par un champ éditable
	      	num=$(this).attr("value");
			//Recupère la redevabilité
			var redevaedit = $('span#txt_<?=$_GET["id"]?>_'+num).html();
			redevaedit = redevaedit.replace("'","&apos;"); //Remplace les apostrophes
	      	$( "div#line<?="_".$_GET["id"]?>_"+num).replaceWith( "<input type='text' name='description<?="_".$_GET["id"]?>_"+num+"' style='width:100%' value='"+redevaedit+"'/>" );
			$(this).attr("disabled", true).addClass("ui-state-disabled");
		});
	
        // Bouton pour ajouter une redevabilité
		   	$( "#create-accountability<?="_".$_GET["id"]?>" )
	      .button()
	      .click(function() {
	      	// Quelle est la dernière ligne
	      	tmp=$( "#users<?="_".$_GET["id"]?> .id" ).filter(":last").val()
	      	if ($.isNumeric(tmp)) {
	      		tmp++;
	      	} else {
	      		tmp=1;
	      	}
	      	// Ajoute une ligne au tableau à éditer
	      	     $( "#users<?="_".$_GET["id"]?> tbody" ).append( "<tr>" +
             		 "<td><input type='hidden' class='id' name='ligne<?="_".$_GET["id"]?>[]' value='" + tmp + "'/>" + 
					  "<input type='hidden' name='id<?="_".$_GET["id"]?>_"+tmp+"' value=''/>" +
					  "<input type='text' name='description<?="_".$_GET["id"]?>_"+tmp+"' style='width:100%'/>" + "</td>" +
           		 "</tr>" );
	      });
	</script>
