<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("include.php");
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<!-- Chargement des scripts et styles pour jquery et jquery-ui -->
		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
	
	<!-- Chargement des styles propre au site -->
	<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/omo.css" />
	
	<!-- Info sur la page -->
	<title>O.M.O &gt; Aide</title>


</head>
<body>
	<? echo "<div id='main_waiting_screen'>".\widget\Widget::FULL_WAITING_SCREEN."</div>";?>
	<!-- Dialogue pour éditer une zone -->
	<div id="dialogStd" title="Editer la zone d'aide"  style='display:none'>
	 	<div id="dialogStdContent"></div>
	</div>	


<?php

	echo "<div class='mainNav' style='height:68px'><div class='title'><img src='style/templates/".$_SESSION["template"]."/images/logo_h.png'/></div></div>";

	echo "<div class='grey_design' style='padding:20px;'>";

	// Instantiation du gestionnaire de base de donnée
	$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
	// Chargement de l'élément cercle sélectionné
	
	echo "<!--".$_GET["key"]."-->";
	
	$help=$manager->loadHelp($_GET["key"]);
	
	if (count($help)>0) {
	foreach ($help as $block) {
		echo "<div style='padding:5px; font-weight:bold;' class='ui-state-default ui-state-active ui-corner-top '>";
		echo "".$block->getTitle()."";			
		echo "</div>";
		echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";

		echo "<div>".$block->getText()."</div>";
		if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isDevelopper()) {
			echo "<div style='text-align:right'><button class='btn_editBlock' id='blk_".$block->getId()."'  title='Editer la zone' href='/formulaires/form_help.php?id=".$block->getId()."'>Editer</button></div>";
		}
		echo "</div>";
	}
	} else {
		echo "Aucune rubrique d'aide associée à cette page.";
	}
		if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isDevelopper()) {
			echo "<div style='text-align:right'><button class='btn_editBlock' id='blk_0'  title='Ajouter une zone' href='/formulaires/form_help.php?key=".urlencode($_GET["key"])."'>Ajouter une zone</button></div>";
		}

	echo "</div>";
?>
	<script>
		$(document).ready(function(){
			// Affiche le formulaire de saisie pour changer le texte d'aide
			$(".btn_editBlock").button().click(function (){
			var stdDialog = $( "#dialogStd" ).dialog({ 
				closeOnEscape: false,
    			beforeclose: function (event, ui) { return false; },  
	      		modal: true,
	      		width:700,
	      		height:600,
	      		buttons: {"Fermer": function() {
		          stdDialog.dialog("close");
		        }}
	      	});
			$("#dialogStdContent").html("<?=\widget\Widget::WAITING_SCREEN?>");
			stdDialog.dialog( "option" , "title" ,$(this).attr("alt"));
			stdDialog.dialog( "open" );
			// Défini son contenu par ajax en le récupérant de l'url
			$("#dialogStdContent").load($(this).attr("href"));
			});
			
			// Une fois tout modifié, cache l'écran de chargement- DDr 4.6.2014
			$("#main_waiting_screen").css("display","none");
			
		});
	</script>
</body>
</html>
