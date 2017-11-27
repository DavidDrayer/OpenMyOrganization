<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<?
	header('Content-Type: text/html; charset=iso-8859-1');
	ob_start();
	include_once ($_SERVER["DOCUMENT_ROOT"]."/onlineEdit/db.php");
	// et se connecte à la base de données
	$dbh =  connectDb(); 
	
	function fo_get_domain($url) {     
		$dots = substr_count($url, '.');     
		$domain = '';      
		for ($end_pieces = $dots; $end_pieces > 0; $end_pieces--) 
		{         
			$test_domain = end(explode('.', $url, $end_pieces));          
			if (dns_check_record($test_domain, 'A')) {
				$domain = $test_domain;             
				break;         
			}     
		}      
		return $domain;
	 }  

	if (isset($_POST["fa_mail"])) {
		
		 
		$my_domain = fo_get_domain($_SERVER["SERVER_NAME"]);  
		$to      = translate("formationagenda_adresse_envoi","info@".$my_domain);
		$to2      = $_POST["fa_mail"];
		
		// Contrôle la validité du formulaire
		if ($_POST["fa_cg"]=="" || $_POST["fa_cg"]=="0") {
			header("HTTP/1.0 401 Unauthorized"); 
			echo translate("formationagenda_saisie_invalide_3", "Vous devez accepter les conditions générales.");
			exit;
		} 

		if ($_POST["fa_mail"]=="" || !filter_var($_POST["fa_mail"], FILTER_VALIDATE_EMAIL) ) {
			header("HTTP/1.0 401 Unauthorized"); 
			echo translate("formationagenda_saisie_invalide_1", "Veuillez entrer une adresse e-mail valide.");
			exit;
		} 

		if ($_POST["fa_nom"]=="" || $_POST["fa_prenom"]==""  ) {
			header("HTTP/1.0 401 Unauthorized"); 
			echo translate("formationagenda_saisie_invalide_2", "Veuillez renseigner votre nom et votre prénom.");
			exit;
		} 
		if ($_POST["fa_ville"]=="" ) {
			header("HTTP/1.0 401 Unauthorized"); 
			echo translate("formationagenda_saisie_invalide_4", "Veuillez renseigner au minimum votre ville de résidence.");
			exit;
		} 
	
			// Récupère la position géolocalisée
			$address=utf8_decode(utf8_decode($_POST["fa_adresse"]).", ".utf8_decode($_POST["fa_npa"])." ".utf8_decode($_POST["fa_ville"]).", ".utf8_decode($_POST["fa_pays"]));
			$address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
			$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
			$response = file_get_contents($url);
			$json = json_decode($response,TRUE); //generate array object from the response from the web
			$lat=$json['results'][0]['geometry']['location']['lat'];
			$long=$json['results'][0]['geometry']['location']['lng'];
		
	
			// Contrôle si l'adresse e-mail existe déjà
			$query="select * from t_contact where cont_mail='".$_POST["fa_mail"]."'";
			$result = mysql_query($query, $dbh);
			if (mysql_num_rows($result)>0) {
				// Si oui, complète ou met à jour les informations dans la table Contact
				$query="update t_contact set cont_pub='".$_POST["fa_ml"]."' ,cont_nom='".utf8_decode($_POST["fa_nom"])."', cont_prenom='".utf8_decode($_POST["fa_prenom"])."', cont_adresse='".utf8_decode($_POST["fa_adresse"])."', cont_npa='".utf8_decode($_POST["fa_npa"])."', cont_ville='".utf8_decode($_POST["fa_ville"])."', cont_pays='".utf8_decode($_POST["fa_pays"])."', cont_lat='".$lat."', cont_long='".$long."' where cont_id=".mysql_result($result,0,"cont_id");
				mysql_query($query, $dbh);
				$id=mysql_result($result,0,"cont_id");
				
			} else {
				// Sinon, ajoute une entrée
				$query="insert into t_contact (cont_pub, cont_mail, cont_prenom, cont_nom, cont_adresse, cont_npa, cont_ville, cont_pays, cont_lat, cont_long) values ('".$_POST["fa_ml"]."','".$_POST["fa_mail"]."','".utf8_decode($_POST["fa_nom"])."','".utf8_decode($_POST["fa_prenom"])."','".utf8_decode($_POST["fa_adresse"])."','".utf8_decode($_POST["fa_npa"])."','".utf8_decode($_POST["fa_ville"])."','".utf8_decode($_POST["fa_pays"])."','".$lat."','".$long."')";
				$result = mysql_query($query, $dbh);
				if ($result>0)
				{ 				
					$id=mysql_insert_id();					
				} else {
					// Sinon problème avec la connexion de la base de donnée
					header("HTTP/1.0 401 Unauthorized"); 
					echo translate("formationagenda_envoi_rate", "Problème avec la base de donnée. Vos informations n'ont pas pu être enregistrées. Essayez plus tard.");
					exit;
				}
			}
			// Lit les infos sur la formation pour réfiger le mail de confirmation
			$query="select * from t_date_formation left join t_formation on (t_date_formation.form_id=t_formation.form_id) where t_date_formation.dafo_id=".$_POST["fa_id"];
			$result = mysql_query($query, $dbh);
			if ($result>0 && mysql_num_rows($result)>0) {
				$oDate = new DateTime(mysql_result($result, $i, "dafo_date"));
				$wd=array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi");
				$msg_formation="Formation: ".mysql_result($result,0,"form_titre");
				$msg_formation.="\n";
				$msg_formation.="Date: ".$wd[$oDate->format("w")]." ".$oDate->format("d.m.Y à H:i").(mysql_result($result, $i, "dafo_duree")!=""?" (".mysql_result($result, $i, "dafo_duree").")":" (".mysql_result($result, $i, "form_duree").")");
				$msg_formation.="\n";
				$msg_formation.="Lieu: ".mysql_result($result, $i, "dafo_ville").(mysql_result($result, $i, "dafo_adresse")!=""?" (".mysql_result($result, $i, "dafo_adresse").")":"");

			} else {
					$msg_formation="Formation non valable sélectionnée";
			}
			
			// Contrôle que la personne ne soit pas déjà inscrite
			//$query="select * from t_contact_formation where "
			$msg_contact=utf8_decode($_POST["fa_nom"])." ".utf8_decode($_POST["fa_prenom"]);
			$msg_contact.="\r\n";
			$msg_contact.=utf8_decode($_POST["fa_adresse"]);
			$msg_contact.="\r\n";
			$msg_contact.=utf8_decode($_POST["fa_npa"])." ".utf8_decode($_POST["fa_ville"]);
			
			// Envoi un message à l'administrateur pour signaler l'inscription (prévention contre l'utilisation abusive)
			$subject = translate ("formationagenda_titre_mail_admin","Nouvelle inscription à une formation sur le site Internet ").$my_domain;
			$message = "Une nouvelle inscription a eu lieu sur le site ".$my_domain.": ".$_POST["fa_mail"];

			$message.="\r\n\r\n";
			$message.=$msg_formation;
			$message.="\r\n\r\n";
			$message.=$msg_contact;
			$tmp=translate ("formationagenda_signature_inscription","");
			if ($tmp!="") {
				$message.="\r\n";
				$message.="\r\n";
				$message.="--";
				$message.="\r\n";
				$message.=$tmp;
			}

			$headers = "From: noreply@".$my_domain."\r\n" ;
			$headers .= 'Content-Type: text;charset=utf-8' . "\r\n";
			mail($to, $subject, $message, $headers);

			// Envoi d'un message de confirmation à la personne inscrite, avec un lien pour valider l'inscription et les informations de paiement
			$subject = translate ("formationagenda_titre_mail_client","Confirmation de votre inscription sur le site ").$my_domain;
			$message = translate ("formationagenda_titre_confirmation_inscription","Nous vous confirmons votre inscription à la formation suivante:");
			$message.="\r\n\r\n";
			$message.=$msg_formation;
			$message.="\r\n\r\n";
			$message.=translate ("formationagenda_confirmation_inscription","Votre inscription a bien été enregistrée. Vous recevrez prochainement un e-mail de confirmation avec les informations concernant l'accès au lieu de formation et les modalités de paiement.");

			if ($tmp!="") {
				$message.="\r\n";
				$message.="\r\n";
				$message.="--";
				$message.="\r\n";
				$message.=$tmp;
			}

			$headers = "From: ".$to."\r\n" ;
			$headers .= 'Content-Type: text;charset=utf-8' . "\r\n";
			mail($to2, $subject, $message, $headers);
			
			// Enregistrement dans la base de donnée de l'inscription
			$query="insert into t_contact_formation (cont_id, dafo_id) values ('".$id."', '".$_POST["fa_id"]."')";
			$result = mysql_query($query, $dbh);
			if ($result>0) {
				
			} else {
				
			}
			// Si réussi, message de confirmation
			echo translate ("formationagenda_confirmation_inscription","Votre inscription a bien été enregistrée. Vous recevrez prochainement un e-mail de confirmation avec les informations concernant l'accès au lieu de formation et les modalités de paiement.");

	
		exit;

	}
	ob_end_flush();

?>


<form id='fa_formulaire' name='fa_formulaire'>
<?
	// Affichage des informations sur la formation
	$query="select * from t_date_formation left join t_formation on (t_date_formation.form_id=t_formation.form_id) where t_date_formation.dafo_id=".$_GET["id"];
	$result = mysql_query($query, $dbh);
	if ($result>0 && mysql_num_rows($result)>0) {
		echo "<h2>Informations:</h2>";
		echo "<div>Formation: <b>".mysql_result($result,0,"form_titre")."</b></div>";
		$oDate = new DateTime(mysql_result($result, $i, "dafo_date"));
		$wd=array("Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi");

		echo "<div>Date: <b>".$wd[$oDate->format("w")]." ".$oDate->format("d.m.Y à H:i").(mysql_result($result, $i, "dafo_duree")!=""?" (".mysql_result($result, $i, "dafo_duree").")":" (".mysql_result($result, $i, "form_duree").")")."</b></div>";
		echo "<div>Lieu: <b>".mysql_result($result, $i, "dafo_ville").(mysql_result($result, $i, "dafo_adresse")!=""?" (".mysql_result($result, $i, "dafo_adresse").")":"")."</b></div>";
		echo "<INPUT type='hidden' name='fa_id' id='fa_id' value='".mysql_result($result,0,"dafo_id")."'>";

	} else {
		echo "Aucune formation trouvée";
		exit;
	}

	// Saisie des informations sur l'utilisateur
	echo "<hr>";
	echo "<h2>Vos coordonnées:</h2>";
	echo "<div class='label' style='display:inline-block; width:50%'>".translate("formationagenda_libelle_nom", "Nom")."</div>";
	echo "<div class='label' style='display:inline-block; width:50%'>".translate("formationagenda_libelle_prenom", "Prénom")."</div>";
	echo "<INPUT type='text' name='fa_nom' id='fa_nom' style='WIDTH: 47%; display:inline-block;'>";
	echo "<INPUT type='text' name='fa_prenom' id='fa_prenom' style='WIDTH: 47%; display:inline-block;'>";
	echo "<div class='label'>".translate("formationagenda_libelle_mail", "E-mail")."</div>";
	echo "<INPUT type='text' name='fa_mail' id='fa_mail' style='WIDTH: 95%'>";
	echo "<div class='label'>".translate("formationagenda_libelle_adresse", "Adresse")."</div>";
	echo "<INPUT type='text' name='fa_adresse' id='fa_adresse' style='WIDTH: 95%'>";
	echo "<div class='label'>".translate("formationagenda_libelle_npa_localite", "NPA/Localité")."</div>";
	echo "<INPUT type='text' name='fa_npa' id='fa_npa' style='WIDTH: 15%'>";
	echo "<INPUT type='text' name='fa_ville' id='fa_ville' style='WIDTH: 79%'>";
	echo "<div class='label'>".translate("formationagenda_libelle_Pays", "Pays")."</div>";
	echo "<SELECT name='fa_pays' id='fa_pays' style='WIDTH: 95%'><OPTION value='Suisse'>".translate("formationagenda_pays_suisse","Suisse")."</OPTION><OPTION value='France'>".translate("formationagenda_pays_france","France")."</OPTION><OPTION value='Deutchland'>".translate("formationagenda_pays_allemagne","Allemagne")."</OPTION><OPTION value='Italia'>".translate("formationagenda_pays_italie","Italie")."</OPTION><OPTION value=''>".translate("formationagenda_pays_autre","Autre")."</OPTION></SELECT>";
	echo "<hr>";
	echo "<div><input type='checkbox' name='fa_cg' id='fa_cg'><label for='fa_cg'> ".translate ("formationagenda_conditions_generales","J'ai lu et j'accepte les conditions générales")."</label></div>";
	echo "<div><input type='checkbox' name='fa_ml' id='fa_ml' checked><label for='fa_ml'> ".translate ("formationagenda_mailing_liste","Je souhaite être tenu au courant des nouveautés")."</label></div>";
	
	// Affichage des conditions générales et autres points administratifs

?>
</form>
