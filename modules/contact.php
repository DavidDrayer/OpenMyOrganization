<?
	ob_start();
	include_once ("../onlineEdit/db.php");

	function get_domain($url) {     
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

	if (isset($_POST["cont_nom"])) {
		header( 'Content-Type:text/html; charset=iso-8859-1' );
		 
		$my_domain = get_domain($_SERVER["SERVER_NAME"]);  
		$to      = translate("contact_adresse_envoi","info@".$my_domain);
		$subject = translate ("contact_titre_mail","Contact site Internet ".$my_domain." > Demande d'infos de ").$_POST["cont_nom"];
		$message = stripslashes($_POST["cont_message"]);
		$headers = 'From: ' . $_POST["cont_mail"] . "\r\n" ;
		
		if ($_POST["cont_nom"]=="" || $message=="" || $_POST["cont_mail"]=="") {
			header("HTTP/1.0 401 Unauthorized"); 
 			echo translate("contact_saisie_invalide", "Veuillez remplir correctement les champs NOM, ADRESSE et MESSAGE.");
		} else
		
		if (@mail($to, $subject, $message, $headers))
 		{ 
 			echo translate("contact_envoi_reussi", "<h2>Message envoyé!</h2>Il a été transmis au responsable du site qui vous contactera prochainement.");
		} 
		else 
		{ 
		    header("HTTP/1.0 401 Unauthorized"); 
 			echo translate("contact_envoi_rate", "Problème avec le serveur mail, le message n'a pas pu être envoyé. Essayez plus tard.");
		} 		
	

		exit;

	}
	ob_end_flush()
?>

<?
	$GLOBALS['contactINCLUDED']=true;

function contact_GetDescription() {
	Return "Affiche un formulaire de contact permettant d'envoyer un e-mail à l'administrateur du site sans révéler l'adresse e-mail et sans nécessiter un programme d'envoi d'emails.<br><br> Le formulaire contient les champs suivants:<li>Nom du visiteur</li><li>Adresse e-mail du visiteur</li><li>Texte du message</li><br>Paramètres du modules:<br> <li>Couleur de texte</li><li>Couleur de fond</li> ";
}
function contact_GetKeywords() {
	Return "Affichage d'actualités";
}
function contact_GetTitle() {
	Return "Formulaire de contact";
}
function contact_GetCredit() {
	Return "Ecrit par David Dräyer";
}
	
	function contact_getParams() {
		$x=array(array("nom"=>"couleur_fond","label"=>"Couleur du fond","type"=>"string"),
				 array("nom"=>"couleur_texte","label"=>"Couleur du texte","type"=>"string"));
		return $x;
	}
		
	function contact_Print() {
		if (isset($_POST["cont_nom"])) { 
			echo "<h2>Merci pour votre message!</h2>Il a été envoyé à chacun des membres de la troupe, et vous devriez obtenir une réponse dans les 2 jours ouvrables. <br>Nous vous remercions pour l'intérêt que vous portez à notre travail, et en espérant vous avoir séduit à travers ce site nous vous saluons... chaleureusement.<br><br><div align='right'>L'équipe de Makadam</div>";
		} else {
?>

<script>
function submitForm()
{ 
	var masque= document.getElementById("masque_contact");
	var masque2= document.getElementById("masque_contact2");
	var div_contact= document.getElementById("ajax");
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
            document.getElementById("ajax").innerHTML=xhr.responseText; 
        } else
        if(xhr.status  == 401) {
            document.getElementById("masque_contact2").innerHTML="<table width='100%' height='100%'><tr><td style='vertical-align:middle; color:<?=urldecode($GLOBALS["couleur_texte"])?>'>" + xhr.responseText + "<p><input  type='button' value='OK' style='width:100px' onclick='document.getElementById(\"masque_contact\").style.display=\"none\"'>" + "</td></tr></table>"; 
        }
        else 
            document.getElementById("masque_contact2").innerHTML="<table width='100%' height='100%'><tr><td style='vertical-align:middle; color:<?=urldecode($GLOBALS["couleur_texte"])?>'>" + "Error code " + xhr.status + "<p><input  type='button'  value='OK' style='width:100px' onclick='document.getElementById(\"masque_contact\").style.display=\"none\"'>" + "</td></tr></table>";
        }
    }; 

 xhr.open("POST", "/modules/contact.php", true);
 xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");  
 var data = "cont_nom=" + document.cont_formulaire.cont_nom.value +  "&cont_message=" + document.cont_formulaire.cont_message.value + "&cont_mail=" + document.cont_formulaire.cont_mail.value;
 xhr.send(data); 
} 
</script>


	<form method="post" name="cont_formulaire">
	<div name="ajax" id="ajax" style="position:relative">
	<div style="position:absolute; z-index:10;  top:0; left:0; right:0; bottom:0;  display:none" id="masque_contact">
		<div style="position:absolute;  opacity:0.7;   filter:alpha(opacity=70); top:0; left:0; right:0; bottom:0; background:<?=urldecode($GLOBALS["couleur_fond"])?> ; " id="masque_contact1"></div>
		<div style="position:absolute; padding:15px; font-weight:bold;  top:0; left:0; right:0; bottom:0; text-align:center; " id="masque_contact2"></div>
	</div>
	<div>
	<P><?=translate("contact_libelle_nom","Votre nom")?>:</P>
	<P><INPUT name="cont_nom" style="WIDTH: 95%"></P>
	<P><?=translate("contact_libelle_email","Votre&nbsp;adresse de courriel")?>:</P>
	<P><INPUT name="cont_mail" style="WIDTH: 95%"></P>
	<P><?=translate("contact_libelle_message","Votre message")?>:</P>
	<P><TEXTAREA name="cont_message" style="WIDTH: 95%; HEIGHT: 83px" rows=4></TEXTAREA>&nbsp;</P><INPUT type=submit onclick="submitForm(); return false;" value="Envoyer le message" width="50">
	</div>
	</div>
	</form>
<?
		}
	}
?>
