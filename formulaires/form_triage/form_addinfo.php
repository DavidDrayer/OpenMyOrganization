<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<?
	$nom=$description="";
	if (isset($_GET["project"])) {
		// Modification d'un cercle existant
		echo "<input type='hidden' name='idProject_".$_GET["id"]."' value='".$_GET["project"]."' >";
		// Chargement de l'objet et initialisation des variables
		$project=$_SESSION["currentManager"]->loadProject($_GET["project"]);
		$nom=$project->getName();
		$description=$project->getDescription();
	}

	echo "<div class='light'>";
/*	echo "<table style='width:100%'  cellspacing=0 cellpadding=0><tr><td style='width:50%; padding-right:10px;'>";
	echo "<div>Rôle/cercle : <br/><select name='idRole"."_".$_GET["id"]."' id='idRole"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getRoles();
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
			echo "<option value='0'>Personne(s) en particulier</option>";
    	echo "</select></div>";
	echo "</td><td style='width:50%'>";	
	echo "<div id='idRoleFocus".$_GET["id"]."' class='hidefocus'>Personne en charge : <br/></div>";
	echo "</td></tr></table>"; */
	echo "<div>Description : <textarea style='resize: vertical; max-height:200px; width:100%' id='textInfo_".$_GET["id"]."' type='text' name='textInfo_".$_GET["id"]."'>".$nom."</textarea></div>";
	//echo "<div>Description : <textarea name='descriptionProject_".$_GET["id"]."' style='width:100%'>$description</textarea></div>";
	echo "<table style='width:100%;' cellspacing=0 cellpadding=0><tr><td style='width:50%; padding-right:10px;'>";

		echo "<div>Amené par : <br/><select name='idProposer"."_".$_GET["id"]."' id='idProposer"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getMembers();
    		// Affiche la liste
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getUserName()."</option>";
			}
    	echo "</select></div>";
	echo "</td><td style='width:50%'>";	
		
	echo "<div id='idRoleProposer".$_GET["id"]."' class='hidefocus'>Dans son rôle : <br/></div>";
	
	// echo "<div id='idProjectProposer".$_GET["id"]."' class='hidefocus'>Attacher à un projet : <br/></div>";
	echo "</td></tr></table>";

	
?>

	</div> 
		<script type="text/javascript">
		
            $('textarea.tinymce').tinymce({
                    // Location of TinyMCE script
                    script_url : 'plugins/tinymce/tinymce.min.js',
					menubar : false,
					toolbar: "undo redo | bold italic | bullist numlist outdent indent",
					statusbar : false

            });
			
			//Y a t il un ou plusieurs focus ?
			$('select#idRole_<?php echo $_GET["id"];?>').change(function () {
			var RoleIDselect = this.value; //Recuperation du rôle selectionné

			if($.isNumeric(RoleIDselect)) {
				if (RoleIDselect==0) {
					 var dataTab = {
					"SelectformID": <?php echo $_GET["id"];?>,
					"RoleID": <?=$_GET["circle"]?>,
					"action": "GetMembers"
					};
				} else {
				   var dataTab = {
					"SelectformID": <?php echo $_GET["id"];?>,
					"RoleID": RoleIDselect,
					"action": "GetFiller"
					};
				}
				//On fait la MAJ en Ajax
				$.ajax({
						   type : "POST",
						   url : "ajax/formtriage.php",
						   data : dataTab,
							success: function(data){
							if(!$.isNumeric(data)){//Si il y a des focus
								$("input#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
								$("select#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
								$("#idRoleFocus"+<?php echo $_GET["id"];?>).append(data); //on ajoute le select focus
								$("#idRoleFocus"+<?php echo $_GET["id"];?>).removeClass("hidefocus");
							} else{//Si c'est un numéro user ou -1
								if(data== -1){ //si pas de user affecté
								$("select#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
								$("input#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
								$("#idRoleFocus"+<?php echo $_GET["id"];?>).addClass("hidefocus");
								}else{
									$("select#idRoleFocus_"+<?php echo $_GET["id"];?>).remove(); //on enlève au cas ou le select focus
									$("input#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
									$("#idRoleFocus"+<?php echo $_GET["id"];?>).append("<input type='hidden' id='idRoleFocus_"+<?php echo $_GET["id"];?>+"' name='idRoleFocus_"+<?php echo $_GET["id"];?>+"' value='"+data+"'>"); //on ajoute le input hidden
									$("#idRoleFocus"+<?php echo $_GET["id"];?>).addClass("hidefocus");
									}
							}							
							},
						  error:function(){
							  alert("Erreur Ajax");
						  } 
						});
			} else{ //Par defaut on efface
			$("select#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
			$("input#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
			$("#idRoleFocus"+<?php echo $_GET["id"];?>).addClass("hidefocus");
			}
			});
			
			// ***********************************************************
			// Affichage de la liste des projets si un rôle est sélectionné
			$( "#idRoleProposer<?php echo $_GET["id"];?>" ).on( "change", "select#idRoleProposer_<?php echo $_GET["id"];?>", function() {

			var ProposerRoleIDselect = this.value; //Recuperation du rôle selectionné

			if($.isNumeric(ProposerRoleIDselect)) {
				   var dataTab = {
					"SelectformID": <?php echo $_GET["id"];?>,
					"RoleID": ProposerRoleIDselect,
					"action": "GetProjects"
					};
				//On fait la MAJ en Ajax
				$.ajax({
						   type : "POST",
						   url : "ajax/formtriage.php",
						   data : dataTab,
							success: function(data){
							if(!$.isNumeric(data) && data!=""){//Si il y a des focus
								$("input#idProjectProposer_"+<?php echo $_GET["id"];?>).remove();
								$("select#idProjectProposer_"+<?php echo $_GET["id"];?>).remove();
								$("#idProjectProposer"+<?php echo $_GET["id"];?>).append(data); //on ajoute le select focus
								$("#idProjectProposer"+<?php echo $_GET["id"];?>).removeClass("hidefocus");
							} 	 else {
								//$("select#idProjectProposer_"+<?php echo $_GET["id"];?>).remove();
								//$("input#idProjectProposer_"+<?php echo $_GET["id"];?>).remove();
								$("#idProjectProposer"+<?php echo $_GET["id"];?>).addClass("hidefocus");
							}				
							},
						  error:function(){
							  alert("Erreur Ajax");
						  } 
						});
			} else{ //Par defaut on efface
			$("select#idProjectProposer_"+<?php echo $_GET["id"];?>).remove();
			$("input#idProjectProposer_"+<?php echo $_GET["id"];?>).remove();
			$("#idProjectProposer"+<?php echo $_GET["id"];?>).addClass("hidefocus");
			}				
								
			});
			
			// ***********************************************************
			// Affichage de la liste des rôles si un membre est sélectionné
			$('select#idProposer_<?php echo $_GET["id"];?>').change(function () {
			var ProposerIDselect = this.value; //Recuperation du rôle selectionné

			if($.isNumeric(ProposerIDselect)) {
				   var dataTab = {
					"SelectformID": <?php echo $_GET["id"];?>,
					"ProposerID": ProposerIDselect,
					"CircleID": <?=$_GET["circle"]?>,
					"action": "GetRoles"
					};
				//On fait la MAJ en Ajax
				$.ajax({
						   type : "POST",
						   url : "ajax/formtriage.php",
						   data : dataTab,
							success: function(data){
							if(!$.isNumeric(data)){//Si il y a des focus
								$("input#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
								$("select#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
								$("#idRoleProposer"+<?php echo $_GET["id"];?>).append(data); //on ajoute le select focus
								$("#idRoleProposer"+<?php echo $_GET["id"];?>).removeClass("hidefocus");
							} 					
							},
						  error:function(){
							  alert("Erreur Ajax");
						  } 
						});
			} else{ //Par defaut on efface
			$("select#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
			$("input#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
			$("#idRoleProposer"+<?php echo $_GET["id"];?>).addClass("hidefocus");
			}
			});



			<? 
			
			
			// Mis à jour du libellé des boutons


				if (isset($_GET["project"]) && $_GET["project"]!="")
					echo "$('#tab_gouv_".$_GET["id"]."').html('Modifier une info<br/><b>".$nom."</b>');";
				else {
					echo "$('#textInfo_".$_GET["id"]."').on ('change',function() {";
					echo "$('#tab_gouv_".$_GET["id"]."').html('Ajouter une point info<br/><b>'+($(this).val().length>20?$(this).val().substring(0, 20)+'...':$(this).val())+'</b>');";
					echo "});";
				}

			?>
			
			
		


	</script>
