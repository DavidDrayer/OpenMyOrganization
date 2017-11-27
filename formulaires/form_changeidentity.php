<?
// Insère les méthodes de base
include_once("../include.php");
	
// S'assurer que l'utilisateur courant est bien un Super Admin

// Traite le formulaire posté
	if (isset($_POST["memb_id"])) {
		// Défini ce membre comme étant l'utilisateur courrant
		$user=$_SESSION["currentManager"]->loadUser($_POST["memb_id"]);
		$_SESSION["currentUser"]=$user;
		echo "Vous êtes maintenant ".$user->getUserName();
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
	// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
		$(".select_name option").removeAttr('disabled');

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
			}
            else {
            	// Problème d'envoi
            	alert("Echec!");
            
            }
        });
        // Bloque la procédure standard d'envoi
        return false;
});

    $( "#dialogStd" ).dialog({ buttons: [{ text: "Changer d'identité", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });

</script>
