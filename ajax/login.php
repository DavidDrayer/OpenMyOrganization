<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");
	
	if (isset($_POST["user_login"])) {
		// Instantiation du gestionnaire de base de donnée
		$manager=new \datamanager\SqlManager($dbh);
		// Cherche si l'utilisateur existe
		$filter=new \holacracy\Filter();
		$filter->addCriteria("userName",$_POST["user_login"]);
		$filter->addCriteria("password",$_POST["user_password"]);
		$users= $manager->findUsers($filter);
		if (count($users)==1) {
			echo "ok";
			$_SESSION["currentUser"]=$users[0];
			$_SESSION["currentUser"]->setLastConnexion();
			$_SESSION["currentManager"]->save($_SESSION["currentUser"]);
			
			// Stock un cookie si demandé
			if (isset($_POST["remember_me"])) {
				if ($_POST["remember_me"]=='remember_me') {
				setcookie("RememberUser", $_SESSION["currentUser"]->getId(), time()+3600*24*30, "/");					
				}
			}
			
		} else {
			// Génère un message d'erreur
			echo "Utilisateur inconnu ou mot de passe incorrect";
			
		}

	} else {
		if (isset($_POST["user_lostPassword"])) {
			$manager=new \datamanager\SqlManager($dbh);
			// Cherche si l'utilisateur existe
		
			$filter=new \holacracy\Filter();
			if (strpos($_POST["user_lostPassword"],"@")===false) {
				$filter->addCriteria("userName",$_POST["user_lostPassword"]);
			} else {
				$filter->addCriteria("email",$_POST["user_lostPassword"]);
			}
			$users= $manager->findUsers($filter);			// Cherche l'utilisateur
			if (count($users)==1) {
				// Crée un nouveau password, l'attribue à l'utilisateur et l'envoi par e-mail
				$pwgen = new \security\PWGen();
    			$newPassword= $pwgen->generate();
				$users[0]->setPassword($newPassword);
				
				if ($users[0]->sendMessage('Mot de passe de remplacement pour OMO','Vous avez fait une demande pour un nouveau mot de passe pour le logiciel OMO. Voici vos informations de connexion: \n\nUtilisateur:'.$users[0]->getUserName().'\nNnouveau mot de passe : '.$newPassword.'\n\nPour des questions de sécurité, veuillez le modifier en vous rendant dans l\'édition de votre profil.')) { 
					$manager->save($users[0]);
					echo "ok";
				} else {
					echo "Le message n'a pas été envoyé";
				}
			} else {
				echo "Utilisateur inconnu.";
			}
		} else { 
			// Par défaut, déconnexion
			echo "ko";
			unset($_SESSION["currentUser"]);
			setcookie("RememberUser", "", 1);

		}
	}
?>
