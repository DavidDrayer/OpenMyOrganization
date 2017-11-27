<?php
	if (isset($_GET["action"])) { 
		include_once("../../../include.php");
	}
	// ********************************************************************/
	// *********** ORGANISATION                        ************/
	// ********************************************************************/

	// Formulaire d'édition
	$organisation=$this->_organisation;
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once ("$root/formulaires/form_editorg.php");

   
?>
