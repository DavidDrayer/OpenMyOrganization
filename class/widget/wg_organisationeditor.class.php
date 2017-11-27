<?php
	namespace widget;
	
// Cette classe affiche un editeur HTML permettant d'�diter un ou plusieurs objet de type "organisation":
// Raison d'�tre, premier lien, liste des administrateurs, etc...
class wg_organisationEditor extends Widget
{

private $_organisation;
	
	// Constructeur n�cessitant l'organisation � �diter
	// Entr�e : l'organisation � �diter
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

					// Envoie le formulaire en AJAX (m�thode POST), la destination �tant d�finie par l'�l�ment � l'ID form_target 
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
						}).fail(function() {alert ("Probl�me d'envoi");});
						// Bloque la proc�dure standard d'envoi
						return false;
				});
				
				// D�fini la hauteur max pour les �l�ments TAB
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
    <li><a name="tabs-3" href="#tabs-3"><span ><span class='omo-tab-label'><? print T_("Pr�f�rences"); ?></span></span></a></li>
</ul>



<!-- Onglet 1, avec les diff�rents r�les -->  
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

