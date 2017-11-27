<?
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");

	// Y a-til une action demand�e?
	if (isset($_POST["action"])) {
	
		// Edition ou cr�ation d'une org
		if ($_POST["action"]=="editOrg") {
			// Charge l'organisation (si elle existe)
			if (isset($_POST["org"]) && $_POST["org"]>0) {
				$organisation=$_SESSION["currentManager"]->loadOrganisation($_POST["org"]);
				
				// Le user courant est-il administrateur de cette organisation?
				
				// Si non, affiche un message d'erreur
				
			} else {
				// Sinon en cr�e une nouvelle
				
				// Le user courant a-t-il le droit d'en ajouter une nouvelle? Selon les conditions d'abonnement, etc.
				
				// Cr�e effectivement l'organisation
				$organisation=new \holacracy\Organisation($_SESSION["currentManager"]);
			}
			
			// Modifie les informations
			$organisation->setName(utf8_decode($_POST["form_orgname"]));
			$organisation->setShortName (utf8_decode($_POST["form_shortname"]));
			$organisation->setDescription (utf8_decode($_POST["form_description"]));
			$organisation->setWebSite (utf8_decode($_POST["form_weborg"]));
			$organisation->setVision(utf8_decode($_POST["form_vision"]));
			$organisation->setVisionDescription(utf8_decode($_POST["form_descriptifv"]));
			$organisation->setMission(utf8_decode($_POST["form_mission"]));
			$organisation->setMissionDescription(utf8_decode($_POST["form_descriptifm"]));
			//$organisation->setPurpose(utf8_decode($_POST["form_purpose"]));
			//$organisation->setPurposeDescription(utf8_decode($_POST["form_descriptifre"]));
			$organisation->setVisibility($_POST["form_visibilityorg"]);
			// Sauve l'objet
			$_SESSION["currentManager"]->save($organisation);
			
			// L'organisation a-t-elle des cercles? Si non, cr�e une structure de base
			if (count($organisation->getCircles()) == 0) {
				
				// Ajoute le membre courant comme membre de l'org et administrateur
				$_SESSION["currentManager"]->addMemberOrganisation($_SESSION["currentUser"]->getId(),$organisation->getId(),1);
				
				// Cr�e un cercle de base
				$circle=new \holacracy\Circle($_SESSION["currentManager"]);
				$circle->setOrganisation($organisation);
				$circle->setName(utf8_decode($_POST["form_ancrage"]));
				$circle->setUserId($_SESSION["currentUser"]->getId());
				$_SESSION["currentManager"]->save($circle);
				
				// Ajoute les r�les structurels
				$premier=new \holacracy\Role();
				$premier->setType(\holacracy\Role::LEAD_LINK_ROLE);
				$premier->setName("Premier lien");
				$premier->setSourceId(\holacracy\Role::LEAD_LINK_ROLE);
				$circle->attachRole($premier);
				// Attache le r�le de 1er lien � la personne en cours
				$_SESSION["currentManager"]->save($premier);
				
				// Pas de second lien pour le cercle d'ancrage
				
				$secretaire=new \holacracy\Role();
				$secretaire->setType(\holacracy\Role::SECRETARY_ROLE);
				$secretaire->setName("Secr�taire");
				$secretaire->setSourceId(\holacracy\Role::SECRETARY_ROLE);
				$circle->attachRole($secretaire);
				$_SESSION["currentManager"]->save($secretaire);
				
				$facilitateur=new \holacracy\Role();
				$facilitateur->setType(\holacracy\Role::FACILITATOR_ROLE);
				$facilitateur->setName("Facilitateur");
				$facilitateur->setSourceId(\holacracy\Role::FACILITATOR_ROLE);
				$circle->attachRole($facilitateur);
				$_SESSION["currentManager"]->save($facilitateur);
				
			} else {
				// Sinon, met � jour les infos
				$circles=$organisation->getCircles();
				$rootCircle=$circles[1];
				
				
				$rootCircle->setName(utf8_decode($_POST["form_ancrage"]));
				$rootCircle->setUserId($_POST["form_firstlink"]);
				$_SESSION["currentManager"]->save($rootCircle);
			}
			// R�cup�re la liste de administrateurs actuels
			$admins=$organisation->getAdmins();
			// Pour chacun, s'assure qu'il est toujours valable, sinon le transforme en utilisateur simple
			foreach ($admins as $admin) {
				if (!in_array($admin->getId(),$_POST['form_admin'])) {
					$_SESSION["currentManager"]->addMemberOrganisation($admin->getId(),$organisation->getId(),0);
				}
			}
			
			// Ajoute ou met � jour la liste des administrateurs
			foreach ($_POST['form_admin'] as $selectedOption) {
				$_SESSION["currentManager"]->addMemberOrganisation($selectedOption,$organisation->getId(),1);
			}
			
			// Recharge l'organisation
			$organisation=$_SESSION["currentManager"]->loadOrganisation($organisation->getId());
			
?>
			<script>
		
				alert("Les modifications ont bien �t� sauvegard�es.");

			</script>
<?
	
		}	
	}
	
	// Affiche le contenu du formulaire
		$visibilities = array();
		
		// D�fini le contenu des listes � choix pour la configuration
	
		$visibilities[0] = "priv&eacute;e (seuls les membres peuvent acc�der aux informations)";
		$visibilities[1] = "semi-priv&eacute;e (ceux qui poss�dent l'URL peuvent acc�der aux informations)";
		$visibilities[2] = "public (tout le monde peut acc�der � la structure)";
		
		echo "<input type='hidden' id='form_target' value='/formulaires/form_editorg.php'>";
		echo "<input type='hidden' name='org' value='".$organisation->getId()."'>";
		echo "<input type='hidden' name='action' value='editOrg'>";
		
		echo "<table style='width:100%;height:100%;' class='containment-wrapper' cellspacing=0>";
		echo "<tr><td style='width:48%;padding-right:2%;'>";
		
			echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>La raison d&rsquo;&ecirc;tre de votre organisation</span><div id='mask2'></div></legend>";

				// Syst�me � onglets pour s�parer la vision, la mission et la raison d'�tre du cercle d'ancrage
				echo '<div id="tabs_vision">';
				echo '  <ul>';
				echo '	<li><a href="#tabs-21">Raison d\'�tre</a></li>';
				echo '	<li><a href="#tabs-22">Sc�ne de R�ve</a></li>';
				echo '	<li><a href="#tabs-23">Objectifs</a></li>';
				echo '  </ul>';
				echo '  <div id="tabs-21">';
					echo "<div class='omo-fieldset omo-fullwidth'>".
					"<div class='omo-label'>Description de la Vision</div>".
					"<div class='omo-fieldhelp'>Quelle soci�t� voulez-vous voir demain? A quoi aspirez-vous?</div>".
					"<div class='omo-field'><textarea style='width:100%; height:130px' type='text' id='form_vision' name='form_vision' placeholder='par exemple: Une soci�t� ou la culture lit�raire est � la port�e de chacun.'>".str_replace("'","&apos;",$organisation->getVision())."</textarea></div>".
					"</div>";

					echo "<div class='omo-fieldset omo-fullwidth'>".
					"<div class='omo-label'>Description de la Mission</div>".
					"<div class='omo-fieldhelp'>Par quel moyen allez-vous vous y prendre? Qu'allez-vous construire?</div>".
					"<div class='omo-field'><textarea style='width:100%; height:130px' type='text' id='form_mission' name='form_mission'  placeholder='par exemple: Nous cr�ons un lieu en ville o� les citoyens peuvent venir louer ou acheter des livres.'>".str_replace("'","&apos;",$organisation->getMission())."</textarea></div>".
					"</div>";

				echo '  </div>';
				echo '  <div id="tabs-22">';

					echo "<div class='omo-fieldset omo-fullwidth'>".
					"<div class='omo-label'>Sc�ne de r�ve</div>".
					"<div class='omo-fieldhelp'>D�tail de ce que l'on souhaite accomplir.</div>".
					"<div class='omo-field'><textarea id='form_descriptifm' name='form_descriptifm' style='width:100%;padding:5px;height:300px; min-height:90px;'>".$organisation->getMissionDescription()."</textarea></div>";
					"</div>";
				echo '  </div>';
				echo '  </div>';
				echo '  <div id="tabs-23">';

					echo "<div class='omo-fieldset omo-fullwidth'>".
					"<div class='omo-label'>Objectifs</div>".
					"<div class='omo-fieldhelp'>Quand pourrons-nous dire que nous avons r�ussi?</div>".
					"<div class='omo-field'><textarea id='form_descriptifv' name='form_descriptifv' style='width:100%;padding:5px;height:300px;min-height:90px;'>".$organisation->getVisionDescription()."</textarea></div>";
					"</div>";

				echo '  </div>';
				echo '</div>';


			

							
		// 2�me colonne
		echo "</td>";
		echo "<td style='width:48%;padding-left:2%;'>";

			echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Vos valeurs</span><div id='mask2'></div></legend>";
			echo "</fieldset>";

			
			// Sauvegarde 
			echo "<div style='text-align:right;'><br/><br/><input type='button' class='save_org' value='".T_("Enregistrer")."'></div>";
		echo "</td></tr></table>";
?>
<script>

		// D�fini la hauteur max pour les �l�ments TAB
		$("#tabs_vision").tabs();

</script>

