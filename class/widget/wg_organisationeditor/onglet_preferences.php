<?php

	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");

	// ********************************************************************/
	// *********** Abonnements                     			   ************/
	// ********************************************************************/
		
	// Formulaire d'édition
	$organisation=$this->_organisation;
	echo "<form name='formulaire_pilot' id='formulaire_id' action='' method='post' target=''>";
	echo "Liste des champs pour les preferences";
	//include_once ("$root/formulaires/form_abo.php");
	echo "</form>";
		
?>
	<script> 

		$(document).ready(function(){

		});
	</script>

