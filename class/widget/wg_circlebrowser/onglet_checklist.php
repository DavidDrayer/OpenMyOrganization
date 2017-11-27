<?
	echo "<div class='omo-help-title'><b>Check-listes:</b> Liste de tâches ou de bonnes habitudes à faire de façon récurrentes. <a href='#' class='omo_act_more_help'>Afficher plus...</a></div>";
	echo "<div class='omo-more-help'>Les rôles, cercles et premiers liens peuvent partager l'état de réalisation d'un certain nombre de tâches récurrentes afin que tous les membres aient une vision réaliste du status des tâches importantes. Chaque rôle/cercle est tenu de maintenir à jour ces informations avec la régularité spécifiée (hebdomadaire, mensuel,...). Voir les articles <a href='https://dev.openmyorganization.com/constitution.php?q=check-list#idx_3_0_0' target='_constitution'>4.1.1</a> et les <a href='https://dev.openmyorganization.com/constitution.php?q=check-list#idx_5' target='_constitution'>redevabilités des rôles structurels</a>.</div>";

	
	// Affichage du tutoriel de ce qu'est une checkliste et comment la créer
	if (count($checklist)==0) {
	echo "<div class='omo-warning-title'>Il n'y a aucune check-liste de définie. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
	echo "<div class='omo-more-warning'>";
			echo "Les check-listes permettent d'ancrer les bonnes habitudes et faciliter le suivi des tâches récurrentes. Découvrez ci-dessous pourquoi et comment les mettre en place.";
			echo "<div class='video'>";
			echo "<h1>Pourquoi?</h1>";
			echo "<hr>";
			echo '<iframe width="280" height="157" src="https://www.youtube.com/embed/YqMEZZEz1-Y?rel=0" frameborder="0" allowfullscreen></iframe>';
			echo "</div>";
			echo "<div class='video'>";
			echo "<h1>Comment?</h1>";
			echo "<hr>";
			echo '<iframe width="280" height="157" src="https://www.youtube.com/embed/YqMEZZEz1-Y?rel=0" frameborder="0" allowfullscreen></iframe>';
			echo "</div>";
	echo "</div>";
	}
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
						
				// Affichage du bouton check si c'est le bon rôle
				if ((!is_null($entry->getRole()) && $_SESSION["currentUser"]->isRole($entry->getRole()) )) {
					echo "<a class='ajax btn_check' href='ajax/validechecklist.php?id=".$entry->getId()."' alt='".T_("Clickez pour une tâche effectuée")."' >check</a>";
				}
				
				echo"</td><td><div style='text-wrap:none; width:55px;'>";
				// Affiche les boutons d'édition si nécessaire$
				if (isset($_SESSION["currentUser"])) {
				//if (isset($_SESSION["currentUser"]) && (count($_SESSION["currentUser"]->getRoles($entry->getCircle()))>0 || $_SESSION["currentUser"]->isAdmin($entry->getCircle()))) {
				if ((!is_null($entry->getRole()) && $_SESSION["currentUser"]->isRole($entry->getRole()) || $_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE | \holacracy\Role::SECRETARY_ROLE,$entry->getCircle()))) {
					echo "<a class='omo-delete ajax' href='ajax/deletechecklist.php?id=".$entry->getId()."' alt='".T_("Supprimer")."' check='".T_("Etes-vous s&ucirc;r de vouloir supprimer cette check-list?")."'></a>";
					echo "<a class='omo-edit dialogPage' href='formulaires/form_checklist.php?id=".$entry->getId().(isset($circle)?"&circle=".$circle->getId():"")."' alt='".T_("Editer une check-list")."'></a>";
	
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


