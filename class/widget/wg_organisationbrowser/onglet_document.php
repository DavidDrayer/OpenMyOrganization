<?

	// Chargela liste des documents

	
	$documents=$this->_role->getDocuments();
	foreach($documents as $document) {
			echo "<div class='role_document'><div class='bottom'><a href='".$document->getURL()."' target='_blank'>".$document->getTitle()."</a></div>";
			echo "<div class='options'><a class='omo-delete ajax' href='ajax/deletechecklist.php?id=202' alt='Supprimer' check='Etes-vous s&ucirc;r de vouloir supprimer cette check-list?'></a><a class='omo-edit dialogPage' href='formulaires/form_checklist.php?id=202' alt='Editer une check-list'></a></div>";
			echo "</div>";
	}


?>
