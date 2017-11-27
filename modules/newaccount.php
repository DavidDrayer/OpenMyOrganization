<!-- newAccount -->
<?
	$GLOBALS['newaccountINCLUDED']=true;

function newaccount_GetDescription() {
	Return "Affiche un formulaire de création de nouveau compte.<br><br> Le formulaire contient les champs suivants:<li>Nom du visiteur</li><li>Adresse e-mail du visiteur</li><br>Paramètres du modules:<br> <li>Couleur de texte</li><li>Couleur de fond</li> ";
}
function newaccount_GetKeywords() {
	Return "newaccount";
}
function newaccount_GetTitle() {
	Return "NewAccount";
}
function newaccount_GetCredit() {
	Return "Ecrit par David Dräyer";
}
	
	function newaccount_getParams() {
		$x=array(array("nom"=>"sl_titre","label"=>"Titre","type"=>"string"),
					array("nom"=>"couleur_fond","label"=>"Couleur du fond","type"=>"string"),
				 array("nom"=>"couleur_texte","label"=>"Couleur du texte","type"=>"string")
				 
				 );
		return $x;
	}
		
function newaccount_Print() {
?>



<script>
	$(document).ready(function() {
		$( "#form-account" ).load('/formulaires/form_newaccount.php');
		
		$("#btn_createAccount").click(function() {
				$( "#form-account" ).submit();
		});
		
		$( "#form-account" ).submit(function() {
			
			// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
			$.post($("#form-account #form_target")[0].value, $("#form-account").serialize()) 
				.done(function(data, textStatus, jqXHR) {
					if (textStatus="success")
					{
						// Traite une éventuelle erreur
						if (data.indexOf("Erreur")>0) {
							eval(data);
						} else {
							// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
							$("#form-account")[0].innerHTML=data;
							// Intérprète les scripts retournés (à vérifier si ça fonctionne)
							eval($("#form-account").find("script").text());
						}
					}
					else {
						// Problème d'envoi
						alert("Echec!");
					
					}
				}).fail(function() {alert ("Problème d'envoi");});
				// Bloque la procédure standard d'envoi
				return false;			

		});
	});	
    </script>
    <style>
		
	</style>
    <div class='module_newaccount' style="position: relative; overflow: hidden; box-sizing:border-box;">
	<form name='form-account' id='form-account'>
		loading...
	</form>
    </div>
<?		
	}
?>
