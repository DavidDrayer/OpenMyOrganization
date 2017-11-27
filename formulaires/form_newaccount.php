<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");
	
	if (isset($_POST["form_action"])) {
		if ($_POST["form_action"]=="newAccount") {
			// Contr�le la validit� des donn�es
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
			
			// Cr�e un nouveau password, l'attribue � l'utilisateur et l'envoi par e-mail
			$pwgen = new \security\PWGen();
			$newPassword= $pwgen->generate();
			$user->setPassword($newPassword);
			
			// Envoi un e-mail pour l'activation du compte
			if ($user->sendMessage('Nouveau compte utilisateur sur '.$_SERVER["HTTP_HOST"],'Bienvenu dans le monde de la gouvernance int�grative!\n\nVous pouvez d�s � pr�sent utiliser le logiciel OMO, qui vous permet de clarifier le fonctionnement de votre organisation et de mettre � port�e d\'un simple click de souris les t�ches, les projets, les r�les et les politiques de votre groupe.\n\nVoici vos informations de connection: \nAdresse du site: http://'.$_SERVER["HTTP_HOST"].'\nNom d\'utilisateur: '.$user->getUserName().'\nAdresse e-mail: '.$user->getEmail().'\nMot de passe : '.$newPassword.'\n\nNous vous souhaitons beaucoup de plaisir au sein de votre organisation, en esp�rant vous aider, � travers l\'utilisation de ce logiciel, � passer plus fluidement des id�es aux r�sultats.')) { 
				$_SESSION["currentManager"]->save($user);
			} else {
				echo "Le message n'a pas pu �tre envoy�. L'utilisateur n'a pas �t� cr��.";
				exit;
			}					
			
			// Affiche un message de confirmation 
			echo "<p>Merci, le compte a bien �t� cr��.</p>";
			
			echo "<p>Un e-mail a �t� envoy� � l'adresse ".$_POST["form_email"]." pour l'activation du compte. Veuillez contr�ler votre bo�te de r�ception.</p>";
			
			// Change les boutons du dialogue si il existe
			// Ne sait pas comment faire �a... comment r�f�rencer le dialogue
			// echo "<script>.closest().dialog('option','buttons', {"Download": function () {...}, "Not now": function () {...} });</script>";
			
			// Ne va pas plus loin, pour ne pas r�afficher le formulaire
			exit;
		}
	} 
	
	// Champs cach�s pour l'action
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
				"<div class='omo-label'>Votre pr�nom</div>".
				"<div class='omo-field'><input style='width:80%' type='text' id='form_firstname' name='form_firstname' value='' placeholder='par exemple: Jean-Robert'></div>".
				"</div>";

				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Votre pseudo</div>".
				"<div class='omo-fieldhelp'>Devrait permettre � vos coll�gues de vous identifier clairement tout en �tant plus court � afficher que votre nom complet.</div>".
				"<div class='omo-field'><input style='width:80%' type='text' id='form_pseudo' name='form_pseudo' value='' placeholder='par exemple: DupontJR'></div>".
				"</div>";

				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Votre e-mail</div>".
				"<div class='omo-fieldhelp'>Vos informations de connexion (mot de passe) seront envoy�es � cette adresse.</div>".
				"<div class='omo-field'><input style='width:80%' type='text' id='form_email' name='form_email' value='' placeholder='par exemple: dupont@monentreprise.org'></div>".
				"</div>";
				
				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<input type='checkbox' id='form_cg'>".
				"J'accepte les <a href='cg.htm' target='_blank'>conditions d'utilisation</a> et de <a href='confidentialite.htm' target='_blank'>confidentialit�</a> d'OpenMyOrganization". 
				"</div>";
				echo "<div style='text-align:right; margin-right:20%'><button id='btn_createAccount'>Cr�er le compte!</button></div>";
				
			echo "</fieldset>";
	

?>
