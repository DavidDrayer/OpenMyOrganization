<!-- newAccount -->
<?
	$GLOBALS['newaccountINCLUDED']=true;

function newaccount_GetDescription() {
	Return "Affiche un formulaire de cr�ation de nouveau compte.<br><br> Le formulaire contient les champs suivants:<li>Nom du visiteur</li><li>Adresse e-mail du visiteur</li><br>Param�tres du modules:<br> <li>Couleur de texte</li><li>Couleur de fond</li> ";
}
function newaccount_GetKeywords() {
	Return "newaccount";
}
function newaccount_GetTitle() {
	Return "NewAccount";
}
function newaccount_GetCredit() {
	Return "Ecrit par David Dr�yer";
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
			
			// Envoie le formulaire en AJAX (m�thode POST), la destination �tant d�finie par l'�l�ment � l'ID form_target 
			$.post($("#form-account #form_target")[0].value, $("#form-account").serialize()) 
				.done(function(data, textStatus, jqXHR) {
					if (textStatus="success")
					{
						// Traite une �ventuelle erreur
						if (data.indexOf("Erreur")>0) {
							eval(data);
						} else {
							// Affiche les donn�es en retour en remplacement du contenu du formulaire (le contenant reste) 
							$("#form-account")[0].innerHTML=data;
							// Int�rpr�te les scripts retourn�s (� v�rifier si �a fonctionne)
							eval($("#form-account").find("script").text());
						}
					}
					else {
						// Probl�me d'envoi
						alert("Echec!");
					
					}
				}).fail(function() {alert ("Probl�me d'envoi");});
				// Bloque la proc�dure standard d'envoi
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
