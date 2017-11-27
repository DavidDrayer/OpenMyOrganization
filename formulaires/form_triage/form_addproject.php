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
	echo "<table width='100%' border=0 cellspacing=0 cellpadding=0><tr><td width='50%'>";
	echo "<div>Rôle/cercle : <br/><select name='idRole"."_".$_GET["id"]."' id='idRole"."_".$_GET["id"]."'>";
	echo "<option value=''>Choisissez...</option>";
			// Charge le cercle en cours
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getRoles(\holacracy\Role::STRUCTURAL_ROLES);
    		// Affiche la liste
			echo "<optgroup label='Roles structurels'>";
			foreach ($roles as $role) {
    			
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
			echo "</optgroup'>";
  			// Charge la liste des rôles qui peuvent être modifiés
    		$roles=$circle->getRoles(\holacracy\Role::CIRCLE);
    		// Affiche la liste
			echo "<optgroup label='Cercles'>";
			foreach ($roles as $role) {
    			
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
			echo "</optgroup'>";
    		$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE);
			echo "<optgroup label='Rôles'>";
			foreach ($roles as $role) {
    			echo "<option value='".$role->getId()."'>".$role->getName()."</option>";
			}
			echo "</optgroup'>";

    	echo "</select></td><td>";
		echo "<div id='idSubRole".$_GET["id"]."' class='hidefocus'>Rôle dans le cercle : <br/></div>";
   	
    	echo "</td></tr></table></div>";
	
	echo "<div id='idRoleFocus".$_GET["id"]."' class='hidefocus'>Personne en charge : <br/></div>";

	echo "<div>Résultat attendu : <input id='nameProject_".$_GET["id"]."' type='text' name='nameProject_".$_GET["id"]."' value='$nom' style='width:100%'></div>";
	echo "<div>Description : <textarea name='descriptionProject_".$_GET["id"]."' style='width:100%'>$description</textarea></div>";
	echo "<div>Proposé par : <select id='idProposer_".$_GET["id"]."' name='idProposer_".$_GET["id"]."'>";
	
	// Affiche la liste des membres du cercle
	echo "<option value=''>Choisissez...</option>";
			// Charge la liste des rôles qui peuvent être modifiés
    		$members=$circle->getMembers();
    		// Affiche la liste
			foreach ($members as $member) {  			
    			echo "<option value='".$member->getId()."'>".$member->getUserName()."</option>";
			}
	
	
	echo "</select>";
	echo "<span id='idRoleProposer".$_GET["id"]."' class='hidefocus'>&nbsp; dans son rôle ";
	echo "</span>";
	$status=\holacracy\Project::getAllStatus();
	echo "<div>Status : <br/><select name='idStat"."_".$_GET["id"]."' id='idStat"."_".$_GET["id"]."'>";
	foreach ($status as $statu) {
		echo "<option value='".$statu->getId()."'";
		 if (isset($_GET["prst_id"]) && $_GET["prst_id"]==$statu->getId()) echo " selected";
		echo ">".$statu->getLabel()."</option>";
	}
	echo "	</select> <input style='vertical-align:middle' type='checkbox' checked id='important_".$_GET["id"]."' name='important_".$_GET["id"]."'> Marquer comme important</div>";

	
	
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
			
			function refreshFocus<?php echo $_GET["id"];?>(RoleIDselect) {
				var dataTab = {
				"SelectformID": <?php echo $_GET["id"];?>,
				"RoleID": RoleIDselect,
				"action": "GetFiller"
				};
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
			}
			
			// Changement de la personne proposante
			$('select#idProposer_<?php echo $_GET["id"];?>').change(function () {
				var UserIDselect = this.value; //Recuperation du user selectionné
				if($.isNumeric(UserIDselect)) {
					// Charge les rôles de cette personnes dans ce cercle
				   var dataTab = {
					"SelectformID": <?php echo $_GET["id"];?>,
					"CircleID": <?php echo $circle->getId()?>,
					"ProposerID": UserIDselect,
					"action": "GetRoles"
					};
					//On fait la MAJ en Ajax
					$.ajax({
						   type : "POST",
						   url : "ajax/formtriage.php",
						   data : dataTab,
							success: function(data){
							if(!$.isNumeric(data) && data!="-1"){//Si il y a des focus
								$("select#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
								$("#idRoleProposer"+<?php echo $_GET["id"];?>).append(data); //on ajoute le select focus
								$("#idRoleProposer"+<?php echo $_GET["id"];?>).removeClass("hidefocus");
								
							} else{//Si c'est un numéro user ou -1
								
								
								$("select#idRoleProposer_"+<?php echo $_GET["id"];?>).remove();
								$("#idRoleProposer"+<?php echo $_GET["id"];?>).addClass("hidefocus");
								
							}							
							},
						  error:function(){
							  alert("Erreur Ajax");
						  } 
						});						
					// Affiche la partie rôle
					$("#proposerRole"+<?php echo $_GET["id"];?>).removeClass("hidefocus");
				} else {
					// Cache la partie rôle
					$("#proposerRole"+<?php echo $_GET["id"];?>).addClass("hidefocus");
				}
				
			});
			
			// Changement du subrole
			$(document).on('change', 'select#idSubRole_<?php echo $_GET["id"];?>', function(){
				var RoleIDselect = this.value; //Recuperation du rôle selectionné
				if (RoleIDselect=="") {
					RoleIDselect = $('select#idRole_<?php echo $_GET["id"];?>').val();
				}
				refreshFocus<?php echo $_GET["id"];?>(RoleIDselect);
				
			});
			
			//Y a t il un ou plusieurs focus ?
			$('select#idRole_<?php echo $_GET["id"];?>').change(function () {
			var RoleIDselect = this.value; //Recuperation du rôle selectionné
			if($.isNumeric(RoleIDselect)) {
			   // Mise à jour de la liste des sous-rôles si c'est un cercle	
			   var dataTab = {
				"SelectformID": <?php echo $_GET["id"];?>,
				"CircleID": RoleIDselect,
				"action": "GetRoles2"
				};
				//On fait la MAJ en Ajax
				$.ajax({
						   type : "POST",
						   url : "ajax/formtriage.php",
						   data : dataTab,
							success: function(data){
							if(!$.isNumeric(data) && data!="-1"){//Si il y a des focus
								$("input#idSubRole_"+<?php echo $_GET["id"];?>).remove();
								$("select#idSubRole_"+<?php echo $_GET["id"];?>).remove();
								$("#idSubRole"+<?php echo $_GET["id"];?>).append(data); //on ajoute le select focus
								$("#idSubRole"+<?php echo $_GET["id"];?>).removeClass("hidefocus");
								
							} else{//Si c'est un numéro user ou -1
								if(data== -1){ //si pas de user affecté
								
								$("select#idSubRole_"+<?php echo $_GET["id"];?>).remove();
								$("input#idSubRole_"+<?php echo $_GET["id"];?>).remove();
								$("#idSubRole"+<?php echo $_GET["id"];?>).addClass("hidefocus");
								}else{
									
									$("select#idSubRole_"+<?php echo $_GET["id"];?>).remove(); //on enlève au cas ou le select focus
									$("input#idSubRole_"+<?php echo $_GET["id"];?>).remove();
									$("#idSubRole"+<?php echo $_GET["id"];?>).append("<input type='hidden' id='idSubRole_"+<?php echo $_GET["id"];?>+"' name='idSubRole_"+<?php echo $_GET["id"];?>+"' value='"+data+"'>"); //on ajoute le input hidden
									$("#idSubRole"+<?php echo $_GET["id"];?>).addClass("hidefocus");
									}
							}							
							},
						  error:function(){
							  alert("Erreur Ajax");
						  } 
						});				
			   // Mise à jour de la liste des focus	
			   refreshFocus<?php echo $_GET["id"];?>(RoleIDselect);
			} else{ //Par defaut on efface
			$("select#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
			$("input#idRoleFocus_"+<?php echo $_GET["id"];?>).remove();
			$("#idRoleFocus"+<?php echo $_GET["id"];?>).addClass("hidefocus");
			$("select#idSubRole_"+<?php echo $_GET["id"];?>).remove();
			$("input#idSubRole_"+<?php echo $_GET["id"];?>).remove();
			$("#idSubRole"+<?php echo $_GET["id"];?>).addClass("hidefocus");
			}
			});


			<? 
			
			
			// Mis à jour du libellé des boutons


				if (isset($_GET["project"]) && $_GET["project"]!="")
					echo "$('#tab_gouv_".$_GET["id"]."').html('Modifier le projet<br/><b>".$nom."</b>');";
				else {
					echo "$('#nameProject_".$_GET["id"]."').on ('change',function() {";
					echo "$('#tab_gouv_".$_GET["id"]."').html('Ajouter un projet<br/><b>'+$(this).val()+'</b>');";
					echo "});";
				}

			?>
			
			
		


	</script>
