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
		echo "<div class='omo-cols'>";
		if ($column>1) {echo "<div class='col1'>";}
		for ($j=0; $j<count($liste);$j++) {
			$role=$liste[$j];
			if ($column>1 && $j==2) echo "</div><div class='col2'>";
			
			// Affiche le nom du rôle (le H3 est pour l'affichage sous forme d'accordéon jquery-ui)
			//echo "<div class='grey_design'>";
			$classme = "";
			if (isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isRole($role)) {
									$classme = "-me";
							}
			echo "<div class='grey_design accordion_role' id='tab_role_".$role->getId()."'><h3><span class='reload'><span class='omo-role-".$role->getType()."".$classme."'>";
			echo "<b>";
			
			if (get_class($role) == "holacracy\\Circle") {
				echo "<a class='ui-icon-circle-plus' href='circle.php?id=".$role->getId()."' title='".T_("Afficher le d&eacute;tail du cercle")."'>".$role->getName()."</a>";
			}
			if (get_class($role) == "holacracy\\Role") {
				echo "<a class='ui-icon-circle-plus' href='role.php?id=".$role->getId()."#tabs-6' title='".T_("Afficher le d&eacute;tail du role")."'>".$role->getName()."</a>";
			}	
					
			echo "</b></span>";
			echo "<span id='role_".$role->getId()."' class='omo-accordion-info'>";
			// Charge la liste des gens en charge du rôle
			$roleFillers=$role->getRoleFillers();

			if ($role->getUserId()>0) { //count($roleFillers)>0
				// Si c'est un cercle, affiche le RoleFiller comme premier lien
				if ($role instanceof \holacracy\Circle) {
					print T_(" 1er lien : ");
					echo $role->getUser()->getUserName();
					
					// Et affiche le second lien
					$repLink=$role->getRoles(\holacracy\Role::REP_LINK_ROLE);
					if (count($repLink)>0) {
						print T_(" - 2nd lien : ");
						//$roleFillers2=$repLink[0]->getRoleFillers();
						if ($repLink[0]->getUserId()>0) {
							echo $repLink[0]->getUser()->getUserName();
							//desaffecte un 2nd lien
						} else {
							print T_("Non affect&eacute;");
						}
						
					}
				} else {
					// Sinon, affiche par qui le rôle est énergétisé (plusieurs personnes possible)
					print T_(" Energ&eacute;tis&eacute; par : ");

					echo "<span title='".$role->getUser()->getFirstName()." ".$role->getUser()->getLastName()."'>".$role->getUser()->getUserName()." </span>";

					//for ($i=0;($i<count($roleFillers) && count($roleFillers)<=3) || ($i<2 && count($roleFillers)>3); $i++){
					//	if ($i>0) {echo " , ";}
					//	echo "<span title='".$roleFillers[$i]->getFirstName()." ".$roleFillers[$i]->getLastName()."'>".$roleFillers[$i]->getUserName()." </span>";
					//	$type = $role->getType();
					//	}
					//}
					$type = $role->getType();
					if ($type & \holacracy\Role::STANDARD_ROLE ) {
						if (count($roleFillers)>0) echo T_(" et <u>").(count($roleFillers)).T_(" autre(s) personne(s)</u>");
					}
				}
			}
			else{  //Si on a pas de role affecté on affiche non affecté et le bouton pour affecter un membre du cercle
				// echo " (".$role->getSuperCircle()->getLeadLink()->getUser()->getUserName().")";
				if ($role->getSourceId()>0 ) {
					if ($role->getSource()->getUserId()>0) {
						print T_(" Energ&eacute;tis&eacute; par : ");
						echo "<span title='".$role->getSource()->getUser()->getFirstName()." ".$role->getSource()->getUser()->getLastName()."'>".$role->getSource()->getUser()->getUserName()." </span>";
					} else {
						print T_("Non affect&eacute;");
					}
				} else 
				if ($role->getSourceCircleId()>0) {
					if ($role->getSourceCircle()->getUserId()>0) {
						print T_(" Energ&eacute;tis&eacute; par : ");
						echo "<span title='".$role->getSourceCircle()->getUser()->getFirstName()." ".$role->getSourceCircle()->getUser()->getLastName()."'>".$role->getSourceCircle()->getUser()->getUserName()." </span>";
					} else {
						print T_("Non affect&eacute;");
					}
				} else {
				$type = $role->getType();
				if ($type & \holacracy\Role::STRUCTURAL_ROLES) {
					print T_("Non affect&eacute;");
				} else {
					print T_("Affect&eacute; par d&eacute;faut au Premier Lien");
					
					// Affiche les focus s'ils existent
					$type = $role->getType();
					if ($type & \holacracy\Role::STANDARD_ROLE ) {
						if (count($roleFillers)>0) echo T_(" (et <u>").(count($roleFillers)).T_(" autre(s) personne(s)</u>)");
					}
					
				}
			}
			}
			$type = $role->getType();
			if ( isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) && $type != 2 || $_SESSION["currentUser"]->isAdmin()) && !($role->getSourceCircleId()!="")) { //Si le role n'est pas un 1er Lien
				echo "<img src='images/edit-user.png' href='formulaires/form_affect_user.php?circle=".$this->_circle->getId()."&role=".$role->getId()."' class='dialogPage' alt='".T_("Assigner le r&ocirc;le ").$role->getName()."'/>";
			}
			echo "</span>";
			// Affichage du résumé du rôle/cercle
			echo "</span></h3><div><table style='width:100%'><tr><td style='width:66%'>";
			// S'il y a une raison d'être, l'affiche
			if ($role->getPurpose()!="") {
				echo "<fieldset class='light'><legend><div id='mask1'></div><span class='omo-purpose'>".T_("Raison d'&ecirc;tre")."</span><div id='mask2'></div></legend>";
				echo str_replace("\n","<br>",str_replace("<br/>","",str_replace("<br>","",$role->getPurpose())))."</fieldset>";
			}
			
			// S'il y a une raison d'être, l'affiche
			if ($role->getType()==\holacracy\Role::CIRCLE && $role->getStrategy()!="") {
				echo "<div class='omo-light-accordion light'><h3><span class='omo-strategy omo-label'>".T_("Strat&eacute;gie")."</span></h3><div>".$role->getStrategy()."</div></div>";
			}

			// S'il y a un domaine, l'affiche
			if (count($role->getScopes())>0) {
				echo "<div class='omo-light-accordion light'><h3><span class='omo-scope omo-label'>".T_("Domaines")."</span></h3><div>";
				$cmpt = 1;
				foreach ($role->getScopes() as $scope) {
					if($cmpt != 1) { echo " , ";}
					$politiquescope = $scope->getPolitiques();
					echo "<span ".($scope->getRoleId()!=$role->getId()?" style='font-style: italic;'":"").">".$scope->getDescription()."</span>";
					if($politiquescope != ""){ echo " <img src='style/templates/images/politics.png' href='formulaires/form_politic-scop.php?domaine=".$scope->getId()."' class='dialogPage' alt='".T_("Politiques du domaine ").str_replace("'","&apos;",$scope->getDescription())."' title='Voir les politiques du domaine ".$scope->getDescription()."' style='width:14px;height:13px;cursor:pointer;'> "; }
					else{ //Si aucune politique
						if($_SESSION["currentUser"]->isRole($role)){ //Si l'utilisateur a le role
						echo " <img src='style/templates/images/add-politics.png' href='formulaires/form_politic-scop.php?domaine=".$scope->getId()."' class='dialogPage' alt='".T_("Politiques du domaine ").str_replace("'","&apos;",$scope->getDescription())."' title='Créer des politiques pour le domaine ".$scope->getDescription()."' style='width:14px;height:13px;cursor:pointer;'> ";
						}
					}
					$cmpt++;
				}			
			}
			echo "</div></div>";
			
			// S'il y a un domaine, l'affiche
			if (count($role->getAccountabilities())>0) {
				echo "<div class='omo-light-accordion light'><h3><span class='omo-accountabilities omo-label'>".T_("Redevabilit&eacute;s")."</span></h3><div>";
				echo "<ul>";
				foreach ($role->getAccountabilities() as $accountability) {
					echo "<li".($accountability->getRoleId()!=$role->getId()?" style='font-style: italic;'":"").">".$accountability->getDescription()."</li>";
				}			
				echo "</ul>";
			}
			echo "</div></div></td>";
			
			if ($column>1) {echo "</tr><tr>";}
			echo "<td>";
			
			// Affiche le détail des rôles fillers avec l'intégralité du nom, ainsi que les focus
			if ($role->getUserId()>0 || ($role->getSourceId()>0 && $role->getSource()->getUserId()>0) || ($role->getSourceCircleId()>0 && $role->getSourceCircle()->getUserId()>0) || count($roleFillers)>0) {
				echo "<fieldset class='light'><legend><div id='mask1'></div><span class='omo-user'>".T_("Energ&eacute;tis&eacute; par")."</span><div id='mask2'></div></legend>";
				if ($role->getUserId()>0 || ($role->getSourceId()>0 && $role->getSource()->getUserId()>0)|| ($role->getSourceCircleId()>0 && $role->getSourceCircle()->getUserId()>0)) {
					if ($role->getUserId()>0) 
						$roleD=$role;
					else if ($role->getSourceId()>0) 
						$roleD=$role->getSource();
					else 
						$roleD=$role->getSourceCircle();
					echo "<div class='omo-user-block ui-corner-all'>";
					if (checkMini("/images/user/".$roleD->getUserId().".jpg",30,30,"mini",1,5)) {
						echo "<a class='omo-user-img dialogPage' href='/user.php?id=".$roleD->getUserId()."&circle=".$roleD->getSuperCircle()->getId()."' class='dialogPage' alt='".T_("Profil de ").$roleD->getUser()->getFirstName()." ".$roleD->getUser()->getLastName()."'><img src='/images/user/mini/".$roleD->getUserId().".jpg'/></a>";
					}
					// Affiche quelques infos et le menu USER			
					echo "<b>Personne en charge:<br/>".$roleD->getUser()->getFirstName()." ".$roleD->getUser()->getLastName()."</b><br>";
					echo "</div>";
				} else {
					// Afficher l'info sur le 1er lien?
					echo "<div class='omo-user-block ui-corner-all'>";
					if (checkMini("/images/user/".$role->getSuperCircle()->getUserId().".jpg",30,30,"mini",1,5)) {
						echo "<a class='omo-user-img dialogPage' href='/user.php?id=".$role->getSuperCircle()->getUserId()."&circle=".$roleD->getSuperCircle()->getId()."' class='dialogPage' alt='".T_("Profil de ").$role->getSuperCircle()->getUser()->getFirstName()." ".$role->getSuperCircle()->getUser()->getLastName()."'><img src='/images/user/mini/".$role->getSuperCircle()->getUserId().".jpg'/></a>";
					}
					// Affiche quelques infos et le menu USER			
					echo "<b>Personne en charge:<br/>1er lien</b><br>";
					echo "</div>";

				}
				for ($i=0;$i<count($roleFillers); $i++){

					echo "<div class='omo-user-block ui-corner-all'>";
					if (checkMini("/images/user/".$roleFillers[$i]->getUserId().".jpg",30,30,"mini",1,5)) {
						echo "<a class='omo-user-img dialogPage' href='/user.php?id=".$roleFillers[$i]->getUserId()."&circle=".$role->getSuperCircle()->getId()."' class='dialogPage' alt='".T_("Profil de ").$roleFillers[$i]->getFirstName()." ".$roleFillers[$i]->getLastName()."'><img src='/images/user/mini/".$roleFillers[$i]->getUserId().".jpg'/></a>";
					}
					// Affiche quelques infos et le menu USER			
					echo "<b>".$roleFillers[$i]->getFirstName()." ".$roleFillers[$i]->getLastName()."</b><br>";
					
					echo T_(" pour ");
					if ($roleFillers[$i]->getFocus()!="") {
						echo $roleFillers[$i]->getFocus();
					} else {
						echo "-";
					}

					echo "</div>";

					
				}
				echo "</fieldset>";
			} else {
				echo "<!--GetUserId : ".$role->getUserId(). " | GetSourceId : ".$role->getSourceId(). " | GetSourceCircleId : ".$role->getSourceCircleId()."-->";
			}
			echo "</td></tr></table>";
			// Lien vers le détail d'un sous-cercle ou d'un rôle
			if ($role instanceof Circle) {
				echo "<a class='ui-icon-circle-plus' href='circle.php?id=".$role->getId()."'>".T_("Afficher le d&eacute;tail du cercle")."</a>";
			}
			if ($role instanceof Role) {
				echo "<a class='ui-icon-circle-plus' href='role.php?id=".$role->getId()."#tabs-5'>".T_("Afficher le d&eacute;tail du r&ocirc;le")."</a>";
			}
			echo "</div></div>";
		}
		if ($column>1) {echo "</div>";}
		echo "</div>";

	}

	
	public function display() {
		
		// Contrôle que l'on ai le droit de visualiser cette page
		if (isset($_SESSION["currentUser"])) {
			$isMember=$this->_circle->getOrganisation()->isMember($_SESSION["currentUser"]);
			$isAdmin=$this->_circle->getOrganisation()->isAdmin($_SESSION["currentUser"]);
			$visibility=$this->_circle->getOrganisation()->getVisibility();
			if ($isMember || $isAdmin || $visibility==2) {
				// Ok, ça marche... y a-t-il des choses à faire?
			} else {
				// La visualisation n'est pas souhaitée, redirige sur la page de login.
				
				header("location:index.php");
				exit;
			}
		} else {
			// Le visiteur sera créé après... mais la page est-elle en mode publique ou semi-publique?
			$visibility=$this->_circle->getOrganisation()->getVisibility();
			if (!$visibility==2) {
				header("location:index.php");
				exit;
			}
		}
	
?>
<html>
	<head>
		<meta name="viewport" content="width=device-width"/>
		<title><? echo $this->_circle->getOrganisation()->getName()." | ".$this->_circle->getName() ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

	
		<!-- styles needed by jScrollPane -->
	    <link rel="stylesheet" href="/plugins/timepicker/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu" />
		<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/circle.css" />


		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>



		<script src="/plugins/jquery-scrolltofixed-min.js"></script>
		<script src="/scripts/circle.js"></script>
		
		<!-- Script google pour les graphiques -->
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>

	   <script type="text/javascript" src="/plugins/timepicker/jquery.ui.timepicker.js?v=0.3.3"></script>

		<!-- Smartsupp Live Chat script -->
		<script type="text/javascript">
		var _smartsupp = _smartsupp || {};
		_smartsupp.key = '0494c3f917ace7e29405137cc471f391bbd3416a';
		window.smartsupp||(function(d) {
			var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
			s=d.getElementsByTagName('script')[0];c=d.createElement('script');
			c.type='text/javascript';c.charset='utf-8';c.async=true;
			c.src='//www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
		})(document);
		</script>
		
		<script>
			function moveUp() {
				newpos=parseInt(($(window).scrollTop()-100)/$(window).height())*$(window).height();
				$(window).scrollTop(newpos);
			}
			function moveDown() {
				newpos=parseInt($(window).scrollTop()/$(window).height()+1)*$(window).height();
				$(window).scrollTop(newpos);
			}
			$(document).ready(function(){
				$("#omo-btnhaut").click(moveUp);
				$("#omo-btnbas").click(moveDown);
				$("#tabs_gauche").tabs({heightStyle: "fill"});
				$("#tabs_droite").tabs({heightStyle: "fill"});
				
				$(window).resize(function () {
						$("#tabs_gauche").tabs({heightStyle: "fill"});
						$("#tabs_droite").tabs({heightStyle: "fill"});
				
				})
								
			});
		</script>

	</head>
	<body>
		<? 
		echo "<div id='main_waiting_screen'>".\widget\Widget::FULL_WAITING_SCREEN."</div>";
		if (!$this->_circle->isActive ()) echo "<div id='object_deleted_screen'>".\widget\Widget::OBJECT_DELETED_SCREEN."</div>";
		
		?>
		<?$this->_displayNav($this->_circle);?>
		<div class='omo-maindiv'>
			<div class='omo-leftcol'>
			<div id="tabs_gauche">
  <ul>
    <li><a name="tabsG-1" href="#tabsG-1"><? print T_("Description"); ?></a></li>
    <li><a name="tabsG-2" href="/class/widget/wg_circlebrowser/refresh.php?refresh=member&circle=<?=$this->_circle->getId() ?>"><span class='omo-user'><span class='omo-tab-label'><? echo T_("Membres"); ?></span></span></a></li>
	<?php 
	if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
			echo "<a href='formulaires/form_addmember.php?circle=".$this->_circle->getId()."' class='dialogPage' alt='".T_("Ajouter un membre dans le cercle")."'><img src='images/adduser-circle.png' class='addmembercircle' title='".T_("Ajouter un membre dans le cercle")."' style='float:right;margin-top:6px;margin-right:5px;'/></a>";

	} ?>
  </ul>

		
  
<!-- Onglet 1, avec les différentes infos sur le cercle -->  
  <div id="tabsG-1">
			<?php 

			if ($this->_circle->getOrganisation()->getMission()!="") {
				echo '<fieldset><legend><div id="mask1"></div><span class=\'omo-mission\'>'.T_("Mission").'</span><a class=\'omo-help\' target=\'help\' href=\'help.php?key=mission\'>&nbsp;</a><div id="mask2"></div></legend>';
				echo '<div class="content" id="content_4">';
				echo str_replace("\n","<br>",str_replace("<br/>","",str_replace("<br>","",$this->_circle->getOrganisation()->getMission())));
				echo "</div>";	
				echo "</fieldset>";
			}
			
			
				if ($this->_circle->getPurpose()!="") { 
			?>
				<fieldset><legend><div id="mask1"></div><span class='omo-purpose'><? print T_("Raison d'&ecirc;tre"); ?></span><a class='omo-help' target='help' href='help.php?key=raison_etre'>&nbsp;</a><div id="mask2"></div></legend>
			<div class="content" id="content_4"><?php echo str_replace("\n","<br>",str_replace("<br/>","",str_replace("<br>","",$this->_circle->getPurpose())));?></div></fieldset>
			<?php 
				}
				if ($this->_circle->getStrategy()!="") { //Si on a une stratégie
			?>
			<fieldset><legend><div id="mask1"></div><span class='omo-strategy'><? print T_("Strat&eacute;gie"); ?></span><a class='omo-help' target='help' href='help.php?key=strategie'>&nbsp;</a><div id="mask2"></div><?php if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
			echo "<a class='omo-edit dialogPage' href='formulaires/form_strategy.php?id=".$this->_circle->getId()."' alt='".T_("Editer la strat&eacute;gie")."'>&nbsp;</a>";
			}?></legend>
			<div class="content" id="content_1"><?php echo utf8_decode($this->_circle->getStrategy());?></div>
			</fieldset>
			<?php 
				}
				else { //Si on a pas de stratégie 
					if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) || $_SESSION["currentUser"]->isAdmin($this->_circle))) { ?>
					<fieldset><legend><div id="mask1"></div><span class='omo-strategy'><? print T_("Strat&eacute;gie"); ?></span><a class='omo-help' target='help' href='help.php?key=strategie'>&nbsp;</a><div id="mask2"></div><?php echo "<a class='omo-edit dialogPage' href='formulaires/form_strategy.php?id=".$this->_circle->getId()."' alt='".T_("Ajouter une strat&eacute;gie")."'>&nbsp;</a>";?>
					</legend>
<?					
		echo "<div class='omo-warning-title'>Il n'y a aucune stratégie de défini. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
		echo "<div class='omo-more-warning'>";
		echo "La stratégie permet à chacun de définir les priorités dans ses projets et ses actions, sans avoir besoin d'en référer à un chef, un manager ou un coordinateur. Définir une stratégie est de la responsabilité du 1er lien qui peut le faire en cliquant sur le petit icon crayon ci-dessus. Découvrez ci-dessous pourquoi et comment créer une stratégie.";
		echo "<div class='videolist'><div class='video'>";
		echo "<h1>La stratégie</h1>";
		echo "<hr>";
		echo '<iframe width="280" height="157" src="https://www.youtube.com/embed/YqMEZZEz1-Y?rel=0" frameborder="0" allowfullscreen></iframe>';
		echo "</div></div>";
		echo "</div>";					
?>				
					
					</fieldset>
					<?php } 
				}
				if (count($this->_circle->getScopes())>0) {  
			?>			<fieldset><legend><div id="mask1"></div><span class='omo-domain'><? print T_("Domaines"); ?></span><a class='omo-help' target='help' href='help.php?key=domaine'>&nbsp;</a><div id="mask2"></div></legend>
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
			<fieldset><legend><div id="mask1"></div><span class='omo-responsability'><? print T_("Redevabilit&eacute;s"); ?></span><a class='omo-help' target='help' href='help.php?key=redevabilite'>&nbsp;</a><div id="mask2"></div></legend>
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

			</div>
			<div class='omo-rightcol'>			
			<div id="tabs_droite">
 <ul>
    <li><a name="tabs-1" href="#tabs-1"><span class='omo-role'><span class='omo-tab-label'><? echo ("Résumé"); ?></span></span></a></li>
    <li><a name="tabs-2" href="#tabs-2"><span class='omo-role'><span class='omo-tab-label'><? print T_("R&ocirc;les"); ?></span></span></a></li>
    <li><a name="tabs-3" href="#tabs-3"><span class='omo-politic'><span class='omo-tab-label'><? print T_("Politiques"); ?></span></span></a></li>
    <li><a name="tabs-4" href="#tabs-4"><span class='omo-checklist'><span class='omo-tab-label'><? print T_("Check-lists"); ?></span></span></a></li>
    <li><a name="tabs-5" href="#tabs-5"><span class='omo-metrics'><span class='omo-tab-label'><? print T_("Indicateurs"); ?></span></span></a></li>
    <li><a name="tabs-6" href="#tabs-6"><span class='omo-projects'><span class='omo-tab-label'><? print T_("Projets"); ?></span></span></a></li>
	<?php 
	// Affiche les boutons d'action pour le premier lien et le secrétaire
	echo "<div class='omo_menu_onglet_right'>";
	// Pour tout utilisateur connecté qui a un rôle
	if (isset($_SESSION["currentUser"]) && (count($_SESSION["currentUser"]->getRoles($this->_circle))>0 || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
		echo "<a href='formulaires/form_checklist.php?circle=".$this->_circle->getId()."' class='dialogPage' alt='".T_("Ajouter une check-list")."' id='menu_button_3' style='display:none'><img src='style/templates/images/checklist_add.png' title='".T_("Ajouter une check-list")."' /></a>";
	}
	// ATTENTION !!!! A redéfinir via les paramètres, et à s'assurer ce que dis la constitution
	// Uniquement pour le 1er lien et le secrétaire
	//if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE | \holacracy\Role::SECRETARY_ROLE,$this->_circle) || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
	
	// Pour tout utilisateur connecté qui a un rôle
	if (isset($_SESSION["currentUser"]) && (count($_SESSION["currentUser"]->getRoles($this->_circle))>0 || $_SESSION["currentUser"]->isAdmin($this->_circle))) {
		echo "<a href='formulaires/form_metrics.php?circle=".$this->_circle->getId()."' class='dialogPage' alt='".T_("Ajouter un indicateur")."' id='menu_button_4' style='display:none'><img src='style/templates/images/metrics_add.png' title='".T_("Ajouter un indicateur")."'/></a>";
	}
		// Charge les prochaines réunions du cercle
		$meetings=$this->_circle->getMeetings();
		$count=0;
		$txt="";
		$date=new \DateTime("now");
		foreach($meetings as $meeting) {
			$count+=1;
			if ($count==1) {
				if ($meeting->getDate()->format("d.m.Y")==$date->format("d.m.Y")) {
					$txt="\nProchaine aujourd&apos;hui à ".$meeting->getStartTime();
				} else {
					$txt="\nProchaine le ".$meeting->getDate()->format("d.m.Y");
				}
				$nextMeeting=$meeting;
			}
		}
		echo "<a href='formulaires/form_meeting.php?circle=".$this->_circle->getId()."' title='".$count.T_(" réunion(s) annoncée(s)").$txt."' class='dialogPage omo-reunion";
		if (isset($nextMeeting) && $nextMeeting->getDate()->format("d.m.Y")==$date->format("d.m.Y")) {
			echo "2";
		} else {
			echo "";
		}
		echo "' alt='Agenda des réunions' ></a>";

	echo "</div>";
	?>
  </ul>
  
<!-- Onglet 1, avec les différents rôles -->  
  <div id="tabs-1">
     <? include "wg_circlebrowser/onglet_resume.php" ?>
  </div>
  <div id="tabs-2">
<?
	echo "<div class='omo-help-title'><b>Rôles:</b> Liste de tous les rôles structurels, rôles opérationnels et sous-cercles du cercle. <a href='#' class='omo_act_more_help'>Afficher plus...</a></div>";
	echo "<div class='omo-more-help'>Description de tous les rôles et sous-cercles de ce cercle, sous la forme de leur raison d'Être, leurs domaines et leurs redevabilités. Pour chacun de ces rôles, la liste des membres qui énergétisent ces rôles est accessible. Pour en savoir plus sur les rôles, se référer à <a href='https://dev.openmyorganization.com/constitution.php?q=role#idx_0' target='_constitution'>l'article 1</a> et à <a href='https://dev.openmyorganization.com/constitution.php?q=cercle#idx_1' target='_constitution'>l'article 2</a> pour les cercles.</div>";
?>
  <fieldset><legend><div id="mask1"></div><span class='omo-structural'><? print T_("R&ocirc;les structurels"); ?></span><div id="mask2"></div></legend>
  <div id="accordion3">
 
<?php
	// Affichage des rôles structurels
	$this->_listeRole($this->_circle->getRoles(\holacracy\Role::STRUCTURAL_ROLES,\holacracy\Circle::TYPE_ORDER),2);
?>
  </div>
  </fieldset>
<?
	$liste=$this->_circle->getRoles(\holacracy\Role::CIRCLE);
	if (count($liste)>0) {
?>
    <fieldset><legend><div id="mask1"></div><? print T_("Sous-Cercles"); ?><div id="mask2"></div></legend>
  <div id="accordion">
 
<?php
	// Affichage des autres rôles (non-structurels)
	$this->_listeRole ($liste);
?>
  </div>
  </fieldset>
<?
	}

	// Affiche si nécessaire la liste des liens transverse non assignés (uniquement pour le premier lien)
	$liste=$this->_circle->getLinks(\holacracy\Circle::SOURCE_LINK , false);
	if (count($liste)>0) {
?>
    <fieldset><legend><div id="mask1"></div><? print T_("Liens transverses"); ?><div id="mask2"></div></legend>
  <div id="accordion">
 
<?php
	// Affichage des autres rôles (non-structurels)
	echo "Il y a ".count($liste). " ".(count($liste)>1?"liens transverses qui ne sont pas assignés à des rôles":"lien transverse qui n'est pas assigné à un rôle");
?>
  </div>
  </fieldset>
<?
	}
?>
   <fieldset><legend><div id="mask1"></div><? print T_("R&ocirc;les"); ?><div id="mask2"></div></legend>
  <div id="accordion">
 
<?php
	// Affichage des autres rôles (non-structurels)
	$liste=$this->_circle->getRoles(\holacracy\Role::STANDARD_ROLE | \holacracy\Role::LINK_ROLE);
	if (count($liste)>0) 
		$this->_listeRole ($liste);
	else {
		echo "<div class='omo-warning-title'>Il n'y a aucun rôle de défini. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
		echo "<div class='omo-more-warning'>";
		echo "Les rôles permettent de distribuer l'autorité et les tâches. Ils sont créés en réunion de gouvernance uniquement. Découvrez ci-dessous pourquoi et comment les créer.";
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
?>
  </div>
  </fieldset>
  
 <?
	// Affichage du lien vers un PDF
	echo "<a href='/circle.php?id=".$this->_circle->getId()."&display=2'>Format PDF</a>";
 ?>
</div>

<!-- Autres onglets  -->  
  <div id="tabs-3">
     <? include "wg_circlebrowser/onglet_politique.php" ?>
  </div>

  <div id="tabs-4">
     <? 
     	$checklist=$this->_circle->getChecklist();
     	$circle=$this->_circle;

	 	include "wg_circlebrowser/onglet_checklist.php"; 
	?>
  </div>
  <div id="tabs-5">
    <? 
		$metrics=$this->_circle->getAllMetrics();
		$circle=$this->_circle;
		include "wg_circlebrowser/onglet_metrics.php"; 
	?>
  </div>
  <div id="tabs-6">
    <? include "wg_circlebrowser/onglet_projets.php" ?>
  </div>
			  </div>
				
			</div>
			
		</div>
		<div class='omo-navv'><div id='omo-btnhaut'></div><div id='omo-btnbas'></div></div>
	</body>
</html>
<script>
		window.setInterval(function() {checkUpdate(<?=$this->_circle->getId()?>,10)}, 10000);
</script>
<?

	}
}

?>
