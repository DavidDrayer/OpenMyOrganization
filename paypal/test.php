<?php
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");
	
	include("fonction_api.php"); // On importe la page créée précédemment
	$requete = construit_url_paypal(); // Construit les options de base
	
	print_r($_POST);

	$abonnement=$_SESSION["currentManager"]->loadSubscriptionType($_POST["suscr"]);

	// Définition de l'élément acheté
	$description=$abonnement->getName()." dès le ".$_POST["date"];
	$prix=$abonnement->getPrice();


	// La fonction urlencode permet d'encoder au format URL les espaces, slash, deux points, etc.)
	$requete = $requete."&METHOD=SetExpressCheckout".
            "&CANCELURL=".urlencode("http://dev.openmyorganization.com/paypal/cancel.php").
            "&RETURNURL=".urlencode("http://dev.openmyorganization.com/paypal/return.php").
            "&AMT=".$prix.
            "&CURRENCYCODE=CHF".
            "&DESC=".urlencode($description).
            "&LOCALECODE=FR";
           // "&HDRIMG=".urlencode("http://www.siteduzero.com/Templates/images/designs/2/logo_sdz_fr.png");

	$ch = curl_init($requete);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$resultat_paypal = curl_exec($ch);

	if (!$resultat_paypal)
		{echo "<p>Erreur</p><p>".curl_error($ch)."</p>";}
	else
	{
		$liste_param_paypal = recup_param_paypal($resultat_paypal); // Lance notre fonction qui dispatche le résultat obtenu en un array

		// Si la requête a été traitée avec succès
		if ($liste_param_paypal['ACK'] == 'Success')
		{
			// Ok, ça a l'air de marcher... avant de quitter cette page, on sauve les infos
			$transaction = new \security\Transaction();
			$transaction->setTocken($liste_param_paypal['TOKEN']);
			$transaction->setSubscriptionId($_POST["suscr"]);
			$transaction->setStartDate($_POST["date"]);
			$transaction->setPrice($prix);
			$transaction->setOrganisationId($_POST["org"]);
			$transaction->setUserId($_SESSION["currentUser"]);
			$_SESSION["currentManager"]->save($transaction);
			
			// Redirige le visiteur sur le site de PayPal
			header("Location: https://www.sandbox.paypal.com/webscr&cmd=_express-checkout&token=".$liste_param_paypal['TOKEN']."&useraction=commit");
					exit();
		}
		else // En cas d'échec, affiche la première erreur trouvée.
		{echo "<p>Erreur de communication avec le serveur PayPal.<br />".$liste_param_paypal['L_SHORTMESSAGE0']."<br />".$liste_param_paypal['L_LONGMESSAGE0']."</p>";}     
	}
	curl_close($ch);
?>
