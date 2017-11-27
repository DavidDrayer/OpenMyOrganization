<?php
	namespace widget;
	
// Cette classe affiche un browser HTML permettant de parcourir un ou plusieurs objet de type "rorganisation" dans son
// intégralité : redevabilites, perimetres, raison d'etre, etc...
class wg_accountBrowser extends Widget
{

private $_organisation;
	
	// Constructeur nécessitant le role à afficher
	// Entrée : le role à afficher
	// Sortie : un objet de type wg_circleBrowser
	public function __construct($organisation) 
	{
		$this->_organisation=$organisation;
	}

public function display() {
?>
<html>
	<head>
		<title>TEST</title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

	
		<!-- styles needed by jScrollPane -->
	    <link rel="stylesheet" href="/plugins/timepicker/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
		<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/circle.css" />

		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
		
	   <script type="text/javascript" src="/plugins/timepicker/jquery.ui.timepicker.js?v=0.3.3"></script>

		
		
		<script>
			function moveUp() {
				newpos=parseInt(($(window).scrollTop()-100)/$(window).height())*$(window).height();
				$(window).scrollTop(newpos);
			}
			function moveDown() {
				newpos=parseInt($(window).scrollTop()/$(window).height()+1)*$(window).height();
				$(window).scrollTop(newpos);
			}
			$(document).ready(function(){
				$("#tabs_droite").tabs({heightStyle: "fill"});
				
				$(window).resize(function () {
						$("#tabs_droite").tabs({heightStyle: "fill"});
				
				})
				
			});
		</script>

	</head>
	<body>
<div class='header'><?$this->_displayNav($this->_organisation);?></div>
<div class='omo-maindiv adminbrowser'>
<div class='omo-rightcol'>			
			<div id="tabs_droite">
 <ul>
    <li><a name="tabs-1" href="#tabs-1"><span class='omo-role'><span class='omo-tab-label'><? print T_("Organisation"); ?></span></span></a></li>
    <li><a name="tabs-2" href="#tabs-2"><span class='omo-politic'><span class='omo-tab-label'><? print T_("Membres"); ?></span></span></a></li>
    <li><a name="tabs-3" href="#tabs-3"><span class='omo-history'><span class='omo-tab-label'><? print T_("Licence"); ?></span></span></a></li>
</ul>
  
<!-- Onglet 1, avec les différents rôles -->  
  <div id="tabs-1">
	<?php include "wg_accountbrowser/onglet_org.php";  ?>
  </div>

<!-- Autres onglets  -->  
  <div id="tabs-2">
     <? echo "tabs2" ?>
  </div>
  <div id="tabs-3">
    <? echo "tabs3" ?>
  </div>
  </div>
</div>
</div>
<?php
}

}

?>

