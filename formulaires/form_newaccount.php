<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");
	
	if (isset($_POST["form_action"])) {
		if ($_POST["form_action"]=="newAccount") {
			// Contrôle la validité des données
			if (!isset($_POST["form_email"]) || $_POST["form_email"]=="") {
				echo "/* Erreur */\n alert('Erreur!! L\'email n\'est pas rempli.');$('#form_email').focus();"; exit;
			}
			// Sauve le nouveau profil
			$user=new \holacracy\User();
			$user->setUserName(utf8_decode($_POST["form_pseudo"]));
			$user->setFirstName(utf8_decode($_POST["form_firstname"]));
			$user->setLastName(utf8_decode($_POST["form_lastname"]));
			$user->setEmail(utf8_decode($_POST["form_email"]));
			$user->setManager($_SESSION["currentManager"]);
			
			// Crée un nouveau password, l'attribue à l'utilisateur et l'envoi par e-mail
			$pwgen = new \security\PWGen();
			$newPassword= $pwgen->generate();
			$user->setPassword($newPassword);
			
			// Envoi un e-mail pour l'activation du compte
			if ($user->sendMessage('Nouveau compte utilisateur sur '.$_SERVER["HTTP_HOST"],'Bienvenu dans le monde de la gouvernance intégrative!\n\nVous pouvez dès à présent utiliser le logiciel OMO, qui vous permet de clarifier le fonctionnement de votre organisation et de mettre à portée d\'un simple click de souris les tâches, les projets, les rôles et les politiques de votre groupe.\n\nVoici vos informations de connection: \nAdresse du site: http://'.$_SERVER["HTTP_HOST"].'\nNom d\'utilisateur: '.$user->getUserName().'\nAdresse e-mail: '.$user->getEmail().'\nMot de passe : '.$newPassword.'\n\nNous vous souhaitons beaucoup de plaisir au sein de votre organisation, en espérant vous aider, à travers l\'utilisation de ce logiciel, à passer plus fluidement des idées aux résultats.')) { 
				$_SESSION["currentManager"]->save($user);
			} else {
				echo "Le message n'a pas pu être envoyé. L'utilisateur n'a pas été créé.";
				exit;
			}					
			
			// Affiche un message de confirmation 
			echo "<p>Merci, le compte a bien été créé.</p>";
			
			echo "<p>Un e-mail a été envoyé à l'adresse ".$_POST["form_email"]." pour l'activation du compte. Veuillez contrôler votre boîte de réception.</p>";
			
			// Change les boutons du dialogue si il existe
			// Ne sait pas comment faire ça... comment référencer le dialogue
			// echo "<script>.closest().dialog('option','buttons', {"Download": function () {...}, "Not now": function () {...} });</script>";
			
			// Ne va pas plus loin, pour ne pas réafficher le formulaire
			exit;
		}
	} 
	
	// Champs cachés pour l'action
	echo "<input type='hidden' name='form_target' id='form_target' value='/formulaires/form_newaccount.php'/>";
	echo "<input type='hidden' name='form_action' id='form_action' value='newAccount'/>";
	
	// Affiche un formulaire de saisie
			echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Inscription</span><div id='mask2'></div></legend>";
				// Nom de l'org
				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Votre nom</div>".
				"<div class='omo-field'><input style='width:80%' type='text' id='form_lastname' name='form_lastname' value='' placeholder='par exemple: Dupont'></div>".
				"</div>";

				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Votre prénom</div>".
				"<div class='omo-field'><input style='width:80%' type='text' id='form_firstname' name='form_firstname' value='' placeholder='par exemple: Jean-Robert'></div>".
				"</div>";

				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Votre pseudo</div>".
				"<div class='omo-fieldhelp'>Devrait permettre à vos collègues de vous identifier clairement tout en étant plus court à afficher que votre nom complet.</div>".
				"<div class='omo-field'><input style='width:80%' type='text' id='form_pseudo' name='form_pseudo' value='' placeholder='par exemple: DupontJR'></div>".
				"</div>";

				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Votre e-mail</div>".
				"<div class='omo-fieldhelp'>Vos informations de connexion (mot de passe) seront envoyées à cette adresse.</div>".
				"<div class='omo-field'><input style='width:80%' type='text' id='form_email' name='form_email' value='' placeholder='par exemple: dupont@monentreprise.org'></div>".
				"</div>";
				
				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<input type='checkbox' id='form_cg'>".
				"J'accepte les <a href='cg.htm' target='_blank'>conditions d'utilisation</a> et de <a href='confidentialite.htm' target='_blank'>confidentialité</a> d'OpenMyOrganization". 
				"</div>";
				echo "<div style='text-align:right; margin-right:20%'><button id='btn_createAccount'>Créer le compte!</button></div>";
				
			echo "</fieldset>";
	

?>
