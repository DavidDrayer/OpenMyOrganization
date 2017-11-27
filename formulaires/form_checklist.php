
<?
	include_once("../include.php");
	if (isset($_POST["action"])) {
	
	if ($_POST["action"]=="addChecklist") {
	
		// Traite les erreurs
		
		if (isset($_POST["role"]) && $_POST["role"]=="") {
			echo "/* Erreur */\n alert('Erreur!! Veuillez choisir le ou les rôles.');$('#select_role').focus();"; exit;
		}
		if ($_POST["recurrence"]=="") {
			echo "/* Erreur */\n alert('Erreur!! Veuillez choisir la récurrence.');$('#select_recurrence').focus();"; exit;
		}
		if ($_POST["checkListTitre"]=="") {
			echo "/* Erreur */\n alert('Erreur!! Le titre n\'est pas rempli.');$('#checkListTitre').focus();"; exit;
		}
	
	
		// Est-ce un nouveau membre? Doit-il être créé?
			if (isset($_POST["id"]) && $_POST["id"]!="") {
				$chk=$_SESSION["currentManager"]->loadChecklist($_POST["id"]);
			} else {
				$chk=new \holacracy\Checklist();
			}
			
			$chk->setDescription(utf8_decode($_POST["checkListText"]));
			$chk->setTitle(utf8_decode($_POST["checkListTitre"]));
			if (isset($_POST["role"]) && $_POST["role"]>0)
				$chk->setRoleId(utf8_decode($_POST["role"]));
			else if (isset($_POST["roleId"]) && $_POST["roleId"]>0)
				$chk->setRoleId(utf8_decode($_POST["roleId"]));
			else
				$chk->setRoleId(NULL);
			if (isset ($_POST["circle"]) && $_POST["circle"]>0) {
				$chk->setCircleId(utf8_decode($_POST["circle"]));
			} else {
				// Récupère le cercle du rôle et l'affecte
				// if ($chk->getRoleId()>0)
				$chk->setCircleId("");
			
				
			}
			if (isset($_POST["idRoleFocus_"])) $chk->setUserId(utf8_decode($_POST["idRoleFocus_"]));

			$chk->setRecurrenceId(utf8_decode($_POST["recurrence"]));
			$_SESSION["currentManager"]->save($chk);
			
			$_POST["chk"]=$chk->getId();

		// Ajoute le membre
		echo T_("La Checkliste a &eacute;t&eacute; ajout&eacute;e");			
		
?>
<script>

    $( "#dialogStd" ).dialog("close");
    refreshChecklist(<?
    if (isset($_POST["circle"]) && $_POST["circle"]!="") {
		echo $_POST["circle"];
	} else {
		echo $_POST["roleId"];
	}
	?>);

    //$( "#dialogStd" ).dialog({ buttons: [ {text: "Fermer", click: function() { $( this ).dialog( "close" ); location.reload(); }} ] });
</script>
<?		
	}} else {
if ((isset($_GET["role"]) && $_GET["role"]!="") || (isset($_GET["circle"]) && $_GET["circle"]!="") || (isset($_GET["id"]) && $_GET["id"]!="")) {
	
		// Initialise pour un nouveau metrics
		if (isset($_GET["circle"]) && $_GET["circle"]!="") {
			$id="";
			$circleId=$_GET["circle"];
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			$recurrenceId="";
			$roleId="";
			$userId="";
			$title="";
			$text="";
		}
		// Initialise pour un nouveau metrics
		if (isset($_GET["role"]) && $_GET["role"]!="") {
			$id="";
			$roleId=$_GET["role"];
			$role=$_SESSION["currentManager"]->loadRole($_GET["role"]);
			$recurrenceId="";
			$text="";
			$userId="";
			$title="";
			$circleId="";
		}
		// Initialise pour l'édition d'un metrics
		if (isset($_GET["id"]) && $_GET["id"]!="") {
			$id=$_GET["id"];
			$checklist=$_SESSION["currentManager"]->loadChecklist($_GET["id"]);
			$circleId=$checklist->getCircleId();
			if ($circleId>0) {
				$circle=$checklist->getCircle();
				
			}
			$userId=$checklist->getUserId();
			$recurrenceId=$checklist->getRecurrenceId();
			$roleId=$checklist->getRoleId();
			if (is_null($roleId)) $roleId==0;
			$text=$checklist->getDescription();
			$title=$checklist->getTitle();
		}
			?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<script src="plugins/tinymce/tinymce.min.js"></script>	
<script src="plugins/tinymce/jquery.tinymce.min.js"></script>
<script>
				function loadUsers(id, selected, more=1) {

					// Charge les rôles de cette personnes dans ce cercle
				   if (id>1) {
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
					} else {
						$("select#idRoleFocus_").remove();
						$("#idPersonne").addClass("hidefocus");
					}
			}
	
</script>	
	<?

			// Affichage des infos cachées pour post du formulaire
			echo "<form id='formulaire'>";
	  		echo "<input type='hidden' id='form_target' value='/formulaires/form_checklist.php'>";
			echo "<input type='hidden' name='circle' value='".$circleId."'>";
			echo "<input type='hidden' name='roleId' value='".$roleId."'>";
			echo "<input type='hidden' name='id' value='".$id."'>";
			echo "<input type='hidden' name='action' value='addChecklist'>";
		
			echo "<table style='width:100%'><tr><td>";
		// Si nous sommes au niveau d'un cercle, affiche le choix du rôle
		if (isset($_GET["circle"]) && $_GET["circle"]>0) {	
			$organisation=$circle->getOrganisation();

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
			echo "<h3>".T_("R&ocirc;le concern&eacute;")."</h3>";
			echo "</td><td>";
			echo "<select id='select_role' name='role'>";
			echo "<option value=''>".T_("Choisissez...")."</option>";
			echo "<option value='0' style='font-weight:bold' ".(($roleId==0 && $roleId!="")?"selected":"").">".T_("Tous les r&ocirc;les")."</option>";
			if (count($roles_s)>0) {
			echo "<optgroup label='".T_("R&ocirc;les structurels")."'></optgroup>";

			foreach($roles_s as $role) {
				echo "<option value='".$role->getId()."'";
				if ($role->getId()==$roleId) echo " selected=''";
				echo "> &nbsp;".$role->getName()."</option>";
			}
			}
			if (count($roles_o)>0) {
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
			// Affichage de la personne en charge
			echo "<h3>Personne en charge</h3>";
			echo "</td><td>";
			echo "<span id='idPersonne' class='hidefocus'>";
			echo "</span>";
		
		}
if (isset($roleId) && $roleId!="") {
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
			
			echo "<h3>".T_("Titre")."</h3>";
			echo "</td><td>";
			echo "<div><input type='text' id='checkListTitre' name='checkListTitre' style='width:100%' value='".str_replace("'","&apos;",$title)."'/></div>";
			echo "</td></tr><tr><td>";
			echo "<h3>".T_("Description")."</h3>";
			echo "</td><td>";
			echo "<div><textarea id='checkListText' name='checkListText' class='tinymce' style='width:100%'>".$text."</textarea></div>";
			echo "</form>";
			
			echo "</td></tr></table>";
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
						
          $('textarea.tinymce').tinymce({
                    // Location of TinyMCE script
					menubar : false,
					plugins: "link, paste",
					extended_valid_elements : "p/div/tr/li,br/td",
                    invalid_elements : "span, table, tr, img, button, input, form, ul, li, h1, h2, h3",
					paste_auto_cleanup_on_paste : true,
					paste_remove_styles: true,
		            paste_remove_styles_if_webkit: true,
		            paste_strip_class_attributes: "none",	
					toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
					statusbar : false

            });
 
             // Prevent jQuery UI dialog from blocking focusin
			$(document).on('focusin', function(e) {
			    if ($(event.target).closest(".mce-window").length) {
					e.stopImmediatePropagation();
				}
			});
			           
            $( "#dialogStd" ).dialog({ buttons: [{ text: "<? echo T_("Ajouter"); ?>", click: function() { $( "#formulaire").submit(); } }, {text: "<? echo T_("Annuler"); ?>", click: function() { $( this ).dialog( "close" ); }} ] });

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
