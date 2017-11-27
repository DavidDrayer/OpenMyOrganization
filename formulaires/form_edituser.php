<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	include_once("../plugins/libMiniature.php");
	
	if (!isset($_GET["user"])) {
		$user=$_SESSION["currentManager"]->loadUser($_SESSION["currentUser"]->getId());	
	} else {
		$user=$_SESSION["currentManager"]->loadUser($_GET["user"]);	
	}

	if (isset($_POST["user_firstname"])) {
		
		// Contrôle les erreurs
			if ($_POST["user_firstname"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le prénom n\'est pas rempli.');$('#user_firstname').focus();"; exit;
			}
			if ($_POST["user_lastname"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le nom n\'est pas rempli.');$('#user_lastname').focus();"; exit;
			}
			if ($_POST["user_email"]=="") {
				echo "/* Erreur */\n alert('Erreur!! L\'email n\'est pas rempli.');$('#user_email').focus();"; exit;
			}
			
		// Contrôle du mot de passe si la modification est demandée
		if (isset($_POST["chk_password"]) && $_POST["chk_password"]==1) {
			if ($_POST["old_password"]=="") {
				echo "/* Erreur */\n alert('Erreur!! L\'ancien mot de passe est vide.');$('#old_password').focus();"; exit;
			}
			if ($_POST["new_password"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le nouveau mot de passe est vide.');$('#new_password').focus();"; exit;
			}
			if ($_POST["new_password"]!=$_POST["check_password"]) {
				echo "/* Erreur */\n alert('Erreur!! Le contrôle du mot de passe est différent.');$('#check_password').focus();"; exit;
			}
			$filter=new \holacracy\Filter();
			$filter->addCriteria("userName",$user->getUserName());
			$filter->addCriteria("password",$_POST["old_password"]);
			$users= $_SESSION["currentManager"]->findUsers($filter);
			if (count($users)!=1) {
				echo "/* Erreur */\n alert('Erreur!! Le mot de passe ne correspond pas.');$('#old_password').focus();"; exit;
			}
				

		}
		


		
		// Sauve les infos
		$user=$_SESSION["currentManager"]->loadUser($_POST["user_id"]);
		$user->setFirstName(utf8_decode($_POST["user_firstname"]));
		$user->setLastName(utf8_decode($_POST["user_lastname"]));
		$user->setEmail(utf8_decode($_POST["user_email"]));
		$user->setUserLangue($_POST["user_langue"]);
		
		if (isset($_POST["chk_password"]) && $_POST["chk_password"]==1) {
			$user->setPassword(utf8_decode($_POST["new_password"]));
		}
		$_SESSION["currentManager"]->save($user);
		
		// Sauve les contacts
		if (isset($_POST["scope_ligne"])) 
		foreach ($_POST["scope_ligne"] as $ligne) {
			if (isset($_POST["cont_id_".$ligne]) && $_POST["cont_id_".$ligne]!="") {
				// Modification d'un focus/assignation
				if (isset($_POST["tyco_id_".$ligne])) {
					if ($_POST["tyco_id_".$ligne]!="" && $_POST["cont_value_".$ligne]!="") {
						$contact=$_SESSION["currentManager"]->loadContact($_POST["cont_id_".$ligne]);
						
						$contact->setType($_POST["tyco_id_".$ligne]);
						$contact->setValue(utf8_decode($_POST["cont_value_".$ligne]));
						$_SESSION["currentManager"]->save($contact);
					} else {
						$contact=$_SESSION["currentManager"]->loadContact($_POST["cont_id_".$ligne]);
						
						$_SESSION["currentManager"]->delete($contact);
					}
				}
			} else {
				if ($_POST["tyco_id_".$ligne]!="" && $_POST["cont_value_".$ligne]!="") {
					$contact=new \holacracy\Contact();
					$contact->setValue(utf8_decode($_POST["cont_value_".$ligne]));
					$contact->setType($_POST["tyco_id_".$ligne]);
					$contact->setUser($_POST["user_id"]);
					$_SESSION["currentManager"]->save($contact);
				}
			}
		}		
		
		echo "Sauvegarde effectuée";
		// Modifie les boutons
?><script>
	if(typeof refreshUser == 'function') {refreshUser(<?=$user->getId()?>)};
    $( "#dialogStd" ).dialog("close");

</script>
<?
		exit;
	}

		
		
		echo "<table width='100%'><tr><td><div id='output'>";
		if (checkMini("/images/user/".$user->getId().".jpg",150,150,"mid",1,5)) {
			echo "<img style='border:1px solid black' src='/images/user/mid/".$user->getId().".jpg'/>";
		} else if (checkMini("/images/user/0.jpg",150,150,"mid",1,5)) {
			echo "<img style='border:1px solid black' src='/images/user/mid/0.jpg'/>";
		}
		echo "</div></td>";

		// Affiche quelques infos et le menu USER			
		echo "<td style='width:100%'>";
		// Affiche les infos de base
			echo "<fieldset><legend><div id='mask1'></div><span>Image</span><div id='mask2'></div></legend>";		
		?>
	<form action="loadImage.php" method="post" enctype="multipart/form-data" id="MyUploadForm">
	<input name="ImageFile" id="imageInput" type="file" />
	<input type="submit"  id="submit-btn" value="Upload" />
	<input type="hidden" name="newImageName" value="<?=$user->getId()?>" />
	<input type="hidden" name="imageType" value="image/jpeg" />
	
	
	<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
	</form>
	
	
	<script src="scripts/jquery.form.js"></script>
	<script type="text/javascript">
	
	//function to check file size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob)
    {
        
        if( !$('#imageInput').val()) //check empty input filed
        {
            $("#output").html("Are you kidding me?");
            return false
        }
        
        var fsize = $('#imageInput')[0].files[0].size; //get file size
        var ftype = $('#imageInput')[0].files[0].type; // get file type
        

        //allow only valid image file types 
        switch(ftype)
        {
            case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
                break;
            default:
                $("#output").html("<b>"+ftype+"</b> Unsupported file type!");
                return false
        }
        
        //Allowed file size is less than 1 MB (1048576)
        if(fsize>1048576) 
        {
            $("#output").html("<b>"+fsize +"</b> Image trop volumineuse! <br />Merci de la réduire avec un éditeur d'image.");
            return false
        }
                
        $('#submit-btn').hide(); //hide submit button
        $('#loading-img').show(); //hide submit button
        $("#output").html("");  
    }
    else
    {
        //Output error to older unsupported browsers that doesn't support HTML5 File API
        $("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
        return false;
    }
}
	
	$(document).ready(function() { 
	    var options = { 
	            target:   '#output',   // target element(s) to be updated with server response 
	            beforeSubmit:  beforeSubmit,  // pre-submit callback 
	            resetForm: true        // reset the form after successful submit 
	        }; 
	        
	     $('#MyUploadForm').submit(function() { 
	            $(this).ajaxSubmit(options);  //Ajax Submit form            
	            // return false to prevent standard browser submit and page navigation 
	            return false; 
	        }); 
	});
	</script>
<?

			echo "</fieldset>";		
			echo "<form id='formulaire'>";
			echo "<input type='hidden' id='form_target' value='/formulaires/form_edituser.php'/>";
			echo "<input type='hidden' name='user_id' value='".$user->getId()."'/>";

			echo "<fieldset><legend><div id='mask1'></div><span>Informations générales</span><div id='mask2'></div></legend>";
			echo "<div><span class='omo-label-fields'>Nom:</span><span class='omo-field'>";
			echo "<input id='user_lastname' name='user_lastname' type='text' value='".str_replace("'","&#39;",$user->getLastName())."'>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Prénom:</span><span class='omo-field'>";
			echo "<input id='user_firstname' name='user_firstname' type='text' value='".str_replace("'","&#39;",$user->getFirstName())."'>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Adresse E-mail principale:</span><span class='omo-field'>";
			echo "<input type='text' id='user_email' name='user_email' value='".$user->getEmail()."'>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Langue principale:</span><span class='omo-field'>";
			$tablangue = $user->getAllLanguage(); //Recupère toutes les langues
			$languser = $user->getUserLangue(); //Récupère la langue actuel du user
			echo "<select name='user_langue'>";
			foreach ($tablangue as $value) {
				if($value == $languser){ echo "<option selected value='".$value."'>".$value."</option>";	}
				else{echo "<option value='".$value."'>".$value."</option>";}
			}
			echo "</select>";
			echo "</span></div>";
			echo "</fieldset>";
		// Affiche tous les contacts
		echo "<fieldset><legend><div id='mask1'></div><span>Informations de contact</span><div id='mask2'></div></legend>";
	
		// Affiche la description du type de contact préféré
		echo "Préférence de contact";
		echo "<textarea id='user_contact' name='user_contact'></textarea>";
	
		echo "<table id='contacts' style='width:100%' cellspacing=0><tbody>";
		$typecontacts=\holacracy\TypeContact::getAllTypeContact();
		$count=1;
		foreach ($user->getContacts() as $contact) {
			echo "<tr><td><input type='hidden' class='id' name='scope_ligne[]' value='".$count."'/><input type='hidden' name='cont_id_".$count."' value='".$contact->getId()."'/><select name='tyco_id_".$count."'><option value=''>Choisissez...</option>";
			// Charge la liste de tous les types de contact
			foreach($typecontacts as $typecontact) {
				echo "<option value='".$typecontact->getId()."'";
				if ($typecontact==$contact->getType()) echo " selected ";
				echo ">".$typecontact->getLabel()."</option>";
			}
			echo "</select></td><td width='100%'>";
			echo "<input type='text' name='cont_value_".$count."' value='".$contact->getValue()."' style='width:100%'>";
			echo "</td><td><button type='button' class='delete-button' style='padding-top:1px; padding-bottom:1px;'><img src='images/delete.png' /></button></td></tr>";
			$count++;
		}
		echo "</tbody></table>";
		echo "<button type='button' id='create-contact'>Ajouter un contact</button>";
		echo "</fieldset>";
		
		// Modification du mot de passe
		echo "<fieldset><legend><div id='mask1'></div><span>Mot de passe</span><div id='mask2'></div></legend>";

		echo "<input type='checkbox' id='chk_password' name='chk_password' value='1'/> Modifier mon mot de passe";
			echo "<div id='div_password' style='display:none'><br/><div><span class='omo-label-fields'>Ancien mot de passe:</span><span class='omo-field'>";
			echo "<input name='old_password' id='old_password' type='password' value=''>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Nouveau mot de passe:</span><span class='omo-field'>";
			echo "<input  name='new_password' id='new_password' type='password' value=''>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Confirmation du mot de passe:</span><span class='omo-field'>";
			echo "<input  name='check_password' id='check_password' type='password' value=''>";
			echo "</span></div>";
			echo "</div>";
		echo "</fieldset>";
		
		echo "</td></tr></table>";
		echo "</form>";
?>
<script>

		// Bouton pour mot de passe:
	$("#chk_password").click(function() {
      $( "#div_password" ).toggle( 'blind', {}, 500 );
    });
    
		// Boutons pour effacer une ligne
		$(".delete-button").button()
		.click(function () {
			// Efface le champ de focus
			$(this).closest("tr").find("input:text").val("");
			// Remet la valeur du champ de sélection à "choisissez..."
			$(this).closest("tr").find("select").val("");
			// Cache la ligne
			$(this).closest("tr").css( "display", "none" )
		
		});
		
        // Bouton pour ajouter un domaine
		   	$( "#create-contact" )
	      .button()
	      .click(function() {
	      	// Quelle est la dernière ligne
	      	tmp=$( "#contacts .id" ).filter(":last").val()
	      	if ($.isNumeric(tmp)) {
	      		tmp++;
	      	} else {
	      		tmp=1;
	      	}
	      	// Ajoute une ligne au tableau à éditer
	      	     $( "#contacts tbody" ).append( "<tr>" +
             		 "<td><input type='hidden' class='id' name='scope_ligne[]' value='" + tmp + "'/>" + 
					  "<input type='hidden' name='scope_id_"+tmp+"' value=''/>" +
					  "<select name='tyco_id_"+tmp+"'><option value=''>Choisissez...</option>" +
					  <?
							foreach($typecontacts as $typecontact) {
							echo "\"<option value='".$typecontact->getId()."'";
						
							echo ">".$typecontact->getLabel()."</option>\" + ";
			}				  	
					  ?>
					  "</select></td><td width='100%'><input type='text'  name='cont_value_"+tmp+"' style='width:100%' name='scope_description_"+tmp+"'/>" + "</td>" +
 					  "<td><button type='button' class='delete-button' style='padding-top:1px; padding-bottom:1px;'><img src='images/delete.png' /></button></td>" +
          		 "</tr>" );
          		 $(".delete-button").button().click(function () {			
					$(this).closest("tr").find("input:text").val("");
						$(this).closest("tr").find("select").val("");
					$(this).closest("tr").css( "display", "none" )});
	      });
	      
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

    $( "#dialogStd" ).dialog({ buttons: [{ text: "Enregistrer", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });

</script>
