<?

	echo "<div class='omo-help-title'><b>Politiques:</b> Liste des règles qui s'appliquent à ce cercle et à tous les sous-cercles. <a href='#' class='omo_act_more_help'>Afficher plus...</a></div>";
	echo "<div class='omo-more-help'>Chaque membre du cercle a la possibilité de proposer en réunion de gouvernance de nouvelles règles, appelées politiques, qui s'appliqueront à tous les rôles et sous-cercles de ce cercle. Ces politiques peuvent être questionnées et modifiées lors de chaque réunion de gouvernance. Voir les articles <a href='https://dev.openmyorganization.com/constitution.php?q=politique#idx_1_0_0' target='_constitution'>2.1.1</a> et le <a href='https://dev.openmyorganization.com/constitution.php?q=politique#idx_2_1_0' target='_constitution'>processus de réunion de gouvernance</a>.</div>";

	$politique=$this->_circle->getPolicy(); 
	
	// Affichage du tutoriel de ce qu'est une checkliste et comment la créer
	if (count($politique)==0) {
	echo "<div class='omo-warning-title'>Il n'y a aucune politique de définie. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
	echo "<div class='omo-more-warning'>";
			echo "Les politiques sont les règles valables pour le cercle et ses sous-cercles, définies en consensus durant les réunions de gouvernance. Découvrez ci-dessous pourquoi et comment les mettre en place.";
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
	
	
	echo "<div class='omo-cols'>";
	
	
//	 echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Politiques de ce cercle</span><div id='mask2'></div></legend>";
	echo " <div class='grey_design accordion_role'>";
 
	foreach($politique as $entry) {
		echo "<h3><b>";
		echo $entry->getTitle();
		echo "</b></h3>";
		echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
		echo $entry->getDescription();
		echo "</div>";
	}

	$domains=$this->_circle->getAllScopes();
	if (count($domains)>0) {

		foreach($domains as $entry) {
			if ($entry->getPolitiques()!="" || $_SESSION["currentUser"]->isRole($entry->getRole())) {
				echo "<h3><b";
					if ($entry->getPolitiques()=="" && $_SESSION["currentUser"]->isRole($entry->getRole())) echo " class='omo-warning' ";
				echo ">";
				echo $entry->getDescription()."</b> (rôle ".$entry->getRole()->getName().")";;
				echo "</h3>";
				echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
				if ($entry->getPolitiques()!="") {
					echo $entry->getPolitiques();
				} else {
					echo "Aucune politique définie pour ce domaine";
					echo '<img src="style/templates/images/add-politics.png" href="formulaires/form_politic-scop.php?domaine='.$entry->getId().'" class="dialogPage" alt="Politiques du domaine ['.$entry->getDescription().']" title="Créer des politiques pour ce domaine" style="width:14px;height:13px;cursor:pointer;">';
				}
				echo "</div>";
			}
		}
		
	}
	echo "</div>";	
//	echo "</fieldset>";
	
	// Affichage des politiques hérites des cercles supérieurs
	/*echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Politiques globales</span><div id='mask2'></div></legend>";
	echo " <div class='grey_design accordion_role'>";

	$politique=$this->_circle->getSubPolicy(); 
	echo "<div class='omo-cols'>";
	
	
	 echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Politiques de ce cercle</span><div id='mask2'></div></legend>";
	echo " <div class='grey_design accordion_role'>";
 
	foreach($politique as $entry) {
		echo "<h3><b>";
		echo $entry->getTitle();
		echo "</b></h3>";
		echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
		echo $entry->getDescription();
		echo "</div>";
	}
	
	echo "</div>";	
	echo "</fieldset>";*/
	
	// Affiche également les politiques associées aux liens transverses 
	$liste=$this->_circle->getLinks();
	if (count($liste)>0) {
	 echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Politiques de liens transverses</span><div id='mask2'></div></legend>";
	echo " <div class='grey_design accordion_role'>";

		foreach($liste as $entry) {
			echo "<h3><b>";
			if ($entry->getSourceId()>0) {
				echo "Rôle [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du rôle doit se faire en gouvernance.'>Rôle indéfini</span>")."] vers [".@$entry->getSuperCircle()->getName()."]";
			} else {
				echo "Cercle [".@$entry->getSourceCircle()->getName()."] vers [".@$entry->getSuperCircle()->getName()."]";
			}
			echo "</b></h3>";
			echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
			// Est-ce un lien transverse de rôle?
			if ($entry->getSourceId()>0) {
			
				if ($this->_circle->getId()==$entry->getMasterId()) {
					echo "Par autorité de ce cercle, le rôle [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du rôle doit se faire en gouvernance.'>Rôle indéfini</span>")."] est représenté dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				} else
				if ($this->_circle->getId()==$entry->getSourceCircleId()) {
					echo "Par autorité du cercle [".$entry->getMaster()->getName()."], le rôle [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du rôle doit se faire en gouvernance.'>Rôle indéfini</span>")."] est représenté dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				} else
				if ($this->_circle->getId()==$entry->getSuperCircleId()) {
					echo "Par autorité du cercle [".$entry->getMaster()->getName()."], le rôle [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du rôle doit se faire en gouvernance.'>Rôle indéfini</span>")."] est représenté dans ce cercle, sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				}
			} else {
				// Sinon c'est un lien transverse de cercle
				if ($this->_circle->getId()==$entry->getMasterId()) {
					echo "Par autorité de ce cercle, le cercle [".@$entry->getSourceCircle()->getName()."] est représenté dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				} else
				if ($this->_circle->getId()==$entry->getSourceCircleId()) {
					echo "Par autorité du cercle [".$entry->getMaster()->getName()."], le cercle [".@$entry->getSourceCircle()->getName()."] est représenté dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				} else
				if ($this->_circle->getId()==$entry->getSuperCircleId()) {
					echo "Par autorité du cercle [".$entry->getMaster()->getName()."], le cercle [".@$entry->getSourceCircle()->getName()."] est représenté dans ce cercle, sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				}
			}
			echo "</div>";
			
		}
		echo "</div>";	
		echo "</fieldset>";
	}

	echo "</div>";
	if (count($politique)>0) {
		echo "<a href='https://dev.openmyorganization.com/pdf/circle.php?id=".$this->_circle->getId()."&display=policy'><img src='/style/templates/common/images/share_pdf.png'></a>";
	}
?>
