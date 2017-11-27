<?
	include_once("../include.php");
	if (isset($_POST["action"])) {
	
	if ($_POST["action"]=="addMember") {
	
		// Traite les erreurs
		if (isset($_POST["radio1"]) && $_POST["radio1"]=="0") {
			if (!isset($_POST["user"]) || $_POST["user"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Sélectionnez un utilisateur.');"; exit;
			}
		} else
		if (isset($_POST["radio1"]) && $_POST["radio1"]=="1") {
			if (!isset($_POST["user2"]) || $_POST["user2"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Sélectionnez un utilisateur.');"; exit;
			}
		} else
		if (isset($_POST["radio1"]) && $_POST["radio1"]=="2") {

			if ($_POST["user_username"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le pseudo n\'est pas rempli.');$('#user_username').focus();"; exit;
			} else {
				// S'assure que le pseudo n'est pas encore utilisé
				$filter=new \holacracy\Filter();
				$filter->addCriteria("userName",$_POST["user_username"]);
				$users= $_SESSION["currentManager"]->findUsers($filter);
				if (count($users)>0) {
					echo "/* Erreur */\n alert('Erreur!! Ce pseudo est déjà utilisé par un autre utilisateur.');$('#user_username').focus();"; exit;
				}
				
			}
			if ($_POST["user_firstname"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le prénom n\'est pas rempli.');$('#user_firstname').focus();"; exit;
			}
			if ($_POST["user_lastname"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le nom n\'est pas rempli.');$('#user_lastname').focus();"; exit;
			}
			if ($_POST["user_email"]=="") {
				echo "/* Erreur */\n alert('Erreur!! L\'email n\'est pas rempli.');$('#user_email').focus();"; exit;
			} else {
				// S'assure du bon format de l'e-mail
				if (!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL)) {
					echo "/* Erreur */\n alert('Erreur!! L\'email n\'a pas un bon format.');$('#user_email').focus();"; exit;

				} else {
				//S'assure que l'email n'existe pas encore 
				$filter=new \holacracy\Filter();
				$filter->addCriteria("email",$_POST["user_email"]);
				$users= $_SESSION["currentManager"]->findUsers($filter);
				if (count($users)>0) {
					echo "/* Erreur */\n alert('Erreur!! Cette adresse est déjà utilisée par un autre utilisateur.');$('#user_email').focus();"; exit;
				}
				}
			}
		} else {
				echo "/* Erreur */\n alert('Erreur!! Choisissez une option de création.');"; exit;
		}

	
		// Est-ce un nouveau membre? Doit-il être créé?
		if ($_POST["radio1"]=="1") {
			$_POST["user"]=$_POST["user2"];
		} else 
		if ($_POST["radio1"]=="2") {

			$user=new \holacracy\User();
			$user->setUserName(utf8_decode($_POST["user_username"]));
			$user->setFirstName(utf8_decode($_POST["user_firstname"]));
			$user->setLastName(utf8_decode($_POST["user_lastname"]));
			$user->setEmail(utf8_decode($_POST["user_email"]));
			$user->setManager($_SESSION["currentManager"]);
			// Crée un nouveau password, l'attribue à l'utilisateur et l'envoi par e-mail
			$pwgen = new \security\PWGen();
			$newPassword= $pwgen->generate();
			$user->setPassword($newPassword);
			
			if ($user->sendMessage('Nouveau compte utilisateur sur '.$_SERVER["HTTP_HOST"],'Bienvenue dans le monde de la gouvernance intégrative!\n\nVous pouvez dès à présent utiliser le logiciel OMO, qui vous permet de clarifier le fonctionnement de votre organisation et de mettre à portée d\'un simple click de souris les tâches, les projets, les rôles et les politiques de votre groupe.\nPour en savoir plus, vous pouvez visualiser le tutoriel suivant: https://www.youtube.com/watch?v=xYmH1_mclPQ.\n\nVoici vos informations de connection: \nAdresse du site: http://'.$_SERVER["HTTP_HOST"].'\nNom d\'utilisateur: '.$user->getUserName().'\nAdresse e-mail: '.$user->getEmail().'\nMot de passe : '.$newPassword.'\n\nNous vous souhaitons beaucoup de plaisir au sein de votre organisation, en espérant vous aider, à travers l\'utilisation de ce logiciel, à passer plus fluidement des idées aux résultats.')) { 
				$_SESSION["currentManager"]->save($user);
			} else {
				echo "Le message n'a pas pu être envoyé. L'utilisateur n'a pas été créé.";
				exit;
			}			
			
			$_POST["user"]=$user->getId();
		}

		// Ajoute le membre
		$_SESSION["currentManager"]->addMemberCircle($_POST["user"],$_POST["circle"]);
		echo "Le membre a été ajouté";			
		
?>
<script>

    $( "#dialogStd" ).dialog("close");
    refreshMembers(<?=$_POST["circle"];?>);
    //$( "#dialogStd" ).dialog({ buttons: [ {text: "Fermer", click: function() { $( this ).dialog( "close" ); location.reload(); }} ] });
</script>
<?		
	}} else {
	if (isset($_GET["circle"]) && $_GET["circle"]!="") {
			$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);
			$organisation=$circle->getOrganisation();

			// Affichage des infos cachées pour post du formulaire
			echo "<form id='formulaire'>";
	  		echo "<input type='hidden' id='form_target' value='/formulaires/form_addmember.php'>";
			echo "<input type='hidden' name='circle' value='".$_GET["circle"]."'>";
			echo "<input type='hidden' name='action' value='addMember'>";
			
				$org=$organisation->getMembers();
				$circ=$circle->getMembers();

			$add=array_udiff($org,$circ,'\holacracy\User::compareUser');
			echo "<h3>Choisissez le membre à ajouter</h3>";
			echo "<div><input type='radio' selected name='radio1' value='0' checked > Un membre de l'organisation ";
	
			echo "<select id='select_user' name='user'>";
			echo "<option value=''>Choisissez...</option>";
			foreach($add as $member) {
				echo "<option value='".$member->getId()."'>".$member->getFirstName()." ".$member->getLastName()."</option>";
			}
			echo "</select>";
			echo "</div>";
			// Champ pour rechercher un membre, par nom, prénom, user ou mail
			echo "<div><input type='radio' selected name='radio1' value='1' > Chercher un membre existant ";
			echo "<input id='user_find' id='user_find' name='user_find' type='text' value='' placeholder='Nom, user ou mail...'>";
			echo "</span>";
			
			
			echo "<select id='select_user2' name='user2' style='display:none'>";
			echo "</select></div>";
			echo "<input type='radio' id='radio_new' selected name='radio1' value='2'> Créer un nouveau membre</div>";
			// Lorsqu'on sélectionne la création d'un nouveau membre, affiche le formulaire de saisi
			// On entrera entre autre: nom, prénom, username et adresse e-mail. Le mot de passe sera généré automatiquement.
			echo "<div id='new_member' style='display:none'><br><fieldset><legend><div id='mask1'></div><span>Nouveau membre</span><div id='mask2'></div></legend>";
			echo "<div><span class='omo-label-fields'>Utilisateur (Pseudo):</span><span class='omo-field'>";
			echo "<input id='user_username' name='user_username' type='text' value=''>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Nom:</span><span class='omo-field'>";
			echo "<input id='user_lastname' name='user_lastname' type='text' value=''>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Prénom:</span><span class='omo-field'>";
			echo "<input id='user_firstname' name='user_firstname' type='text' value=''>";
			echo "</span></div>";
			echo "<div><span class='omo-label-fields'>Adresse E-mail principale:</span><span class='omo-field'>";
			echo "<input type='text' id='user_email' name='user_email' value=''>";
			echo "</span></div>";
			echo "</fieldset></div>";			
			
			
			echo "</form>";
			
			
	}
	
?>
<script>
$("#formulaire").ready(function(){

$("#select_user").focus(function () {
	$("input:radio[name='radio1']")[0].checked = true;
		$("#new_member").css("display","none");

});
$("#select_user2").focus(function () {
	$("input:radio[name='radio1']")[1].checked = true;
		$("#new_member").css("display","none");

});
$("#user_find").focus(function () {
	$("input:radio[name='radio1']")[1].checked = true;
		$("#new_member").css("display","none");


});
// Un texte est écrit dans le champ recherche
$("#user_find").keyup(function () {
	 	$.post("ajax/listeUser.php", $("#formulaire").serialize()) 
        .done(function(data, textStatus, jqXHR) {
            if (textStatus="success")
            {
             	// Traite une éventuelle erreur
            	if (data.indexOf("Erreur")>0) {
            		eval(data);
            	} else {
					if (data=="") {
						$("#select_user2").hide();
					} else {
						// Affiche les données en retour dans le select 
						$("#select_user2").html("<option value=''>Choisissez...</option>"+data);
						$("#select_user2").show();
						// Intérprète les scripts retournés (à vérifier si ça fonctionne)
						eval($("#formulaire").find("script").text());
					}
	            }
			}
            else {
            	// Problème d'envoi
            	alert("Echec!");
            
            }
        });
});

$("input:radio[name='radio1']").change(function() {
	$("#new_member").css("display",($(this).val()=="2")?"":"none");
});


$( "#dialogStd" ).dialog({ buttons: [{ text: "Ajouter", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });

 $("#formulaire").submit(function() {
	 // Désactive le bouton "ajouter" pour ne pas envoyer 2 fois la même info
	 $(".ui-dialog-buttonpane button:contains('Ajouter')").button("disable");
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
	            $(".ui-dialog-buttonpane button:contains('Ajouter')").button("enable");
			}
            else {
            	// Problème d'envoi
            	alert("Echec de l'envoi du formulaire!");
            	$(".ui-dialog-buttonpane button:contains('Ajouter')").button("enable");
            
            }
        });
        // Bloque la procédure standard d'envoi
        return false;
});

});
</script>
<?
	}
?>
