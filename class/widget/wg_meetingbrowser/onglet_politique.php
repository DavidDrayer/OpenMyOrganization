<?
	$politique=$this->_meeting->getCircle()->getPolicy(); 
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

	$domains=$this->_meeting->getCircle()->getAllScopes();
	if (count($domains)>0) {

		foreach($domains as $entry) {
			if ($entry->getPolitiques()!="") {
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
	$liste=$this->_meeting->getCircle()->getLinks();
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
			
				if ($this->_meeting->getCircle()->getId()==$entry->getMasterId()) {
					echo "Par autorité de ce cercle, le rôle [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du rôle doit se faire en gouvernance.'>Rôle indéfini</span>")."] est représenté dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				} else
				if ($this->_meeting->getCircle()->getId()==$entry->getSourceCircleId()) {
					echo "Par autorité du cercle [".$entry->getMaster()->getName()."], le rôle [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du rôle doit se faire en gouvernance.'>Rôle indéfini</span>")."] est représenté dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				} else
				if ($this->_meeting->getCircle()->getId()==$entry->getSuperCircleId()) {
					echo "Par autorité du cercle [".$entry->getMaster()->getName()."], le rôle [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du rôle doit se faire en gouvernance.'>Rôle indéfini</span>")."] est représenté dans ce cercle, sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				}
			} else {
				// Sinon c'est un lien transverse de cercle
				if ($this->_meeting->getCircle()->getId()==$entry->getMasterId()) {
					echo "Par autorité de ce cercle, le cercle [".@$entry->getSourceCircle()->getName()."] est représenté dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				} else
				if ($this->_meeting->getCircle()->getId()==$entry->getSourceCircleId()) {
					echo "Par autorité du cercle [".$entry->getMaster()->getName()."], le cercle [".@$entry->getSourceCircle()->getName()."] est représenté dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				} else
				if ($this->_meeting->getCircle()->getId()==$entry->getSuperCircleId()) {
					echo "Par autorité du cercle [".$entry->getMaster()->getName()."], le cercle [".@$entry->getSourceCircle()->getName()."] est représenté dans ce cercle, sous forme de lien transverse. Il y porte sa raison d'être, ses redevabilités et ses domaines.";
				}
			}
			echo "</div>";
			
		}
		echo "</div>";	
		echo "</fieldset>";
	}

	echo "</div>";

?>
