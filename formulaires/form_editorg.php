<?
	$root = realpath($_SERVER["DOCUMENT_ROOT"]);
	include_once("$root/include.php");

	// Y a-til une action demandée?
	if (isset($_POST["action"])) {
	
		// Edition ou création d'une org
		if ($_POST["action"]=="editOrg") {
			
			// Contrôle la validité des champs
			if (!isset($_POST["form_orgname"]) || $_POST["form_orgname"]=="") {
				echo "//Erreur\nalert('Veuillez remplir le nom de l\'organisation');"; 
				exit;
			}
			if (!isset($_POST["form_shortname"]) || $_POST["form_shortname"]=="") {
				echo "//Erreur\nalert('Veuillez donner un nom court à l\'organisation');"; 
				exit;
			}
			if (!isset($_POST["form_description"]) || $_POST["form_description"]=="") {
				echo "//Erreur\nalert('Veuillez renseigner un bref descriptif');"; 
				exit;
			}
			if (!isset($_POST["form_ancrage"]) || $_POST["form_ancrage"]=="") {
				echo "//Erreur\nalert('Veuillez définir le nom du cercle d\'ancrage');"; 
				exit;
			}
			if (!isset($_POST["form_weborg"]) || $_POST["form_weborg"]=="") {
				echo "//Erreur\nalert('Veuillez préciser le site web');"; 
				exit;
			}
			
			// Charge l'organisation (si elle existe)
			if (isset($_POST["org"]) && $_POST["org"]>0) {
				$organisation=$_SESSION["currentManager"]->loadOrganisation($_POST["org"]);
				
				// Le user courant est-il administrateur de cette organisation?
				
				// Si non, affiche un message d'erreur
				
			} else {
				// Sinon en crée une nouvelle
				
				// Le user courant a-t-il le droit d'en ajouter une nouvelle? Selon les conditions d'abonnement, etc.
				
				// Crée effectivement l'organisation
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
//			$organisation->setPurpose(utf8_decode($_POST["form_purpose"]));
	//		$organisation->setPurposeDescription(utf8_decode($_POST["form_descriptifre"]));
			$organisation->setVisibility($_POST["form_visibilityorg"]);
			// Sauve l'objet
			$_SESSION["currentManager"]->save($organisation);
			
			// L'organisation a-t-elle des cercles? Si non, crée une structure de base
			if (count($organisation->getCircles()) == 0) {
				
				// Ajoute le membre courant comme membre de l'org et administrateur
				$_SESSION["currentManager"]->addMemberOrganisation($_SESSION["currentUser"]->getId(),$organisation->getId(),1);
				
				// Crée un cercle de base
				$circle=new \holacracy\Circle($_SESSION["currentManager"]);
				$circle->setOrganisation($organisation);
				$circle->setName(utf8_decode($_POST["form_ancrage"]));
				$circle->setUserId($_SESSION["currentUser"]->getId());
				$_SESSION["currentManager"]->save($circle);
				
				// Ajoute les rôles structurels
				$premier=new \holacracy\Role();
				$premier->setType(\holacracy\Role::LEAD_LINK_ROLE);
				$premier->setName("Premier lien");
				$premier->setSourceId(\holacracy\Role::LEAD_LINK_ROLE);
				$circle->attachRole($premier);
				// Attache le rôle de 1er lien à la personne en cours
				$_SESSION["currentManager"]->save($premier);
				
				// Pas de second lien pour le cercle d'ancrage
				
				$secretaire=new \holacracy\Role();
				$secretaire->setType(\holacracy\Role::SECRETARY_ROLE);
				$secretaire->setName("Secrétaire");
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
				// Sinon, met à jour les infos
				$circles=$organisation->getCircles();
				$rootCircle=$circles[1];
				
				
				$rootCircle->setName(utf8_decode($_POST["form_ancrage"]));
				$rootCircle->setUserId($_POST["form_firstlink"]);
				$_SESSION["currentManager"]->save($rootCircle);
			}
			// Récupère la liste de administrateurs actuels
			$admins=$organisation->getAdmins();
			// Pour chacun, s'assure qu'il est toujours valable, sinon le transforme en utilisateur simple
			foreach ($admins as $admin) {
				if (!in_array($admin->getId(),$_POST['form_admin'])) {
					$_SESSION["currentManager"]->addMemberOrganisation($admin->getId(),$organisation->getId(),0);
				}
			}
			
			// Ajoute ou met à jour la liste des administrateurs
			foreach ($_POST['form_admin'] as $selectedOption) {
				$_SESSION["currentManager"]->addMemberOrganisation($selectedOption,$organisation->getId(),1);
			}
			
			// Recharge l'organisation
			$organisation=$_SESSION["currentManager"]->loadOrganisation($organisation->getId());
			
?>
			<script>
		
				alert("Les modifications ont bien été sauvegardées.");
				location="/editorg.php?id=<?=$organisation->getId() ?>"; 

			</script>
<?
			exit;
		}	
	}
	
	// Affiche le contenu du formulaire
		$visibilities = array();
		
		// Défini le contenu des listes à choix pour la configuration
	
		$visibilities[0] = "priv&eacute;e (seuls les membres peuvent accéder aux informations)";
		$visibilities[1] = "semi-priv&eacute;e (ceux qui possèdent l'URL peuvent accéder aux informations)";
		$visibilities[2] = "public (tout le monde peut accéder à la structure)";
		
		echo "<input type='hidden' id='form_target' value='/formulaires/form_editorg.php'>";
		echo "<input type='hidden' name='org' value='".$organisation->getId()."'>";
		echo "<input type='hidden' name='action' value='editOrg'>";
		
		echo "<table style='width:100%;height:100%;' class='containment-wrapper' cellspacing=0>";
		echo "<tr><td style='width:48%;padding-right:2%;'>";
		
			$langue = $organisation->getLanguage();
			$security = $organisation->getVisibility();
			$circles = $organisation->getCircles();
			$members = $organisation->getMembers(); 
			if (count($members)==0) $members[0]=$_SESSION["currentUser"];
			$firstlink=$_SESSION["currentUser"]->getId();
			$ancrage="";
	
					
			foreach ($circles as $circleancrage){
				$ancragepurpose = $circleancrage->getPurpose();
				$ancragepurpose = str_replace("'","&rsquo;",$ancragepurpose);
				$firstlink = $circleancrage->getUserId(); //chope le role de 1er lien
				$ancrage=$circleancrage->getName();
			}

			echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Votre organisation</span><div id='mask2'></div></legend>";
				// Nom de l'org
				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Nom de l'organisation</div>".
				"<div class='omo-field'><input style='width:100%' type='text' id='form_orgname' name='form_orgname' value='".str_replace("'","&apos;",$organisation->getName())."' placeholder='par exemple: Librairie l&apos;Air de Rien Sàrl'></div>".
				"</div>";

				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Nom abrégé</div>".
				"<div class='omo-fieldhelp'>Pour l'envoi de messages ou l'affichage dans les barres de navigation.</div>".
				"<div class='omo-field'><input style='width:50%' type='text' id='form_shortname' name='form_shortname' value='".str_replace("'","&apos;",$organisation->getShortName())."' placeholder='par exemple: Librairie AdR'></div>".
				"</div>";

				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Nom du cercle d'ancrage</div>".
				"<div class='omo-fieldhelp'>Nom du premier cercle de votre organisation</div>".
				"<div class='omo-field'><input style='width:50%' type='text' id='form_ancrage' name='form_ancrage' value='".str_replace("'","&apos;",$ancrage)."' placeholder='par exemple: Comité'></div>".
				"</div>";

				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Description</div>".
				"<div class='omo-fieldhelp'>Pour la recherche d'organisation lorsque la visibilité est définie à public.</div>".
				"<div class='omo-field'><input style='width:100%' type='text' id='form_description' name='form_description' value='".str_replace("'","&apos;",$organisation->getDescription())."' placeholder='par exemple: librairie spécialisée dans les ouvrages anciens'></div>".
				"</div>";
				
				echo "<div class='omo-fieldset omo-fullwidth'>".
				"<div class='omo-label'>Site Web</div>".
				"<div class='omo-field'><input style='width:100%' type='text' id='form_weborg' name='form_weborg' value='".str_replace("'","&apos;",$organisation->getWebSite())."' placeholder='par exemple: http://www.airderien.ch'></div>".
				"</div>";
			echo "</fieldset>";


							
		// 2ème colonne
		echo "</td>";
		echo "<td style='width:48%;padding-left:2%;'>";

			// Affichage du premier lien et des administrateurs de l'org
			echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Administration de l'organisation</span><div id='mask2'></div></legend>";
				
			echo "<div class='omo-fieldset omo-fullwidth'>".
			"<div class='omo-label'>Liste des administrateurs</div>".
			"<div class='omo-fieldhelp'>Membres ayant accès à cette page de configuration</div>";

			echo "<select id='form_admin' name='form_admin[]' data-placeholder='Choisissez un ou plusieurs administrateurs...' class='chosen-select' multiple style='width:100%' tabindex='4'>";
			foreach($members as $member){
			if($member->getId()==$_SESSION["currentUser"]->getId() || $organisation->isAdmin($member)){ echo '<option value="'.$member->getId().'" selected>'.$member->getUserName().'</option>'; }
			else{echo '<option value="'.$member->getId().'">'.$member->getUserName().'</option>';} 
			}
			echo "</select>";
			
			echo "</div>";
			
			echo "<div class='omo-fieldset omo-fullwidth'>".
			"<div class='omo-label'>1er lien du cercle d'ancrage</div>";

			echo "<select class='chosen-select' style='width:100%' id='form_firstlink' name='form_firstlink'>";				
			foreach($members as $member){
			if($member->getId() == $firstlink){ echo '<option value="'.$member->getId().'" selected>'.$member->getUserName().'</option>'; }
			else{echo '<option value="'.$member->getId().'">'.$member->getUserName().'</option>';} 
			}
			echo "</select>";
			echo "</div>";
				
							
			echo "</fieldset>";

			echo "<fieldset><legend><div id='mask1'></div><span class='omo-structural'>Visibilit&eacute; de l'organisation</span><div id='mask2'></div></legend>";
				echo "<div class='omo-fieldset omo-fullwidth'>".
					"<div class='omo-fieldhelp'>Qui peut voir les infos de votre organisation?</div>";

				echo "<select id='form_visibilityorg' class='chosen-select' name='form_visibilityorg'>";	
				foreach($visibilities as $key=>$visibility){
				if($security == $key){ echo '<option value="'.$visibility.'" selected>'.$visibility.'</option>'; }
				else{echo '<option value="'.$key.'">'.$visibility.'</option>';} 
				}			
				echo "</select>";
				echo "</div>";
			echo "</fieldset>";
			

			
			// Sauvegarde 
			echo "<div style='text-align:right;'><br/><br/><input type='button' class='save_org' value='".T_("Enregistrer")."'></div>";
		echo "</td></tr></table>";
?>
<script>
	$(document).ready(function() {
		$("#form_firstlink").select2({
			minimumResultsForSearch: 10,
			theme: "classic"
		});
		$("#form_admin").select2({
			placeholder: "Choisissez un ou plusieurs administrateurs",
			minimumResultsForSearch: 10,
			theme: "classic"
		});
		$("#form_admin").on("select2:unselecting", function (e) { if (e.params.args.data.id == <?=$_SESSION["currentUser"]->getId()?> ) { alert("Vous ne pouvez pas supprimer votre propre compte."); return false;} });
		$("#form_visibilityorg").select2({		
			minimumResultsForSearch: Infinity,
			theme: "classic"
		});
	});	

</script>

