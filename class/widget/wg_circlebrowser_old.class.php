<?php
	namespace widget;
// Cette classe affiche un browser HTML permettant de parcourir un objet de type "cercle" dans son
// intégralité : liste de rôles, sous-cercles, projets, liste de membres, etc...
class wg_circleBrowser extends Widget
{
	// l'élément cercle à afficher
	private $_circle;

	// Constructeur nécessitant le cercle à afficher
	// Entrée : le cercle à afficher
	// Sortie : un objet de type wg_circleBrowser
	public function __construct(\holacracy\Circle $circle)
	{
		$this->_circle=$circle;
	}
	
	// Procédure interne pour afficher une liste de rôles
	// Entrée: la liste de rôles à afficher sous forme d'array()
	// Sortie : à l'écran
	private	function _listeRole ($liste, $column=1) {
		if ($column>1) {echo "<table border=0 cellspacing=0 cellpadding=0 width='100%'><tr><td style='padding-right:20px;'>";}
		for ($j=0; $j<count($liste);$j++) {
			$role=$liste[$j];
			if ($column>1 && $j==2) echo "</td><td width='50%'>";
			
			// Affiche le nom du rôle (le H3 est pour l'affichage sous forme d'accordéon jquery-ui)
			//echo "<div class='grey_design'>";
			echo "<div class='grey_design accordion_role' id='tab_role_".$role->getId()."'><h3><span class='reload'><span class='omo-role-".$role->getType()."'>";

			echo "<b>".$role->toHTMLString()."</b></span>";
			echo "<span id='role_".$role->getId()."' class='omo-accordion-info'>";
			// Charge la liste des gens en charge du rôle
			$roleFillers=$role->getRoleFillers();
			if (count($roleFillers)>0) {
				// Si c'est un cercle, affiche le RoleFiller comme premier lien
				if ($role instanceof \holacracy\Circle) {
					echo " 1er lien : ";
					echo $roleFillers[0]->getUserName();
					
					// Et affiche le second lien
					$repLink=$role->getRoles(\holacracy\Role::REP_LINK_ROLE);
					if (count($repLink)>0) {
						echo " - 2nd lien : ";
						$roleFillers2=$repLink[0]->getRoleFillers();
						if (count($roleFillers2)>0) {
							echo $roleFillers2[0]->getUserName();
							//desaffecte un 2nd lien
						} else {
							echo "Non affecté";
						}
						
					}
				} else {
					// Sinon, affiche par qui le rôle est énergétisé (plusieurs personnes possible)
					echo " Energétisé par : ";
					for ($i=0;($i<count($roleFillers) && count($roleFillers)<=3) || ($i<2 && count($roleFillers)>3); $i++){
						if ($i>0) {echo " , ";}
						echo "<span title='".$roleFillers[$i]->getFirstName()." ".$roleFillers[$i]->getLastName()."'>".$roleFillers[$i]->getUserName()." </span>";
						$type = $role->getType();
					//	}
					}
					if ($i<count($roleFillers)) echo " et <u>".(count($roleFillers)-$i)." autres personnes</u>";
				
				}
			}
			else{  //Si on a pas de role affecté on affiche non affecté et le bouton pour affecter un membre du cercle
				echo " Non affecté ";
				$type = $role->getType();
			}
			$type = $role->getType();
			if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) && $type != 2 || $_SESSION["currentUser"]->isAdmin())){ //Si le role n'est pas un 1er Lien
				echo "<img src='images/edit-user.png' href='formulaires/form_affect_user.php?circle=".$this->_circle->getId()."&role=".$role->getId()."' class='dialogPage' alt='Assigner le rôle ".$role->getName()."'/>";
			}
			echo "</span>";
			// Affichage du résumé du rôle/cercle
			echo "</span></h3><div><table style='width:100%'><tr><td style='width:66%'>";
			// S'il y a une raison d'être, l'affiche
			if ($role->getPurpose()!="") {
				echo "<fieldset class='light'><legend><div id='mask1'></div><span class='omo-purpose'>Raison d'être</span><div id='mask2'></div></legend>";
				echo $role->getPurpose()."</fieldset>";
			}
			
			// S'il y a une raison d'être, l'affiche
			if ($role->getType()==\holacracy\Role::CIRCLE && $role->getStrategy()!="") {
				echo "<div class='omo-light-accordion light'><h3><span class='omo-strategy omo-label'>Strategie</span></h3><div>".$role->getStrategy()."</div></div>";
			}

			// S'il y a un domaine, l'affiche
			if (count($role->getScopes())>0) {
				echo "<div class='omo-light-accordion light'><h3><span class='omo-scope omo-label'>Domaines</span></h3><div>";
				echo "<ul>";
				foreach ($role->getScopes() as $scope) {
					echo "<li>".$scope->getDescription()."</li>";
				}			
				echo "</ul>";
			}
			echo "</div></div>";
			
			// S'il y a un domaine, l'affiche
			if (count($role->getAccountabilities())>0) {
				echo "<div class='omo-light-accordion light'><h3><span class='omo-accountabilities omo-label'>Redevabilités</span></h3><div>";
				echo "<ul>";
				foreach ($role->getAccountabilities() as $accountability) {
					echo "<li>".$accountability->getDescription()."</li>";
				}			
				echo "</ul>";
			}
			echo "</div></div></td>";
			
			if ($column>1) {echo "</tr><tr>";}
			echo "<td>";
			
			// Affiche le détail des rôles fillers avec l'intégralité du nom, ainsi que les focus
			if (count($roleFillers)>0) {
				echo "<fieldset class='light'><legend><div id='mask1'></div><span class='omo-user'>Energétisé par</span><div id='mask2'></div></legend>";
				for ($i=0;$i<count($roleFillers); $i++){
					//echo "<table><tr>";
					echo "<div class='omo-user-block ui-corner-all'>";
					if (checkMini("/images/user/".$roleFillers[$i]->getUserId().".jpg",30,30,"mini",1,5)) {
						echo "<a href='/user.php?id=".$roleFillers[$i]->getUserId()."&circle=".$role->getSuperCircle()->getId()."' class='dialogPage' alt='Profil de ".$roleFillers[$i]->getFirstName()." ".$roleFillers[$i]->getLastName()."'><img class='omo-user-img' src='/images/user/mini/".$roleFillers[$i]->getUserId().".jpg'/></a>";
					}
					// Affiche quelques infos et le menu USER			
					echo "<b>".$roleFillers[$i]->getFirstName()." ".$roleFillers[$i]->getLastName()."</b><br>";
					if (count($roleFillers)>1) { 
						echo " pour ";
						if ($roleFillers[$i]->getFocus()!="") {
							echo $roleFillers[$i]->getFocus();
						} else {
							echo "-";
						}
					}
					echo "</div>";
				//	echo "</td></tr></table>";
					
				}
				echo "</fieldset>";
			}
			echo "</td></tr></table>";
			// Lien vers le détail d'un sous-cercle ou d'un rôle
			if ($role instanceof Circle) {
				echo "<a class='ui-icon-circle-plus' href='circle.php?id=".$role->getId()."'>Afficher le détail du cercle</a>";
			}
			if ($role instanceof Role) {
				echo "<a class='ui-icon-circle-plus' href='role.php?id=".$role->getId()."'>Afficher le détail du rôle</a>";
			}
			echo "</div></div>";
		}
		if ($column>1) {echo "</td></tr></table>";}

	}

	
	public function display() {
	
		// Contrôle si le cercle est encore actif (layout à améliorer
		if (!$this->_circle->isActive ()) {
			Echo "Désolé, ce cercle a été supprimé. Retournez au niveau supérieur:";
			for($circle=$this->_circle; !$circle->getSuperCircle()->isActive(); $circle=$circle->getSuperCircle()) {}
			echo "<a href='circle.php?id=".$circle->getSuperCircle()->getId()."'>".$circle->getSuperCircle()->getName()."</a>";
			exit;
		}	
	
		// Fonctions pour les miniatures d'images
		include_once($_SERVER['DOCUMENT_ROOT'] . "/plugins/libMiniature.php");
?>

<!-- Anciennement pour adapter les styles -->
<!-- <script src="style/templates/<?=$_SESSION["template"]?>/circle.js"></script> -->

<script src="plugins/jquery.mCustomScrollbar.min.js"></script>
<script src="/scripts/circle.js"></script>
<script src="/scripts/shortcuts.js"></script>


<script> //Fonctions pour affecter et desaffecter un user à un cercle
//Lancement de la fenêtre ajout membre cercle

 $("img.imgaddusercircle").on("click",function(event){
 event.preventDefault();
var id_circle = $(this).attr("id"); //On recupère l'ID circle
//alert("ON VA AJOUTER UN UTILISATEUR DANS LE CERCLE : "+id_circle);
//Ouvre la fenetre avec la data ID Role
	openDialog ("/formulaires/form_addmember.php?circle="+id_circle, "Ajouter un nouveau membre")
	return false;
});

 
	  
	  
	  
</script>

<link href="style/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css" />

<?php
		echo "<div id='main_waiting_screen'>".\widget\Widget::FULL_WAITING_SCREEN."</div>";
		echo "<div id='circleBrowser' class='main'>";
		$this->_displayNav($this->_circle);
		echo "<table class='omo-main-table'>";
		echo "<tr><td class='omo-left-col'>";
?>

  
 <!-- Popup pour les raccourcis de capture -->   
  <div id="dialog-item" title="Capturer un item">
  <p>Formulaire ici pour capturer un item</p>
</div>
<div id="dialog-action" title="Capturer une action">
  <p>Formulaire ici pour capturer une action</p>
</div>
<div id="dialog-project" title="Capturer un projet">
  <p>Formulaire ici pour capturer un projet</p>
</div>
<div id="dialog-tension" title="Capturer une tension">
  <p>Formulaire ici pour capturer une tension</p>
</div>

<!-- Onglets de gauche -->
<div id="tabs_gauche">
  <ul>
    <li><a name="tabsG-1" href="#tabsG-1">Description</a></li>
    <li><a name="tabsG-2" href="/class/widget/wg_circlebrowser/refresh.php?refresh=member&circle=<?=$this->_circle->getId() ?>"><span class='omo-user'><span class='omo-onglet-label'>Membres</span></span></a></li>
	<?php 
	if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
		//echo "<img src='images/adduser-circle.png' class='imgaddusercircle' id='".$this->_circle->getId()."' title='Ajouter un membre dans le cercle' style='cursor:pointer;margin-top:3px;margin-right:5px;float:right;'/>";
			echo "<a href='formulaires/form_addmember.php?circle=".$this->_circle->getId()."' class='dialogPage' alt='Ajouter un membre dans le cercle'><img src='images/adduser-circle.png' class='addmembercircle' title='Ajouter un membre dans le cercle' style='float:right;margin-top:6px;margin-right:5px;'/></a>";

	} ?>
  </ul>

		
  
<!-- Onglet 1, avec les différentes infos sur le cercle -->  
  <div id="tabsG-1">
		

			<?php 
				if ($this->_circle->getPurpose()!="") { 
			?>
				<fieldset><legend><div id="mask1"></div><span class='omo-purpose'>Raison d'être</span><a class='omo-help' target='help' href='help.php?key=raison_etre'>&nbsp;</a><div id="mask2"></div></legend>
			<div class="content" id="content_4"><?php echo $this->_circle->getPurpose();?></div></fieldset>
			<?php 
				}
				if ($this->_circle->getStrategy()!="") { //Si on a une stratégie
			?>
			<fieldset><legend><div id="mask1"></div><span class='omo-strategy'>Stratégie</span><a class='omo-help' target='help' href='help.php?key=strategie'>&nbsp;</a><div id="mask2"></div><?php if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
			echo "<a class='omo-edit dialogPage' href='formulaires/form_strategy.php?id=".$this->_circle->getId()."' alt=\"Editer la stratégie\">&nbsp;</a>";
			}?></legend>
			<div class="content" id="content_1"><?php echo utf8_decode($this->_circle->getStrategy());?></div>
			</fieldset>
			<?php 
				}
				else { //Si on a pas de stratégie 
					if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) || $_SESSION["currentUser"]->isAdmin($this->_circle))) { ?>
					<fieldset><legend><div id="mask1"></div><span class='omo-strategy'>Stratégie</span><a class='omo-help' target='help' href='help.php?key=strategie'>&nbsp;</a><div id="mask2"></div><?php echo "<a class='omo-edit dialogPage' href='formulaires/form_strategy.php?id=".$this->_circle->getId()."' alt=\"Ajouter une stratégie\">&nbsp;</a>";?>
					</legend>
					</fieldset>
					<?php } 
				}
				if (count($this->_circle->getScopes())>0) {  
			?>			<fieldset><legend><div id="mask1"></div><span class='omo-domain'>Domaine</span><a class='omo-help' target='help' href='help.php?key=domaine'>&nbsp;</a><div id="mask2"></div></legend>
			<div class="content" id="content_2"><ul>
			<?php
				foreach ($this->_circle->getScopes() as $scope) {
					echo "<li>".$scope->getDescription()."</li>";
				}
			?>
			</ul></div></fieldset>
			<?php 
				}
				if (count($this->_circle->getAccountabilities())>0) { 
			?>
			<fieldset><legend><div id="mask1"></div><span class='omo-responsability'>Redevabilités</span><a class='omo-help' target='help' href='help.php?key=redevabilite'>&nbsp;</a><div id="mask2"></div></legend>
			<div class="content"  id="content_3"><ul>
			<?php
				foreach ($this->_circle->getAccountabilities() as $accountability) {
					echo "<li>".$accountability->getDescription()."</li>";
				}
			?>
			</ul></div></fieldset>
			<?php } ?>
			</div>


	</div>
		
<?php		
		echo "</td><td>";
?>	
<!-- Système à onglets, avec les 6 titres -->	
<div id="tabs">
  <ul>
    <li><a name="tabs-1" href="#tabs-1"><span class='omo-role'><span class='omo-onglet-label'>Rôles</span></span></a></li>
    <li><a name="tabs-2" href="#tabs-2"><span class='omo-onglet-label'>Politiques</span></a></li>
    <li><a name="tabs-3" href="#tabs-3"><span class='omo-onglet-label'>Historique</span></a></li>
    <li><a name="tabs-4" href="#tabs-4"><span class='omo-checklist'><span class='omo-onglet-label'>Check-lists</span></span></a></li>
    <li><a name="tabs-5" href="#tabs-5"><span class='omo-metrics'><span class='omo-onglet-label'>Indicateurs</span></span></a></li>
    <li><a name="tabs-6" href="#tabs-6"><span class='omo-onglet-label'>Projets</span></a></li>
	<?php 
	// Affiche les boutons d'action pour le premier lien et le secrétaire
	echo "<div class='omo_menu_onglet_right'>";
	// Pour tout utilisateur connecté qui a un rôle
	if (isset($_SESSION["currentUser"]) && (count($_SESSION["currentUser"]->getRoles($this->_circle))>0 || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
		echo "<a href='formulaires/form_checklist.php?circle=".$this->_circle->getId()."' class='dialogPage' alt='Ajouter une checkliste' id='menu_button_3' style='display:none'><img src='style/templates/images/checklist_add.png' title='Ajouter une checkliste' /></a>";
	}
	// Uniquement pour le 1er lien et le secrétaire
	if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE | \holacracy\Role::SECRETARY_ROLE,$this->_circle) || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
		echo "<a href='formulaires/form_metrics.php?circle=".$this->_circle->getId()."' class='dialogPage' alt='Ajouter un metrics' id='menu_button_4' style='display:none'><img src='style/templates/images/metrics_add.png' title='Ajouter un metrics'/></a>";
		echo "<a href='formulaires/form_gouvernance.php?circle=".$this->_circle->getId()."' class='dialogPage' alt='Action de gouvernance'><img src='images/reunion-gouvernance.png' title='Editer la gouvernance' /></a>";
	}
	echo "</div>";
	?>
  </ul>
  
<!-- Onglet 1, avec les différents rôles -->  
  <div id="tabs-1">
  <fieldset><legend><div id="mask1"></div><span class='omo-structural'>Rôles structurels</span><div id="mask2"></div></legend>
  <div id="accordion3">
 
<?php
	// Affichage des rôles structurels
	$this->_listeRole($this->_circle->getRoles(\holacracy\Role::STRUCTURAL_ROLES),2);
?>
  </div>
  </fieldset>
    <fieldset><legend><div id="mask1"></div>Sous-Cercles<div id="mask2"></div></legend>
  <div id="accordion">
 
<?php
	// Affichage des autres rôles (non-structurels)
	$this->_listeRole ($this->_circle->getRoles(\holacracy\Role::CIRCLE));
?>
  </div>
  </fieldset>
     <fieldset><legend><div id="mask1"></div>Rôles<div id="mask2"></div></legend>
  <div id="accordion">
 
<?php
	// Affichage des autres rôles (non-structurels)
	$this->_listeRole ($this->_circle->getRoles(~\holacracy\Role::STANDARD_ROLE));
?>
  </div>
  </fieldset>
  

</div>

<!-- Autres onglets  -->  
  <div id="tabs-2">
     <? include "wg_circlebrowser/onglet_politique.php" ?>
  </div>
  <div id="tabs-3">
    <? include "wg_circlebrowser/onglet_historique.php" ?>
  </div>
  <div id="tabs-4">
     <? 
     	$checklist=$this->_circle->getChecklist();
	 	include "wg_circlebrowser/onglet_checklist.php"; 
	?>
  </div>
  <div id="tabs-5">
    <? 
		$metrics=$this->_circle->getMetric();
		include "wg_circlebrowser/onglet_metrics.php"; 
	?>
  </div>
  <div id="tabs-6">
    <? include "wg_circlebrowser/onglet_projets2.php" ?>
  </div>
</div>
<?php
		echo "</td></tr></table>";
		echo "</div>";
	}
}

?>