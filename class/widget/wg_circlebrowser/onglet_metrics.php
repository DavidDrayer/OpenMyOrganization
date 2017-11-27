<?
	echo "<div class='omo-help-title'><b>Indicateurs:</b> Valeurs numériques permettant d'évaluer le bon fonctionnement de l'organisation. <a href='#' class='omo_act_more_help'>Afficher plus...</a></div>";
	echo "<div class='omo-more-help'>Les rôles, cercles et premiers liens peuvent définir les informations importantes à monitorer en vue d'un suivi efficace et transparent de l'activité de l'organisation. Chaque rôle/cercle est tenu de maintenir à jour ces informations avec la régularité spécifiée (hebdomadaire, mensuel,...). Voir les articles <a href='https://dev.openmyorganization.com/constitution.php?q=indicateur#idx_3_0_0' target='_constitution'>4.1.1</a> et les <a href='https://dev.openmyorganization.com/constitution.php?q=indicateur#idx_5' target='_constitution'>redevabilités des rôles structurels</a>.</div>";

	// Affichage du tutoriel de ce qu'est une checkliste et comment la créer
	if (count($metrics)==0) {
	echo "<div class='omo-warning-title'>Il n'y a aucun indicateur de défini. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
	echo "<div class='omo-more-warning'>";
			echo "Les indicateurs permettent de garder une vision globale du fonctionnement de l'organisation sous la forme de quelques chiffres clés. Découvrez ci-dessous pourquoi et comment les mettre en place.";
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
	$recurrences=\holacracy\Recurrence::getAllRecurrence($_SESSION["currentUser"]->getUserLangue());
	foreach($recurrences as $recurrence) {
		ob_start();
		echo "<div class='grey_design'>";
		echo "<div style='padding:5px; font-weight:bold;' class='ui-state-default ui-state-active ui-corner-top '>";
		echo $recurrence->getLabel();
		echo "</div>";
		echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
		$count=0;
		echo "<table width='100%' cellspacing=1 cellpadding=0>";
		foreach($metrics as $entry) {
			if ($entry->getRecurrenceId()==$recurrence->getId()) {
				echo "<tr class='highlight'><td><span style='display:inline-block; width:200px; font-weight:bold'>";
				if ($entry->getRole()) {
					// Si le rôle=le cercle affichage de Premier lien - DDR, 19.6.2014
					if ($entry->getCircle() && $entry->getRole()->getId()==$entry->getCircle()->getId()) 
						echo T_("Premier lien");
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
						if ($entry->getCircle()->getUser()) 
							echo "Premier lien: ".$entry->getCircle()->getUser()->getUserName();
						else 
							echo "Premier lien";
					}
				echo "</div>";
				echo "</td><td width='100%'>";
				if (isset($_SESSION["currentUser"])) {
				// Affiche les boutons d'édition si nécessaire
				
				echo "<span title='".str_replace("'","&apos;",$entry->getDescription())."'";
				if ((is_null($entry->getRole()) || $_SESSION["currentUser"]->getId()==$entry->getUserId() || $_SESSION["currentUser"]->isRole($entry->getRole()))) {
					echo " class='omo-me' ";
				} else echo " class='omo-greytext'";
				echo ">".$entry->getName()."</span>";
				} else {
					echo $entry->getName();
				}
				$count++;
				echo "</td><td style='min-width:120px; text-align:right; font-weight:bold; font-size:large; border:2px solid #cccccc; padding: 2px; margin:1px;'>";
				if ($entry->getNumeric()==1) {
					// Affiche la dernière valeur
					$date = new DateTime();
					$date->modify("-".$recurrence->getTimeLaps()." days");
					if ($entry->getValue()) {
					if ($entry->getValue()->getDate()<$date) {
						echo "<span style='color:red;'>".$entry->getValue()->getValue()."</span>";
					} else {
						echo $entry->getValue()->getValue();
					}
		
					
					// Si 2 valeurs, affiche la tendance entre les 2 dernières
					$values=$entry->getValues();
					if (count($values)>1) {
						if ($values[count($values)-1]->getValue()>$values[count($values)-2]->getValue()) {
							echo "<img src='/images/arrow_up.png' style='vertical-align:top'>";
						} else 					
						if ($values[count($values)-1]->getValue()<$values[count($values)-2]->getValue()) {
							echo "<img src='/images/arrow_down.png' style='vertical-align:top'>";
						} else 
							echo "<img src='/images/arrow_flat.png' style='vertical-align:top'>";
					} else {
							echo "<img src='/images/arrow_none.png' style='vertical-align:top'>";
					}
					
					// S'il y a un objectif, affiche le %
					echo "<span style='font-size:x-small; min-width:35px; display:inline-block'>";
					if ($entry->getGoal()>0) {
						echo "(".intval($entry->getValue()->getValue()/$entry->getGoal()*100)."%)";
						
					}
					echo "</span>";
					
					}
				}
				echo "</td><td><div style='text-wrap:none; width:140px;'>";
				
				if ($entry->getNumeric()==1) {
				
					echo "<a href='/formulaires/form_statistic.php?id=".$entry->getId()."&circ_id=".$entry->getCircleId()."' class='dialogPage' alt='".str_replace("'","&apos;",$entry->getName())."' >";
					echo "<img src='/images/metrics.png'>";
					echo "</a>";
				} 
				if ($entry->getFile()!="" && isset($isMember) && $isMember==true) {
				
					echo "<a href='".$entry->getFile()."'  alt='".str_replace("'","&apos;",$entry->getName())."' target='_blank'>";
					echo "<img src='/images/icon_file.png'>";
					echo "</a>";
				} 
				if (isset($_SESSION["currentUser"]) && ((!is_null($entry->getRole()) && $_SESSION["currentUser"]->isRole($entry->getRole())) || $_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE | \holacracy\Role::SECRETARY_ROLE,$entry->getCircle()))) {
					echo "<a class='omo-delete ajax' href='ajax/deletemetrics.php?id=".$entry->getId()."' alt='".T_("Supprimer")."' check='".T_("Etes-vous s&ucirc;r de vouloir supprimer cet indicateur?")."'>&nbsp;</a>";
					echo "<a class='omo-edit dialogPage' href='formulaires/form_metrics.php?id=".$entry->getId().(isset($circle)?"&circle=".$circle->getId():"")."' alt='".T_("Editer un indicateur")."'></a>";
				} 
				// Affiche un lien
				if ($entry->getFile()!="" && isset($isMember) && $isMember==true) {
					echo "<a class='omo-link dialogPage' href='/formulaires/form_link.php?id=".$entry->getId()."' alt='".T_("Insérer dans une page HTML")."'>&nbsp;</a>";
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
