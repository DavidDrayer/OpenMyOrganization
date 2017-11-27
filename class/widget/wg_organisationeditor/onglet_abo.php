<?php

	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");

	// ********************************************************************/
	// *********** Abonnements                     			   ************/
	// ********************************************************************/
		
	// Formulaire d'édition
	$organisation=$this->_organisation;
	echo "<form name='formulaire_abo' id='formulaire_abo' action='/paypal/test.php' method='post' target='payment_window'>";
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once ("$root/formulaires/form_abo.php");
	echo "</form>";
		
?>
	<script> 
		function refreshAbo() {
			$("#formulaire_abo").load( "/formulaires/form_abo.php?action=refresh&org=<?=$organisation->getId()?>" );
			alert("réussi");
		}
		$(document).ready(function(){
					
			$("#formulaire_abo").submit(function() {

				// Ouvre une fenêtre de paiement
				window.open('', 'payment_window', 'menubar=no, scrollbars=yes, width=1000, height=600')
				
				// Redirige vers la page de paiement

			});

		});
	</script>

