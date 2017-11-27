<?	

include_once("../include.php");

	if (!isset($_SESSION["currentUser"]) || $_SESSION["currentUser"]->getId()<2) {
		// Si l'utilisateur n'est pas identifié (NULL ou visiteur)
		?>
		<script>
			alert("Vous avez été déconnectés du serveur.");
			$( "#dialogStd" ).dialog( "close" );
			 location.reload();
		</script>
<?
		exit;
	}


	// POST du formulaire
	if (isset($_POST["action"])) {
	
	if ($_POST["action"]=="addMetric") {
	
		// Traite les erreurs
		
		if ($_POST["role"]=="") {
			echo "/* Erreur */\n alert('Erreur!! Veuillez choisir le ou les rôles.');$('#select_role').focus();"; exit;
		}
		if ($_POST["recurrence"]=="") {
			echo "/* Erreur */\n alert('Erreur!! Veuillez choisir la récurrence.');$('#select_recurrence').focus();"; exit;
		}
		if ($_POST["metricsTitle"]=="") {
			echo "/* Erreur */\n alert('Erreur!! Le nom n\'est pas rempli.');$('#metricsTitle').focus();"; exit;
		}
	
	
		// Crée ou met à jour la metrics
			if (isset($_POST["id"]) && $_POST["id"]!="") {
				$metric=$_SESSION["currentManager"]->loadMetric($_POST["id"]);
			} else {
				$metric=new \holacracy\Metric();
			}
			$metric->setDescription(utf8_decode($_POST["metricsText"]));
			$metric->setName(utf8_decode($_POST["metricsTitle"]));
			$metric->setShortname(utf8_decode($_POST["metricsTitle"]));
			$metric->setRoleId(utf8_decode($_POST["role"]));
			if (isset($_POST["idRoleFocus_"])) $metric->setUserId(utf8_decode($_POST["idRoleFocus_"]));
			
			if (isset($_POST["circle"]) && $_POST["circle"]!="") {
				$metric->setCircleId(utf8_decode($_POST["circle"]));
			} else {
				// Possibilité de créer des indicateurs sans cercle...
				// $role=$_SESSION["currentManager"]->loadRole($_POST["role"]);
				$metric->setCircleId("");
			}
			$metric->setRecurrenceId(utf8_decode($_POST["recurrence"]));
			
			// Type de metrics?
			if (isset($_POST["metr_numeric"])) {
				$metric->setNumeric(1);
				if (isset($_POST["metr_goal"]) && $_POST["metr_goal"]!="") $metric->setGoal($_POST["metr_goal"]); else $metric->setGoal(NULL);
			} else {
				$metric->setNumeric(0);
				$metric->setGoal(NULL);
			}
			if (isset($_POST["metr_file"])) {
					$metric->setFile(utf8_decode($_POST["metr_file"]));		
			} else {
				$metric->setFile("");
			}
			
			$_SESSION["currentManager"]->save($metric);
			
			$_POST["metric"]=$metric->getId();

		// Texte de validation
		echo T_("L'indicateur a &eacute;t&eacute; ajout&eacute;e");			
		
?>
<script>

    $( "#dialogStd" ).dialog("close");
    refreshMetrics(<?
    if (isset($_POST["circle"]) && $_POST["circle"]!="") {
		echo $_POST["circle"];
	} else {
		echo $_POST["role"];
	}
		?>);
    //$( "#dialogStd" ).dialog({ buttons: [ {text: "Fermer", click: function() { $( this ).dialog( "close" ); location.reload(); }} ] });
</script>
<?		
		}
	} // Fin du POST Action
	else 
	{  // Début affichage du formulaire
		if ((isset($_GET["role"]) && $_GET["role"]!="") || (isset($_GET["circle"]) && $_GET["circle"]!="") || (isset($_GET["id"]) && $_GET["id"]!="")) {
		
			// Initialise pour un nouveau metrics de cercle
			if (isset($_GET["circle"]) && $_GET["circle"]!="") {
				$id="";
				$circleId=$_GET["circle"];
				$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
				$recurrenceId="";
				$roleId="";
				$userId="";
				$text="";
				$title="";
				$file="";
				$numeric=1;
				$goal="";
			}
			// Initialise pour un nouveau metrics de rôle
			if (isset($_GET["role"]) && $_GET["role"]!="") {
				$id="";
				$roleId=$_GET["role"];
				$userId=$_SESSION["currentUser"]->getId();
				$role=$_SESSION["currentManager"]->loadRole($_GET["role"]);
				$recurrenceId="";
				$circleId=""; //$role->getSuperCircleId();
				$text="";
				$title="";
				$file="";
				$numeric=1;
				$goal="";
			}
			// Initialise pour l'édition d'un metrics
			if (isset($_GET["id"]) && $_GET["id"]!="") {
				$id=$_GET["id"];
				$metric=$_SESSION["currentManager"]->loadMetric($_GET["id"]);
				$circle=$metric->getCircle();
				$circleId=$metric->getCircleId();
				$recurrenceId=$metric->getRecurrenceId();
				$roleId=$metric->getRoleId();
				$userId=$metric->getUserId();
				$text=$metric->getDescription();
				$title=$metric->getName();
				$file=$metric->getFile();
				$numeric=$metric->getNumeric();
				$goal=$metric->getGoal();
			}
		
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<script src="plugins/tinymce/tinymce.min.js"></script>	
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	
<script>
				function loadUsers(id, selected, more=1) {

					// Charge les rôles de cette personnes dans ce cercle
				   var dataTab = {
					"SelectformID": "",
					"RoleID": id,
					"action": "GetFiller",
					"default":"Choisissez...",
					"selected":selected,
					"more":more
					};
					//On fait la MAJ en Ajax
					$.ajax({
						   type : "POST",
						   url : "ajax/formtriage.php",
						   data : dataTab,
							success: function(data){
							if(!$.isNumeric(data) && data!="-1"){//Si il y a des focus
								$("select#idRoleFocus_").remove();
								$("#idPersonne").append(data); //on ajoute le select focus
								$("#idPersonne").removeClass("hidefocus");
							
							} else{//Si c'est un numéro user ou -1
								
								
								$("select#idRoleFocus_").remove();
								$("#idPersonne").addClass("hidefocus");
								
							}							
							},
						  error:function(){
							  alert("Erreur Ajax");
						  } 
						});	
			}
	
</script>
<?
			

			// Affichage des infos cachées pour post du formulaire
			echo "<form id='formulaire'>";
	  		echo "<input type='hidden' id='form_target' value='/formulaires/form_metrics.php'>";
			echo "<input type='hidden' name='id' value='".$id."'>";
			echo "<input type='hidden' name='action' value='addMetric'>";
			echo "<table style='width:100%'><tr><td>";
			
		// Si nous sommes au niveau d'un cercle, affiche le choix du rôle
		if (isset($circleId) && $circleId>0 && !$roleId>0) {
			echo "<input type='hidden' name='circle' value='".$circleId."'>";
			$organisation=$circle->getOrganisation();

				//$org=$organisation->getMembers();
				//7$roles_s=$circle->getRoles(\holacracy\Role::STRUCTURAL_ROLES);
				//$roles_o=$circle->getRoles(~\holacracy\Role::STRUCTURAL_ROLES);
				
			// Si c'est le premier lien, charge tous les rôles
			if ($_SESSION["currentUser"]->getId()==$circle->getUserId()) {
				$roles_s=$circle->getRoles(\holacracy\Role::STRUCTURAL_ROLES);
				$roles_o=$circle->getRoles(~\holacracy\Role::STRUCTURAL_ROLES);
			} else {
			// Sinon, seulement les rôles de l'utilisateur courant
				$roles_s=$_SESSION["currentUser"]->getRoles($circle, \holacracy\Role::STRUCTURAL_ROLES);
				$roles_o=$_SESSION["currentUser"]->getRoles($circle, ~\holacracy\Role::STRUCTURAL_ROLES);
			}
	

			//$add=array_udiff($org,$circ,'\holacracy\User::compareUser');
			echo "<h3>Titre</h3>";   
			echo "</td><td>";
			echo "<select id='select_role' name='role' title='Hop'>";
			echo "<option value=''>".T_("Choisissez...")."</option>";
			if (count($roles) > 0) {
				echo "<optgroup label='".T_("R&ocirc;les structurels")."'></optgroup>";

				foreach($roles_s as $role) {
					echo "<option value='".$role->getId()."'";
					if ($role->getId()==$roleId) echo " selected=''";
					echo "> &nbsp;".$role->getName()."</option>";
				}
			}
			if (count($roles_o) > 0) {
				echo "<optgroup label='".T_("R&ocirc;les op&eacute;rationnels")."'></optgroup>";

				foreach($roles_o as $role) {
					echo "<option value='".$role->getId()."'";
					if ($role->getId()==$roleId) echo " selected=''";
					echo "> &nbsp;".$role->getName()."</option>";
				}
			}
			echo "</select>";
			echo "<span id='idPersonne' class='hidefocus'>&nbsp; personne en charge ";
			echo "</span>";
		} else {
			// charge le rôle pour récupérer le cercle
			echo "<input type='hidden' name='circle' value='".$circleId."'>";
			echo "<input type='hidden' name='role' value='".$roleId."'>";
			
			// Affichage de la personne en charge
			echo "<h3>Personne en charge</h3>";
			echo "</td><td>";
			echo "<span id='idPersonne' class='hidefocus'>";
			echo "</span>";
?>
	<script>
		$(function() {
			loadUsers(<? echo $roleId.($userId!=""?",".$userId:",0").",0" ?>);
		});
	</script>
<?
		}


		
			$recurrences=\holacracy\Recurrence::getAllRecurrence();
		
			echo "</td></tr><tr><td>";
			echo "<h3>".T_("R&eacute;currence")."</h3>";
			echo "</td><td>";
			echo "<select id='select_recurrence' name='recurrence'>";
			echo "<option value=''>".T_("Choisissez...")."</option>";
			foreach($recurrences as $recurrence) {
				echo "<option value='".$recurrence->getId()."'";
				if ($recurrence->getId()==$recurrenceId) echo " selected=''";
				echo "> &nbsp;".$recurrence->getLabel()."</option>";
			}
			echo "</select>";
			
			echo "</td></tr><tr><td>";
			echo "<h3>".T_("Type")."</h3>";
			echo "</td><td>";
			echo "<div><input type='checkbox' id='numeric' name='metr_numeric' value=0 ".($numeric==1?"checked":"")."> <label for='numeric'>Numérique</label><span>. Objectif: <input type='text' name='metr_goal' id='metr_goal' value='".($goal>0?$goal:"")."'></span></div>";
			echo "<div><input type='checkbox' id='file'  name='metr_file' value=1 ".($file!=""?"checked":"")."> <label for='file'>Fichier</label><span> (précisez): <input type='text' name='metr_file' id='metr_file' value='".str_replace("'","&apos;",$file)."'></span></div>";
			echo "</td></tr><tr><td>";
			echo "<h3>".T_("Nom")."</h3>";
			echo "</td><td>";
			echo "<div><input id='metricsTitle' name='metricsTitle' style='width:100%' value='".str_replace("'","&apos;",$title)."'></input></div>";
			
			echo "</td></tr><tr><td>";
			echo "<h3>".T_("Description")."</h3>";
			echo "</td><td>";
			echo "<div><textarea id='metricsText' name='metricsText' class='tinymce' style='width:100%'>".$text."</textarea></div>";
			
			echo "</td></tr></table>";
			echo "</form>";

		}
?>

		<script type="text/javascript">	
			
		// Changement du rôle concerné
			$('#select_role').change(function () {
				$("#idPersonne").addClass("hidefocus");
				var RoleIDselect = this.value; //Recuperation du user selectionné
				if($.isNumeric(RoleIDselect)) {
					loadUsers(RoleIDselect);

				} else {
					// Cache la partie rôle
					$("#idPersonne").addClass("hidefocus");
				}
				
			});

			if (typeof tinymce != "undefined") tinymce.remove();			
           $('textarea.tinymce').tinymce({
                    // Location of TinyMCE script
					menubar : false,
					plugins: "link, paste",
					extended_valid_elements : "p/div/tr/li,br/td",
                    invalid_elements : "span, table, tr, img, button, input, form, ul, li, h1, h2, h3",
					paste_auto_cleanup_on_paste : true,
					paste_remove_styles: true,
		            paste_remove_styles_if_webkit: true,
		            paste_strip_class_attributes: true,
					toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
					statusbar : false

            });
 
             // Prevent jQuery UI dialog from blocking focusin
			$(document).on('focusin', function(e) {
			    if ($(event.target).closest(".mce-window").length) {
					e.stopImmediatePropagation();
				}
			});
			
			$( "#dialogStd" ).dialog({ buttons: [{ text: "<? echo ($id>0?T_("Modifier"):T_("Ajouter")); ?>", click: function() { $( "#formulaire").submit(); } }, {text: "<? echo T_("Annuler"); ?>", click: function() { $( this ).dialog( "close" ); }} ] });
			
	  $("#formulaire").submit(function() {

		// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
	 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
	        .done(function(data, textStatus, jqXHR) {
	            if (textStatus="success")
	            {
					
	            	// Traite une éventuelle erreur
	            	if (data.indexOf("Erreur")>0) {
	            		eval(data);
	            	} else {
	            	
		            	// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
		                $("#formulaire")[0].innerHTML=data;
		                // Intérprète les scripts retournés (à vérifier si ça fonctionne)
		                eval($("#formulaire").find("script").text());
	                }
				}
	            else {
	            	// Problème d'envoi
	            	alert("Echec!");
	            
	            }
	            
	        });
	        // Bloque la procédure standard d'envoi
	        return false;
	});

        </script>
<?
	}
?>
