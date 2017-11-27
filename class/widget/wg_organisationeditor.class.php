<?php
	namespace widget;
	
// Cette classe affiche un editeur HTML permettant d'éditer un ou plusieurs objet de type "organisation":
// Raison d'être, premier lien, liste des administrateurs, etc...
class wg_organisationEditor extends Widget
{

private $_organisation;
	
	// Constructeur nécessitant l'organisation à éditer
	// Entrée : l'organisation à éditer
	// Sortie : un objet de type wg_organisationEditor
	public function __construct($organisation=NULL) 
	{
		$this->_organisation=$organisation;
	}

public function display() {
?>
<html>
	<head>
		<title>Administration</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

	
		<!-- styles needed by jScrollPane -->
		<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/circle.css" />

		<!--jquery -->
		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
	
		<link href="/plugins/select2/select2.min.css" rel="stylesheet" />
		<script src="/plugins/select2/select2.min.js"></script>

		<script> 

			
			$(document).ready(function(){
				
				$('.save_org').button().click(function() {$("#formulaire").submit()});
				
				 $("#formulaire").submit(function() {

					// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
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
						}).fail(function() {alert ("Problème d'envoi");});
						// Bloque la procédure standard d'envoi
						return false;
				});
				
				// Défini la hauteur max pour les éléments TAB
				$("#tabs_droite").tabs({heightStyle: "fill"});
				
				$(window).resize(function () {
					$("#tabs_droite").tabs({heightStyle: "fill"});
				})

			});
			
			function moveUp() {
				newpos=parseInt(($(window).scrollTop()-100)/$(window).height())*$(window).height();
				$(window).scrollTop(newpos);
			}
			function moveDown() {
				newpos=parseInt($(window).scrollTop()/$(window).height()+1)*$(window).height();
				$(window).scrollTop(newpos);
			}

		</script>

	</head>
	<body>
<div class='header'><?$this->_displayNav($this->_organisation, true);?></div>
<div class='omo-maindiv adminbrowser'>
<form id='formulaire'>	
<div class='omo-rightcol'>	
	
			<div id="tabs_droite">
 <ul>
    <li><a name="tabs-1" href="#tabs-1"><span class='omo-role'><span class='omo-tab-label'><? print T_("Organisation"); ?></span></span></a></li>
    <li><a name="tabs-2" href="#tabs-2"><span ><span class='omo-tab-label'><? print T_("Pilot Canvas"); ?></span></span></a></li>
    <li><a name="tabs-3" href="#tabs-3"><span ><span class='omo-tab-label'><? print T_("Préférences"); ?></span></span></a></li>
</ul>



<!-- Onglet 1, avec les différents rôles -->  
  <div id="tabs-1">
	<?php include "wg_organisationeditor/onglet_org.php";  ?>
  </div>

<!-- Autres onglets  -->  
  <div id="tabs-2">
     <?php include "wg_organisationeditor/onglet_pilot.php";  ?>
  </div> 
   <div id="tabs-3">
     <?php include "wg_organisationeditor/onglet_preferences.php";  ?>
  </div>


  </div>
  
</div>
</form>
</div>
<?php
}

}

?>

