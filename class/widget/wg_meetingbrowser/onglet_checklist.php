<?

	// Suis-je le secrétaire du meeting?
	$isSecretary=($_SESSION["currentUser"]->getId()>1 && $meeting->getSecretaryId()==$_SESSION["currentUser"]->getId());
	// Le meeting est-il en cours?
	$isInProcess=($meeting->getOpeningTime()!=null && $meeting->getClosingTime()==null);
	// Affichage des entrées du chat

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
				echo "<tr class='highlight'><td><span ";

				echo "style='display:inline-block; width:200px; font-weight:bold'>";

				
				if ($entry->getRole()) {
					// Si le rôle=le cercle affichage de Premier lien - DDR, 19.6.2014
					if ($entry->getCircle() && $entry->getRole()->getId()==$entry->getCircle()->getId()) 
						echo "Premier lien";
					else
						echo $entry->getRole()->getName();
				} else {
					echo T_("Tous les membres");
				}
				echo "</span>";
								echo "<div class='omo-greytext'>";
				// Affichage de la personne en charge du rôle, pour les revues
				if ($entry->getUser()) {
					echo $entry->getUser()->getUserName();
				} else
				if ($entry->getRole()) 
					if ($entry->getRole()->getUserId()>0) {
						echo $entry->getRole()->getUser()->getUserName();
					} else {
						echo "Premier lien: ".$entry->getCircle()->getUser()->getUserName();
					}
				echo "</div>";
				echo "</td><td  width='100%'>";
				
				if (isset($_SESSION["currentUser"])) {			
				echo "<span title='".str_replace("'","&apos;",$entry->getDescription())."'";
				if ((is_null($entry->getRole()) || $_SESSION["currentUser"]->isRole($entry->getRole()))) {
					echo " class='omo-me' ";
				} else echo " class='omo-greytext'";
				echo ">";
				
				echo $entry->getTitle();
				if ($entry->getTitle()=="" && $entry->getDescription()!="" ) echo $entry->getDescription();

				echo "</span>";
				} else {
					echo $entry->getTitle();
					if ($entry->getTitle()=="" && $entry->getDescription()!="" ) echo $entry->getDescription();
				}
				$count++;
				
		// Affichage du VU si checké récemment
				if ($entry->getDate()) {
					$interval=date_diff( $entry->getDate()->getDate(),new DateTime())->format("%a") ;
					if (date_diff( $entry->getDate()->getDate(),new DateTime())->format("%a") <= $recurrence->getTimeLaps()) {
					
					if ($interval>($recurrence->getTimeLaps()/7*6)) {
						// Attention, c'est bientôt le moment!
						echo "</td><td>";
						echo "<span class='checkbof' title='Il reste ".($recurrence->getTimeLaps()-$interval)." jour(s)'></span>";
					} else {
						// Ok, dans les temps
						echo "</td><td>";
						if ($interval==0)
							echo "<span class='check' title=\"Il y a moins d'un jour\"></span>";
						else
						if ($interval==1)
							echo "<span class='check' title='Il y a ".$interval." jour'></span>";
						else
							echo "<span class='check' title='Il y a ".$interval." jours'></span>";
					}
				} else {
					// No check, combien de jours de retard?
					echo "</td><td>";
					echo "<span class='nocheck' title='En retard de ".($interval-$recurrence->getTimeLaps())." jour(s)'></span>";
				}
				} else {
					echo "</td><td>";
					echo "<span class='nocheck' title='Jamais fait'></span>";
				}
				echo "</td><td>";
						
				// Affichage du bouton check si c'est le secrétaire et que la réunion est en cours
				if ($isSecretary && $isInProcess) {
					echo "<a class='ajax btn_check' href='ajax/validechecklist.php?id=".$entry->getId()."' alt='".T_("Clickez pour une tâche effectuée")."' >check</a>";
				}
				
				echo"</td><td><div style='text-wrap:none; width:55px;'>";
				// Affiche les boutons d'édition si nécessaire$

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
