<?

	ob_start();
	include_once ("../onlineEdit/db.php");


	function ml_get_domain($url) {     
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

	// 3me étape, renseigne la ville et le pays
	if (isset($_POST["ml_ville"])) {
		header( 'Content-Type:text/html; charset=iso-8859-1' );
		if ($_POST["ml_ville"]=="" ) {
			header("HTTP/1.0 401 Unauthorized"); 
			echo translate("mailingListe_saisie_invalide_3", "Veuillez renseigner le nom de votre localité.");
		} else {
			// Lit les coordonnées de Google
			$address=utf8_decode($_POST["ml_ville"].", ".$_POST["ml_pays"]);
			$address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
			$url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
			$response = file_get_contents($url);
			$json = json_decode($response,TRUE); //generate array object from the response from the web
			$lat=$json['results'][0]['geometry']['location']['lat'];
			$long=$json['results'][0]['geometry']['location']['lng'];

			// Ajoute l'adresse dans la base de donnée
			$query="update t_contact set cont_ville='".str_replace("'","\'",utf8_decode($_POST["ml_ville"]))."', cont_pays='".$_POST["ml_pays"]."', cont_lat='".$lat."', cont_long='".$long."' where cont_id=".$_POST["ml_id"];
			$result = mysql_query($query, $dbh);
			// Si réussi, confirme le bon enregistrement de tout.
			if ($result>0)
			{ 
				$id=$_POST["ml_id"];
				echo translate("mailingListe_sauvegarde_reussie_3", "Merci pour ces infos! Nous vous tiendrons informés de nos activités.");
			} 
			else 
			{ 
				// Sinon problème avec la connexion de la base de donnée
				header("HTTP/1.0 401 Unauthorized"); 
				echo translate("mailingListe_envoi_rate", "Problème avec la base de donnée. Vos informations n'ont pas pu être enregistrées. Essayez plus tard.");
			} 	
		}	
		exit;		
	}
	// 2ème étape, renseigne le nom et le prénom
	if (isset($_POST["ml_nom"])) {
		header( 'Content-Type:text/html; charset=iso-8859-1' );
		if ($_POST["ml_nom"]=="" && $_POST["ml_prenom"]=="") {
			header("HTTP/1.0 401 Unauthorized"); 
			echo translate("mailingListe_saisie_invalide_2", "Veuillez renseigner au moins le nom OU le prénom.");
		} else {
			
			// Ajoute l'adresse dans la base de donnée
			$query="update t_contact set cont_titre='".$_POST["ml_titre"]."', cont_nom='".str_replace("'","\'",utf8_decode($_POST["ml_nom"]))."', cont_prenom='".str_replace("'","\'",utf8_decode($_POST["ml_prenom"]))."' where cont_id=".$_POST["ml_id"];
			$result = mysql_query($query, $dbh);
			// Si réussi, demande plus d'infos à la personne (titre, nom et prénom)
			if ($result>0)
			{ 
				$id=$_POST["ml_id"];
			echo translate("mailingListe_sauvegarde_reussie_2", "Merci pour ces infos. Afin de vous tenir informé en priorité des évènements les plus proches de chez vous, n'hésitez pas à nous indiquer votre région:");
				echo "<div class='label'>".translate("mailingListe_libelle_ville", "Ville")."</div>";
				echo "<INPUT type='text' name='ml_ville' id='ml_ville' style='WIDTH: 95%'>";
				echo "<div class='label'>".translate("mailingListe_libelle_pays", "Pays")."</div>";
				echo "<INPUT type='hidden' name='ml_id' id='ml_id' value='".$id."'>";
				echo "<SELECT name='ml_pays' id='ml_pays' style='WIDTH: 95%'><OPTION value='Suisse'>".translate("mailingListe_pays_suisse","Suisse")."</OPTION><OPTION value='France'>".translate("mailingListe_pays_france","France")."</OPTION><OPTION value='Deutchland'>".translate("mailingListe_pays_allemagne","Allemagne")."</OPTION><OPTION value='Italia'>".translate("mailingListe_pays_italie","Italie")."</OPTION><OPTION value=''>".translate("mailingListe_pays_autre","Autre")."</OPTION></SELECT>";
				echo "<INPUT type=button onclick='ml_submitForm(); return false;' value='Valider' width='50'>";
			} 
			else 
			{ 
				// Sinon problème avec la connexion de la base de donnée
				header("HTTP/1.0 401 Unauthorized"); 
				echo translate("mailingListe_envoi_rate", "Problème avec la base de donnée. Vos informations n'ont pas pu être enregistrées. Essayez plus tard.");
			} 	
		}	
		exit;		
	} 
	if (isset($_POST["ml_mail"])) {
		header( 'Content-Type:text/html; charset=iso-8859-1' );
		 
		$my_domain = ml_get_domain($_SERVER["SERVER_NAME"]);  
		$to      = translate("mailingListe_adresse_envoi","info@".$my_domain);
		
		
		if ($_POST["ml_mail"]=="" || !filter_var($_POST["ml_mail"], FILTER_VALIDATE_EMAIL)) {
			header("HTTP/1.0 401 Unauthorized"); 
			echo translate("mailingListe_saisie_invalide_1", "Veuillez entrer une adresse e-mail valide.");
		} else {
			// Contrôle que l'adresse n'existe pas encore
			$query="select * from t_contact where cont_mail='".$_POST["ml_mail"]."'";
			$result = mysql_query($query, $dbh);
			if (mysql_num_rows($result)>0) {
				// Est-il utile de compléter nom et prénom?
				if (mysql_result($result,0,"cont_nom")!="" || mysql_result($result,0,"cont_prenom")!="") {
					header("HTTP/1.0 401 Unauthorized"); 
					echo translate("mailingListe_saisie_invalide_1_bis", "Cette adresse e-mail existe déjà.");
					exit;
				} else {
					$id=mysql_result($result,0,"cont_id");
				}
			} else {
				// Ajoute l'adresse dans la base de donnée
				$query="insert into t_contact (cont_mail) values ('".$_POST["ml_mail"]."')";
				$result = mysql_query($query, $dbh);
				if ($result>0)
				{ 
					// Envoi un message à l'administrateur pour signaler l'inscription (prévention contre l'utilisation abusive)
					$subject = translate ("mailingListe_titre_mail_admin","Nouvelle inscription sur la ML du site Internet ").$my_domain;
					$message = "Une nouvelle adresse a été ajoutée sur le site ".$my_domain.": ".$_POST["ml_mail"];
					$headers = "From: noreplay@".$my_domain."\r\n" ;
					@mail($to, $subject, $message, $headers);

					// Envoi d'un message à la personne inscrite, pour éviter les inscriptions abusives
					
					$id=mysql_insert_id();					
				} else {
					// Sinon problème avec la connexion de la base de donnée
					header("HTTP/1.0 401 Unauthorized"); 
					echo translate("mailingListe_envoi_rate", "Problème avec la base de donnée. Vos informations n'ont pas pu être enregistrées. Essayez plus tard.");
					exit;
				}
			}
			
			
			// Si réussi, demande plus d'infos à la personne (titre, nom et prénom)

			echo translate("mailingListe_sauvegarde_reussie_1", "<h2>Adresse enregistrée!</h2>Pour mieux vous servir, merci de compléter les informations suivantes:");
			echo "<div class='label'>".translate("mailingListe_libelle_titre", "Politesse")."</div>";
			echo "<SELECT name='ml_titre' id='ml_titre' style='WIDTH: 95%'><OPTION value='0'>".translate("mailingListe_politesse_monsieur","Monsieur")."</OPTION><OPTION value='1'>".translate("mailingListe_politesse_madame","Madame")."</OPTION></SELECT>";
			echo "<div class='label'>".translate("mailingListe_libelle_nom", "Nom")."</div>";
			echo "<INPUT type='text' name='ml_nom' id='ml_nom' style='WIDTH: 95%'>";
			echo "<div class='label'>".translate("mailingListe_libelle_prenom", "Prénom")."</div>";
			echo "<INPUT type='hidden' name='ml_id' id='ml_id' value='".$id."'>";
			echo "<INPUT type='text' name='ml_prenom' id='ml_prenom' style='WIDTH: 95%'>";
			echo "<INPUT type=button onclick='ml_submitForm(); return false;' value='Valider' width='50'>";		

		}
		exit;

	}
	ob_end_flush()
?>

<?
	$GLOBALS['mailingListeINCLUDED']=true;

function mailingListe_GetDescription() {
	Return "Affiche un formulaire permettant de s'inscrire à la newsletter.<br><br> Le formulaire contient les champs suivants:<li>Nom du visiteur</li><li>Adresse e-mail du visiteur</li><br>Paramètres du modules:<br> <li>Couleur de texte</li><li>Couleur de fond</li> ";
}
function mailingListe_GetKeywords() {
	Return "mailing liste contact";
}
function mailingListe_GetTitle() {
	Return "Newsletter";
}
function mailingListe_GetCredit() {
	Return "Ecrit par David Dräyer";
}
	
	function mailingListe_getParams() {
		$x=array(array("nom"=>"ml_titre","label"=>"Titre","type"=>"string"),
					array("nom"=>"ml_couleur_fond","label"=>"Couleur du fond","type"=>"string"),
				 array("nom"=>"ml_couleur_texte","label"=>"Couleur du texte","type"=>"string"),
				 array("nom"=>"btn_admin","label"=>"Administration","type"=>"button")
				 
				 );
		return $x;
	}
		
	function mailingListe_Print() {
		if (isset($_POST["cont_nom"])) { 
			echo "<h2>Merci pour votre message!</h2>Il a été envoyé à chacun des membres de la troupe, et vous devriez obtenir une réponse dans les 2 jours ouvrables. <br>Nous vous remercions pour l'intérêt que vous portez à notre travail, et en espérant vous avoir séduit à travers ce site nous vous saluons... chaleureusement.<br><br><div align='right'>L'équipe de Makadam</div>";
		} else {
?>

<script>
function ml_submitForm()
{ 
	$('#ml_submit').attr('disabled', 'disabled');
	$('#ml_submit').blur()
	var masque= document.getElementById("masque_ml");
	var masque2= document.getElementById("masque_ml2");
	var div_ml= document.getElementById("ml_ajax");
	masque2.innerHTML="<table width='100%' height='100%'><tr><td style='vertical-align:middle'><div style='font-weight:bold; color:<?=urldecode($GLOBALS["couleur_texte"])?>;'>Contact avec le serveur...</div></td></tr></table>";
    masque.style.display="";
	var xhr; 
    try {  xhr = new ActiveXObject('Msxml2.XMLHTTP');   }
    catch (e) 
    {
        try {   xhr = new ActiveXObject('Microsoft.XMLHTTP'); }
        catch (e2) 
        {
           try {  xhr = new XMLHttpRequest();  }
           catch (e3) {  xhr = false;   }
         }
    }
 
    xhr.onreadystatechange  = function() 
    { 
       if(xhr.readyState  == 4)
       {
        if(xhr.status  == 200) {
            document.getElementById("ml_ajax").innerHTML=xhr.responseText; 
            document.getElementById("masque_ml").style.display="none";
        } else
        if(xhr.status  == 401) {
            document.getElementById("masque_ml2").innerHTML="<table width='100%' height='100%'><tr><td style='vertical-align:middle; color:<?=urldecode($GLOBALS["couleur_texte"])?>'>" + xhr.responseText + "<p><input  type='button' value='OK' style='width:100px' onclick='document.getElementById(\"masque_ml\").style.display=\"none\"; $(\"#ml_submit\").removeAttr(\"disabled\");'>" + "</td></tr></table>"; 
        }
        else 
            document.getElementById("masque_ml2").innerHTML="<table width='100%' height='100%'><tr><td style='vertical-align:middle; color:<?=urldecode($GLOBALS["couleur_texte"])?>'>" + "Error code " + xhr.status + "<p><input  type='button'  value='OK' style='width:100px' onclick='document.getElementById(\"masque_ml\").style.display=\"none\"; $(\"#ml_submit\").removeAttr(\"disabled\");'>" + "</td></tr></table>";
        }
    }; 

 xhr.open("POST", "/modules/mailingListe.php", true);
 xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
 if (document.ml_formulaire.ml_mail) { 
	var data = "ml_mail=" + document.ml_formulaire.ml_mail.value;
 }
if (document.ml_formulaire.ml_nom) { 
	var data = "ml_titre=" + document.ml_formulaire.ml_titre.value + "&ml_nom=" + document.ml_formulaire.ml_nom.value + "&ml_prenom="+document.ml_formulaire.ml_prenom.value + "&ml_id=" + document.ml_formulaire.ml_id.value;
 }
 if (document.ml_formulaire.ml_ville) { 
	var data = "ml_ville=" + document.ml_formulaire.ml_ville.value + "&ml_pays="+document.ml_formulaire.ml_pays.value + "&ml_id=" + document.ml_formulaire.ml_id.value;
 }
 xhr.send(data); 
} 
</script>

	<div class='module_mailing'>
		<h2><?=urldecode($GLOBALS["ml_titre"])?></h2>
	<form method="post" name="ml_formulaire" id="ml_formulaire">
	<div  style="position:relative">
	<div style="position:absolute; z-index:10;  top:0; left:0; right:0; bottom:0;  display:none" id="masque_ml">
		<div style="position:absolute;  opacity:0.7;   filter:alpha(opacity=70); top:0; left:0; right:0; bottom:0; background:<?=urldecode($GLOBALS["couleur_fond"])?> ; " id="masque_ml1"></div>
		<div style="position:absolute; padding:15px; font-weight:bold;  top:0; left:0; right:0; bottom:0; text-align:center; " id="masque_ml2"></div>
	</div>
	<div name="ml_ajax" id="ml_ajax">
	<div class='label'><?=translate("mailingListe_libelle_email","Votre&nbsp;adresse de courriel")?></div>
	<INPUT type='text' name="ml_mail" id="ml_mail" style="WIDTH: 95%">
	<INPUT type=button id='ml_submit' onclick="ml_submitForm(); return false;" value="S'inscrire" width="50">
	</div>
	</div>
	</form>
	</div>
<?
		}
	}
?>
