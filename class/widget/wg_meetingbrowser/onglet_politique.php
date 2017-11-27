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
				echo $entry->getDescription()."</b> (r�le ".$entry->getRole()->getName().")";;
				echo "</h3>";
				echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
				if ($entry->getPolitiques()!="") {
					echo $entry->getPolitiques();
				} else {
					echo "Aucune politique d�finie pour ce domaine";
					}
				echo "</div>";
			}
		}
		
	}
	echo "</div>";	
//	echo "</fieldset>";
	
	// Affichage des politiques h�rites des cercles sup�rieurs
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
	
	// Affiche �galement les politiques associ�es aux liens transverses 
	$liste=$this->_meeting->getCircle()->getLinks();
	if (count($liste)>0) {
	 echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Politiques de liens transverses</span><div id='mask2'></div></legend>";
	echo " <div class='grey_design accordion_role'>";

		foreach($liste as $entry) {
			echo "<h3><b>";
			if ($entry->getSourceId()>0) {
				echo "R�le [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du r�le doit se faire en gouvernance.'>R�le ind�fini</span>")."] vers [".@$entry->getSuperCircle()->getName()."]";
			} else {
				echo "Cercle [".@$entry->getSourceCircle()->getName()."] vers [".@$entry->getSuperCircle()->getName()."]";
			}
			echo "</b></h3>";
			echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";
			// Est-ce un lien transverse de r�le?
			if ($entry->getSourceId()>0) {
			
				if ($this->_meeting->getCircle()->getId()==$entry->getMasterId()) {
					echo "Par autorit� de ce cercle, le r�le [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du r�le doit se faire en gouvernance.'>R�le ind�fini</span>")."] est repr�sent� dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'�tre, ses redevabilit�s et ses domaines.";
				} else
				if ($this->_meeting->getCircle()->getId()==$entry->getSourceCircleId()) {
					echo "Par autorit� du cercle [".$entry->getMaster()->getName()."], le r�le [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du r�le doit se faire en gouvernance.'>R�le ind�fini</span>")."] est repr�sent� dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'�tre, ses redevabilit�s et ses domaines.";
				} else
				if ($this->_meeting->getCircle()->getId()==$entry->getSuperCircleId()) {
					echo "Par autorit� du cercle [".$entry->getMaster()->getName()."], le r�le [".($entry->getSourceId()>0?$entry->getSource()->getName():"<span class='omo-warning' title='L&#39;affectation du r�le doit se faire en gouvernance.'>R�le ind�fini</span>")."] est repr�sent� dans ce cercle, sous forme de lien transverse. Il y porte sa raison d'�tre, ses redevabilit�s et ses domaines.";
				}
			} else {
				// Sinon c'est un lien transverse de cercle
				if ($this->_meeting->getCircle()->getId()==$entry->getMasterId()) {
					echo "Par autorit� de ce cercle, le cercle [".@$entry->getSourceCircle()->getName()."] est repr�sent� dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'�tre, ses redevabilit�s et ses domaines.";
				} else
				if ($this->_meeting->getCircle()->getId()==$entry->getSourceCircleId()) {
					echo "Par autorit� du cercle [".$entry->getMaster()->getName()."], le cercle [".@$entry->getSourceCircle()->getName()."] est repr�sent� dans le cercle [".@$entry->getSuperCircle()->getName()."], sous forme de lien transverse. Il y porte sa raison d'�tre, ses redevabilit�s et ses domaines.";
				} else
				if ($this->_meeting->getCircle()->getId()==$entry->getSuperCircleId()) {
					echo "Par autorit� du cercle [".$entry->getMaster()->getName()."], le cercle [".@$entry->getSourceCircle()->getName()."] est repr�sent� dans ce cercle, sous forme de lien transverse. Il y porte sa raison d'�tre, ses redevabilit�s et ses domaines.";
				}
			}
			echo "</div>";
			
		}
		echo "</div>";	
		echo "</fieldset>";
	}

	echo "</div>";

?>
