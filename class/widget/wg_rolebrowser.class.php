<?php
	namespace widget;
// Cette classe affiche un browser HTML permettant de parcourir un objet de type "cercle" dans son
// intégralité : liste de rôles, sous-cercles, projets, liste de membres, etc...
class wg_roleBrowser extends Widget
{
	// l'élément cercle à afficher
	private $_role;

	// Constructeur nécessitant le cercle à afficher
	// Entrée : le cercle à afficher
	// Sortie : un objet de type wg_roleBrowser
	public function __construct(\holacracy\Role $role)
	{
		$this->_role=$role;
	}
	
	public function display() {
		// Contrôle que l'on ai le droit de visualiser cette page
		if (isset($_SESSION["currentUser"])) {
			$isMember=$this->_role->getOrganisation()->isMember($_SESSION["currentUser"]);
			$isAdmin=$this->_role->getOrganisation()->isAdmin($_SESSION["currentUser"]);
			$visibility=$this->_role->getOrganisation()->getVisibility();
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
		<title><? echo $this->_role->getOrganisation()->getName()." | ".$this->_role->getName() ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />

	
		<!-- styles needed by jScrollPane -->
	    <link rel="stylesheet" href="/plugins/timepicker/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
		<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu" />
		<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/circle.css" />

		<script src="/plugins/jquery-2.1.0.min.js"></script>
		 <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
		
		
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
		<script src="/plugins/jquery-scrolltofixed-min.js"></script>
		<script src="/scripts/role.js"></script>
		
		<script>
			function refreshUser() {
		
			}
	
			function refreshDoc(role) {
				// réaffiche tous les documents
				$("#tabs-1").load("/class/widget/wg_rolebrowser/refresh.php?refresh=document&role="+role);
			
			}
			
			function refreshMembers(role) {
				//alert ("RefreshMembers");

				$("#ui-tabs-1").load("/class/widget/wg_rolebrowser/refresh.php?refresh=member&role="+role);
			}

			function refreshChecklist(role) {
				$("#tabs-4").load("/class/widget/wg_rolebrowser/refresh.php?refresh=checklist&role=<?=$this->_role->getId()?>");
			}
			
			function refreshMetrics(role=null) {
				$("#tabs-5").load("/class/widget/wg_rolebrowser/refresh.php?refresh=metrics&role=<?=$this->_role->getId()?>");
			}

			function refreshRole(role) {
				$("#tab_role_"+role+" h3 span.reload").load("/class/widget/wg_rolebrowser/refresh.php?refresh=role&role="+role);
				$("#tab_role_"+role+" div").load("/class/widget/wg_rolebrowser/refresh.php?refresh=roleContent&role="+role);
			}
		</script>
		
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
 		if (!$this->_role->isActive ()) echo "<div id='object_deleted_screen'>".\widget\Widget::OBJECT_DELETED_SCREEN."</div>";
		
		?>
		<?$this->_displayNav($this->_role);?>
		<div class='omo-maindiv'>
			<div class='omo-leftcol'>
			<div id="tabs_gauche">
  <ul>
    <li><a name="tabsG-1" href="#tabsG-1">Description du rôle</a></li>

  </ul>

		
  
<!-- Onglet 1, avec les différentes infos sur le cercle -->  
  <div id="tabsG-1">
			<?php 
				if ($this->_role->getPurpose()!="") { 
			?>
				<fieldset><legend><div id="mask1"></div><span class='omo-purpose'><? print T_("Raison d'&ecirc;tre"); ?></span><a class='omo-help' target='help' href='help.php?key=raison_etre'>&nbsp;</a><div id="mask2"></div></legend>
			<div class="content" id="content_4"><?php echo str_replace("\n","<br>",str_replace("<br/>","",str_replace("<br>","",$this->_role->getPurpose())));?></div></fieldset>
			<?php 
				}

				if (count($this->_role->getScopes())>0) {  
			?>			<fieldset><legend><div id="mask1"></div><span class='omo-domain'><? print T_("Domaines"); ?></span><a class='omo-help' target='help' href='help.php?key=domaine'>&nbsp;</a><div id="mask2"></div></legend>
			<div class="content" id="content_2"><ul>
			<?php
				foreach ($this->_role->getScopes() as $scope) {
					echo "<li>".$scope->getDescription()."</li>";
				}
			?>
			</ul></div></fieldset>
			<?php 
				}
				if (count($this->_role->getAccountabilities())>0) { 
			?>
			<fieldset><legend><div id="mask1"></div><span class='omo-responsability'><? print T_("Redevabilit&eacute;s"); ?></span><a class='omo-help' target='help' href='help.php?key=redevabilite'>&nbsp;</a><div id="mask2"></div></legend>
			<div class="content"  id="content_3"><ul>
			<?php
				foreach ($this->_role->getAccountabilities() as $accountability) {
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
    <li><a name="tabs-1" href="#tabs-1"><span class='omo-documents'><span class='omo-tab-label'><? print T_("Documents"); ?></span></span></a></li>
    <li><a name="tabs-2" href="#tabs-2"><span class='omo-politic'><span class='omo-tab-label'><? print T_("Politiques"); ?></span></span></a></li>
    <li><a name="tabs-3" href="#tabs-3"><span class='omo-history'><span class='omo-tab-label'><? print T_("Historique"); ?></span></span></a></li>
    <li><a name="tabs-4" href="#tabs-4"><span class='omo-checklist'><span class='omo-tab-label'><? print T_("Check-lists"); ?></span></span></a></li>
    <li><a name="tabs-5" href="#tabs-5"><span class='omo-metrics'><span class='omo-tab-label'><? print T_("Indicateurs"); ?></span></span></a></li>
    <li><a name="tabs-6" href="#tabs-6"><span class='omo-projects'><span class='omo-tab-label'><? print T_("Projets"); ?></span></span></a></li>
	<?php 
	// Affiche les boutons d'action pour le premier lien et le secrétaire
	echo "<div class='omo_menu_onglet_right'>";

	// Est rôle
	if ($_SESSION["currentUser"]->isRole($this->_role)) {
		echo "<a href='formulaires/form_checklist.php?role=".$this->_role->getId()."' class='dialogPage' alt='".T_("Ajouter une check-list")."' id='menu_button_4' style='display:none'><img src='style/templates/images/checklist_add.png' title='".T_("Ajouter une check-list")."' /></a>";
		echo "<a href='formulaires/form_metrics.php?role=".$this->_role->getId()."' class='dialogPage' alt='".T_("Ajouter un indicateur")."' id='menu_button_5' style='display:none'><img src='style/templates/images/metrics_add.png' title='".T_("Ajouter un indicateur")."'/></a>";
	}
	
	echo "</div>";
	?>
  </ul>
  
<!-- Onglet 1, avec les documents partagés -->  
<div id="tabs-1">
	<?		
		$documents=$this->_role->getDocuments();
		include "wg_rolebrowser/onglet_document.php" 
	?>
</div>

 <div id="tabs-2">
 	<? include "wg_rolebrowser/onglet_politique.php" ?>
</div>

<!-- Autres onglets -->

  <div id="tabs-3">
    <? include "wg_rolebrowser/onglet_historique.php" ?>
  </div>
  <div id="tabs-4">
     <? 
     	$checklist=$this->_role->getChecklist();
	 	include "wg_rolebrowser/onglet_checklist.php"; 
	?>
  </div>
  <div id="tabs-5">
    <? 
		$metrics=$this->_role->getAllMetrics();
		include "wg_rolebrowser/onglet_metrics.php"; 
	?>
  </div>
  <div id="tabs-6">
    <? include "wg_rolebrowser/onglet_projets.php" ?>
  </div>
			  </div>
				
			</div>
			
		</div>
		<div class='omo-navv'><div id='omo-btnhaut'></div><div id='omo-btnbas'></div></div>
	</body>
</html>
<script>
		window.setInterval(function() {checkUpdate(<?=$this->_role->getId()?>,10)}, 10000);
</script>
<?

	}
}

?>
