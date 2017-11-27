<?

	// Charge les différents types de recurrence
	$recurrences=\holacracy\Recurrence::getAllRecurrence();
	foreach($recurrences as $recurrence) {
		ob_start();
		echo "<div class='grey_design'>";
		echo "<div style='padding:5px; font-weight:bold;' class='ui-state-default ui-state-active ui-corner-top '>";
		echo $recurrence->getLabel();
		echo "</div>";
		echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
		$count=0;
		echo "<table  width='100%' cellspacing=1 cellpadding=0>";
		foreach($checklist as $entry) {
			if ($entry->getRecurrenceId()==$recurrence->getId()) {
				echo "<tr class='highlight'><td  width='100%'>";
				
				echo "<span title='".str_replace("'","&apos;",$entry->getDescription())."'>";
				echo $entry->getTitle();
				if ($entry->getTitle()=="" && $entry->getDescription()!="" ) echo $entry->getDescription();
				echo "</span>";

				$count++;
				echo "</td><td><div style='text-wrap:none; width:55px;'>";
				// Affiche les boutons d'édition si nécessaire$
				if (isset($_SESSION["currentUser"])) {
				//if (isset($_SESSION["currentUser"]) && (count($_SESSION["currentUser"]->getRoles($entry->getCircle()))>0 || $_SESSION["currentUser"]->isAdmin($entry->getCircle()))) {
				if ((!is_null($entry->getRole()) && $_SESSION["currentUser"]->isRole($entry->getRole()) || $_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE | \holacracy\Role::SECRETARY_ROLE,$entry->getCircle()))) {
					echo "<a class='omo-delete ajax' href='ajax/deletechecklist.php?id=".$entry->getId()."' alt='".T_("Supprimer")."' check='".T_("Etes-vous s&ucirc;r de vouloir supprimer cette check-list?")."'></a>";
					echo "<a class='omo-edit dialogPage' href='formulaires/form_checklist.php?id=".$entry->getId()."' alt='".T_("Editer une check-list")."'></a>";
	
				} 
				}
				echo "</div></td></tr>";
			}
		}
		echo "</table>";
		if ($count==0) echo T_("<i>Aucune entrée</i>");
		echo "</div>";
		echo "</div>";
		if ($count==0) ob_end_clean(); else ob_end_flush();
	}

?>
