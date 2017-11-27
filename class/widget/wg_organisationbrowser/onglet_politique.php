<?
	$domains=$this->_role->getScopes();
	if (count($domains)>0) {
		echo " <div class='grey_design accordion_pol'>";

		foreach($domains as $entry) {
			echo "<h3><b>";
			echo $entry->getDescription()."</b> ";
			echo "</h3>";
			echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
			if ($entry->getPolitiques()!="") {
				echo $entry->getPolitiques();
			} else {
				echo "Aucune politique définie pour ce domaine";
			}
			echo "</div>";
		}
		
		echo "</div>";	

	} else {
		echo "Aucun domaine associé à ce rôle";
	}



?>
