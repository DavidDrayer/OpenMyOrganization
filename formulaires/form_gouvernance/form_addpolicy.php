<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<?

	if (!isset($_GET["policy"])) {
		echo "<div>Type de politique : <select name='policyType_".$_GET["id"]."' id='policyType_".$_GET["id"]."' style='width:100%'><option value='1'>Politique de cercle</option><option value='2'>Lien transverse</option></select></div>";
	}
	echo "<div id='option1_".$_GET["id"]."'>";

	if (!isset($_GET["policy"]) || substr($_GET["policy"],0,2)!="LT") {
		$titre=$description="";
		if (isset($_GET["policy"])) {
			// Modification d'un cercle existant
			echo "<input type='hidden' name='policy_".$_GET["id"]."' value='".$_GET["policy"]."' >";
			// Chargement de l'objet et initialisation des variables
			$policy=$_SESSION["currentManager"]->loadPolicy($_GET["policy"]);
			$titre=$policy->getTitle();
			$description=$policy->getDescription();
		}
	
		echo "<div>Titre : <input id='titrePolicy_".$_GET["id"]."' type='text' name='titrePolicy_".$_GET["id"]."' value='".str_replace("\"","&quot;",$titre)."' style='width:100%'></div>";
		echo "<div>Description : <textarea name='descriptionPolicy_".$_GET["id"]."' class='tinymce' style='width:100%'>$description</textarea></div>";
	}
	if (!isset($_GET["policy"])) {
		echo "</div>";
		echo "<div id='option2_".$_GET["id"]."' style='display:none'>";
	}

	function listeCircle($circle,$indent="") {
	    	$roles=$circle->getRoles(\holacracy\Role::CIRCLE);
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$indent.$role->getName()."</option>";
				listeCircle($role,$indent." &nbsp; &nbsp;");
			}
	}
	if (!isset($_GET["policy"]) || substr($_GET["policy"],0,2)=="LT") {
	
	if (!isset($_GET["policy"])) {
		echo "<div>Rôle ou Cercle source</div>";
		echo "<select class='sourceCircle' id='idSourceCircle"."_".$_GET["id"]."' name='idSourceCircle"."_".$_GET["id"]."'>";
		echo "<option value=''>Choisissez la source...</option>";
					echo "<optgroup label='Rôles'></optgroup>";

					$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			    	$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE | \holacracy\Role::STRUCTURAL_ROLES);
		    		// Affiche la liste
					foreach ($roles as $role) {
		    			echo "<option ";
						echo "value=R_'".$role->getId()."'>".$role->getName()."</option>";
					}
				echo "<optgroup label='Sous-cercles'></optgroup>";

				// Charge le cercle en cours
				$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
   				echo "<option value='".$circle->getId()."'>".$circle->getName()."</option>";
				// Charge la liste des rôles qui peuvent être modifiés
	 			listeCircle($circle," &nbsp; &nbsp;");
	    	echo "</select>";
	    	echo "<div style='display:none' class='userDiv'><div>Personne en charge</div>";
	    	echo "<select class='idUser' id='idUser"."_".$_GET["id"]."' name='idUser"."_".$_GET["id"]."'></select></div>";
		echo "<div>Cercle cible</div>";
		echo "<select id='idTargetCircle"."_".$_GET["id"]."' name='idTargetCircle"."_".$_GET["id"]."'>";
		echo "<option value=''>Choisissez la cible...</option>";
		
				// Charge le cercle en cours
				$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
   				echo "<option disabled='' value='".$circle->getId()."'>".$circle->getName()."</option>";
				// Charge la liste des rôles qui peuvent être modifiés
	    		listeCircle($circle," &nbsp; &nbsp;");
	    	echo "</select>";
			
    	} else {
    		// Association d'un lien transverse à un rôle
    		
    		// Charge la policy pour récpérer les valeurs courantes
    		$policy=$_SESSION["currentManager"]->loadRole(substr($_GET["policy"],2));

			echo "<div>Rôle associé à ce lien transverse:</div>";
			echo "<input type='hidden' name='policy_".$_GET["id"]."' value='".substr($_GET["policy"],2)."' >";
	
			echo "<select class='sourceRole' id='idSourceRole"."_".$_GET["id"]."' name='idSourceRole"."_".$_GET["id"]."'>";
			echo "<option value=''>Auncun rôle, l'ensemble du cercle est représenté</option>";
					// Charge le cercle en cours
					$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			    	$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE | \holacracy\Role::STRUCTURAL_ROLES);
		    		// Affiche la liste
					foreach ($roles as $role) {
		    			echo "<option ";
						if ($policy->getSourceId()==$role->getId()) echo "selected ";
						echo "value='".$role->getId()."'>".$role->getName()."</option>";
					}


	    	echo "</select>";
    			echo "<div style='' class='userDiv'><div>Personne en charge</div>";
	    	echo "<select class='idUser' id='idUser"."_".$_GET["id"]."' name='idUser"."_".$_GET["id"]."'><option>test</option></select></div>";

    	}
	}


	echo "</div>";
	
?>

		<script type="text/javascript">

			var previous;


			$("#policyType_<?=$_GET["id"]?>").one('focus', function () {
		        // Store the current value on focus and on change
		        previous = this.value;
		    }).change( function() {
				$("#option"+previous+"_<?=$_GET["id"]?>").css("display","none");
				$("#option"+this.value+"_<?=$_GET["id"]?>").css("display","");
				previous = this.value;
			});

			<? 
			// Mis à jour du libellé des boutons
			if (isset($_GET["policy"])) {
				echo "$('#tab_gouv_".$_GET["id"]."').html('Modifier la politique<br/><div class=omo_legende_onglet>".str_replace("'","&apos;",$titre)."</div>');";
			} else {
				echo "$('#titrePolicy_".$_GET["id"]."').on ('change',function() {";
				echo "$('#tab_gouv_".$_GET["id"]."').html('Ajouter la politique<br/><div class=omo_legende_onglet>'+$(this).val()+'</div>');";
				echo "});";
			}
			?>
				
			
			// Charge la liste des users lorsque change la sélection
			$('.sourceCircle').change(function () {
				$(this).next().css("display","none");
				$(this).next().find('.idUser option').remove();
				if ($(this).val()=="<?=$_GET["circle"] ?>") {
					$(this).next().css("display","");
					$.ajax({ type: "GET",   
						 url: "/ajax/listeUser.php?idCircle=<?=$_GET["circle"] ?>&format=select",   
						 async: false,
						 success : function(text)
						 {
							 response= decodeURIComponent(escape(text));
						 }
					});
					$(this).next().find('.idUser').append(response);
				}
				if ($(this).val().substring(0,2)=="R_") {
					$(this).next().css("display","");
					$.ajax({ type: "GET",   
						 url: "/ajax/listeUser.php?idRole="+($(this).val().substring(2))+"&format=select",   
						 async: false,
						 success : function(text)
						 {
							 response= decodeURIComponent(escape(text));
						 }
					});
					$(this).next().find('.idUser').append(response);				}
			});
			
            $('textarea.tinymce').tinymce({
                    // Location of TinyMCE script
                    script_url : 'plugins/tinymce/tinymce.min.js',
					menubar : false,
					plugins: "link, paste",
                    invalid_elements : "table, tr, td, img, button, input, form",
					paste_auto_cleanup_on_paste : true,
					paste_remove_styles: true,
		            paste_remove_styles_if_webkit: true,
		            paste_strip_class_attributes: true,					toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
					statusbar : false

            });
	</script>
