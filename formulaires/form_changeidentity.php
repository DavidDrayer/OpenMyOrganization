<?
// Ins�re les m�thodes de base
include_once("../include.php");
	
// S'assurer que l'utilisateur courant est bien un Super Admin

// Traite le formulaire post�
	if (isset($_POST["memb_id"])) {
		// D�fini ce membre comme �tant l'utilisateur courrant
		$user=$_SESSION["currentManager"]->loadUser($_POST["memb_id"]);
		$_SESSION["currentUser"]=$user;
		echo "Vous �tes maintenant ".$user->getUserName();
		exit;

		
	}

// Afficher l'ensemble des utilisateurs
		echo "<form id='formulaire'>";
		echo "<input type='hidden' id='form_target' value='/formulaires/form_changeidentity.php'/>";
		
		$listeMembres=$_SESSION["currentManager"]->loadMemberListe();

			echo "<select id='memb_id' name='memb_id' class='select_name'>";
			// Charge la liste de tous les types de contact
			if (count($listeMembres)>0) {
				foreach($listeMembres as $membre) {
					echo "<option value='".$membre->getId()."'";
					echo "> &nbsp;".$membre->getFirstName()." ".$membre->getLastName()." (".$membre->getId()." - ".$membre->getUserName().")</option>";
				}
			}
			echo "</select>";

// 

?>
<script>

	$("#formulaire").submit(function() {
	// Envoie le formulaire en AJAX (m�thode POST), la destination �tant d�finie par l'�l�ment � l'ID form_target 
		$(".select_name option").removeAttr('disabled');

 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
        .done(function(data, textStatus, jqXHR) {
            if (textStatus="success")
            {
            	// Traite une �ventuelle erreur
            	if (data.indexOf("Erreur")>0) {
            		eval(data);
            	} else {
            	
            	
	            	// Affiche les donn�es en retour en remplacement du contenu du formulaire (le contenant reste) 
	                $("#formulaire")[0].innerHTML=data;
	                // Int�rpr�te les scripts retourn�s (� v�rifier si �a fonctionne)
	                eval($("#formulaire").find("script").text());
                }
			}
            else {
            	// Probl�me d'envoi
            	alert("Echec!");
            
            }
        });
        // Bloque la proc�dure standard d'envoi
        return false;
});

    $( "#dialogStd" ).dialog({ buttons: [{ text: "Changer d'identit�", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });

</script>
