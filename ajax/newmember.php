<?
	// Se connecte à la base de données
	include_once("../include.php");



	if (isset($_POST["mail"])) {
	// Contrôle la validité de l'e-mail
	if (!filter_var($_POST["mail"], FILTER_VALIDATE_EMAIL)) {		
		echo "alert ('L\'adresse [".$_POST["mail"]."] n\'est pas valide');";
		exit;
	}
	// Contrôle que cette adresse e-mail ne soit pas déjà utilisée
	$filter=new \holacracy\Filter();
	$filter->addCriteria("email",$_POST["mail"]);
	$users= $_SESSION["currentManager"]->findUsers($filter);
	if (count($users)>0) {
		echo "/* Erreur */\n alert('Erreur!! Cette adresse est déjà utilisée par un autre utilisateur.');"; 
		exit;
	}
	

	// Crée un utilisateur avec uniquement son adresse e-mail
	$user=new \holacracy\User();
	$user->setEmail($_POST["mail"]);
	$user->setActive(0);
	$user->setCode(md5(time().$_POST["mail"]));
	$_SESSION["currentManager"]->save($user);
	
	// Envoie un e-mail pour activer le compte
	$content ="Bienvenue sur OpenMyOrganization!<br><br>".
		"Vous avez fait la demande pour créer un compte afin d'accéder au logiciel en ligne OpenMyOrganization qui permet de rendre transparentes et accessibles les informations nécessaires à la bonne pratique de la gouvernance partagée.<br><br>".
		"Pour finaliser votre inscription, veuillez compléter votre profil en cliquant sur le lien suivant: http://dev.openmyorganization.com/index.php?code=".$user->getCode()."<br><br>".
		"S'il s'agit d'une erreur et que vous n'avez pas demandé à vous inscrire, vous pouvez cliquer sur ce lien afin de supprimer votre adresse e-mail définitivement de notre base de données, ceci AVANT d'avoir complété votre profil : http://dev.openmyorganization.com/index.php?code=".$user->getCode()."&action=delete<br><br>".
		"Nous vous souhaitons la bievenue dans l'univers de la gouvernance partagée!<br><br>".
		"L'équipe de développement";
	if ($user->sendMessage("Votre inscription sur OpenMyOrganization", $content)) {
		// Si tout est ok, affiche un message de confirmation et efface le champ dans l'adresse.
		
		echo "alert ('Un message vous a été envoyé à l\'adresse ".$_POST["mail"].". Veuillez contrôler votre boîte de réception et suivre la procédure indiquée pour finaliser votre inscription.');";
	} else {
		echo "alert ('Une erreur s\'est produite');";
	}
} else 
if (isset($_POST["id"])) {
	// Vérifie les données postées
	if (!isset($_POST["username"]) || $_POST["username"]=="") {
		echo "alert ('Username invalide.');";
		exit;
	}
	if (!isset($_POST["lastname"]) || $_POST["lastname"]=="") {
		echo "alert ('Nom invalide.');";
		exit;
	}
	if (!isset($_POST["firstname"]) || $_POST["firstname"]=="") {
		echo "alert ('Prénom invalide.');";
		exit;
	}
	if (!isset($_POST["password"]) || $_POST["password"]=="") {
		echo "alert ('Mot de passe invalide.');";
		exit;
	}
	if (!isset($_POST["verif"]) || $_POST["verif"]!=$_POST["password"]) {
		echo "alert ('Vérifier votre mot de passe.');";
		exit;
	}
	// Ajoute un user
	$user=$_SESSION["currentManager"]->loadUser($_POST["id"]);
	$user->setUserName($_POST["username"]);
	$user->setLastName($_POST["lastname"]);
	$user->setFirstName($_POST["firstname"]);
	$user->setPassword($_POST["password"]);
	
	$user->setActive(1);
	$_SESSION["currentManager"]->save($user);	
	echo "alert('Votre inscription a été complétée. Veuillez vous connecter.')\n";
	echo "document.location='/'";
	
} else 
	echo "alert ('Commande inconnue');";
?>
