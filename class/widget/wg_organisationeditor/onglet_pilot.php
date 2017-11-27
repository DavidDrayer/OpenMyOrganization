<?php

	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");

	// ********************************************************************/
	// *********** Abonnements                     			   ************/
	// ********************************************************************/
		
	// Formulaire d'édition
	$organisation=$this->_organisation;
	include_once ("$root/formulaires/form_editpilot.php");

		
?>


