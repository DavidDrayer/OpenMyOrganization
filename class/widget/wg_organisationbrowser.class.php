<?php
	namespace widget;
	
// Cette classe affiche un browser HTML permettant de parcourir un ou plusieurs objet de type "rorganisation" dans son
// intégralité : redevabilites, perimetres, raison d'etre, etc...
class wg_OrganisationBrowser extends Widget
{
	// l'élément role à afficher
	private $_organisation;
	
	// Constructeur nécessitant le role à afficher
	// Entrée : le role à afficher
	// Sortie : un objet de type wg_circleBrowser
	public function __construct($organisation) 
	{
		$this->_organisation=$organisation;
	}
	


	
	private function _displayMeeting($object, $max=10) {
		$month=array(1 => 'Jan', 2 => 'Fev',3 => 'Mars', 4 => 'Avr',5 => 'Mai', 6 => 'Juin',7 => 'Juil', 8 => 'Aout',9 => 'Sept', 10 => 'Oct',11 => 'Nov', 12 => 'Dec');
		$day=array(0 => 'Dim', 1 => 'Lun',2 => 'Mar', 3 => 'Mer',4 => 'Jeu', 5 => 'Ven',6 => 'Sam', 7 => 'Dim');
		$meetinglist=$object->getMeetingList();
		if (count($meetinglist)==0) {
					echo "<div class='omo-warning-title'>Aucune réunion planifiée. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
				echo "<div class='omo-more-warning'>";
						echo "Ce sont les secrétaires de chaque cercles qui convoquent les réunions, que ce soit les réunions de gouvernance, opérationnelles ou de stratégie. Si vous avez besoin d'une réunion particulière, regardez avec le secrétaire. Découvrez ci-dessous à quoi servent les différentes réunions et comment les agender.";
						echo "<div class='videolist'>";
						echo "<div class='video'>";
						echo "<h1>Les réunions</h1>";
						echo "<hr>";
						echo '<iframe width="280" height="157" src="https://www.youtube.com/embed/YqMEZZEz1-Y?rel=0" frameborder="0" allowfullscreen></iframe>';
						echo "</div>";
						echo "</div>";
				echo "</div>";

		} else {
		foreach ($meetinglist as $meeting) {
			$canEdit=(isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE | \holacracy\Role::SECRETARY_ROLE,$meeting->getCircle()) || $_SESSION["currentUser"]->isAdmin($meeting->getCircle())));

			echo "<div class=' ui-corner-all";
			echo " omo-meeting-type".$meeting->getMeetingTypeId();
			if ($meeting->getClosingTime()!="") {
				echo " omo-old-meeting";
			} else if ($meeting->getOpeningTime()!="") {
				echo " omo-current-meeting";
			} 
			
			echo "'>";
			if ($canEdit && $meeting->getOpeningTime()=="") {
				echo "<span style='float:right;  position: relative;'>";
				echo "<a class='omo-delete ajax' href='ajax/deleteMeeting.php?id=".$meeting->getId()."' alt='".T_("Supprimer")."' check='".T_(htmlentities("Etes-vous sûr de vouloir supprimer cette réunion?"))."'>&nbsp;</a>";
				echo "<a class='omo-edit dialogPage' href='formulaires/form_meeting.php?action=edit&id=".$meeting->getId()."' alt='".T_(htmlentities("Editer les infos de la réunion"))."'>&nbsp;</a>";
				echo "</span>";
			}
			
			// Affichage de la date sous forme de calandrier
			echo "<div style='float:left' class='omo-calendrier'><div class='omo-date-mois'>".$month[$meeting->getDate()->format("n")].($meeting->getDate()->format("y")!=date("y")?" ".$meeting->getDate()->format("n"):"")."</div><div  class='omo-date-nojour'>".$meeting->getDate()->format("d")."</div><div class='omo-date-jour'>".$day[$meeting->getDate()->format("w")]."</div></div>";
			
			echo "<div><b>";			
			echo "Réunion de <a href='meeting.php?id=".$meeting->getId()."'>";
			echo $meeting->getMeetingType();
			echo "</a><br/>".T_(htmlentities("pour le cercle"))." <span style='white-space: pre;'><a href='circle.php?id=".$meeting->getCircle()->getId()."'>".$meeting->getCircle()->getName()."</a></span>"."</b></div><div style='font-size:smaller'>";
			
			// Affiche des infos sur la réunion, comme si elle est en cours, terminée, ou ses horaires
			if ($meeting->getClosingTime()!="") {
				echo T_(htmlentities("La réunion est terminée depuis le ")).$meeting->getClosingTime()->format("d.m.Y à H:i");
			} else if ($meeting->getOpeningTime()!="") {
				echo T_(htmlentities("La réunion est en cours depuis ")).$meeting->getOpeningTime()->format("H:i, \l\e d.m.Y");				
			} else {
				echo T_(htmlentities("De ")).substr($meeting->getStartTime(),0,-3)." "."à"." ".substr($meeting->getEndTime(),0,-3).", ".($meeting->getLocation()!=""?$meeting->getLocation():"<i>lieu indéfini</i>");
			}
			echo "</div><div style='clear:both;'></div>";

			
			echo "</div>";
		}
			echo "<div style='margin-top:15px; padding-top:10px;border-top:4px solid rgb(98, 124, 146)'>".T_(htmlentities("Importez le calendrier dans Google")).": <a href='/interface/ical.php?org=".$object->getId()."'><img style='vertical-align:bottom' src='/images/ical-feed.png'></a></div>";

		}
	}

	public function display() {
		
		// Contrôle que l'on ai le droit de visualiser cette page
		if (isset($this->_organisation) && !is_array($this->_organisation)) {
		if ( isset($_SESSION["currentUser"])) {
			$isMember=$this->_organisation->isMember($_SESSION["currentUser"]);
			$isAdmin=$this->_organisation->isAdmin($_SESSION["currentUser"]);
			$visibility=$this->_organisation->getVisibility();
			if ($isMember || $isAdmin || $visibility==2) {
				// Ok, ça marche... y a-t-il des choses à faire?
			} else {
				// La visualisation n'est pas souhaitée, redirige sur la page de login.
				
				header("location:index.php");
				exit;
			}
		} else {
			// Le visiteur sera créé après... mais la page est-elle en mode publique ou semi-publique?
			$visibility=$this->_organisation->getVisibility();
			if (!$visibility==2) {
				header("location:index.php");
				exit;
			}
		}}
	
?>
<html>
<head>
	<meta name="viewport" content="width=device-width"/>
	<!-- Chargement des scripts et styles pour jquery et jquery-ui -->
		<script src="/plugins/jquery-2.1.0.min.js"></script>
		<script src="/plugins/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>

	   <!-- pour éditer l'agenda ??? -->
	   <script type="text/javascript" src="/plugins/timepicker/jquery.ui.timepicker.js?v=0.3.3"></script>

	<script src="/scripts/organisation.js"></script>
	<script src="/scripts/shortcuts.js"></script>
	
	<link href="/plugins/select2/select2.min.css" rel="stylesheet" />
	<script src="/plugins/select2/select2.min.js"></script>

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
		// fonction de refresh à mettre ici pour avoir accès à l'ID de l'organisation actuelle.
		function refreshChecklist(organisation) {

			$("#tabs-4").load("/class/widget/wg_organisationbrowser/refresh.php?refresh=checklist&organisation=<? if (!is_array($this->_organisation)) echo $this->_organisation->getId();?>");
		}
		
		function refreshMetrics(organisation) {

			$("#tabs-5").load("/class/widget/wg_organisationbrowser/refresh.php?refresh=metrics&organisation=<? if (!is_array($this->_organisation)) echo $this->_organisation->getId();?>");
		}
	</script>
	
	<!-- Chargement des styles propre au site -->
	<!--<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/omo.css" />-->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=Ubuntu" />
		<link rel="stylesheet" href="style/templates/<?=$_SESSION["template"]?>/circle.css" />

	<link rel="stylesheet" href="/plugins/timepicker/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />

	
	<!-- Info sur la page -->
	<title>
<?
		if (!isset($_SESSION["currentUser"])) 
			echo "O.M.O | Login";
		else
			if (is_array($this->_organisation)) 
				echo "O.M.O | Bienvenue";
			else
				echo $this->_organisation->getName()." | Tableau de bord";
?>	
	
	</title>
	<style>
	/* Login */
	table.accueil{text-align:center; margin : 0px auto auto auto; padding-top:50px; width:570px; }
	input#user_login, input#user_password{padding:2px; width:120px; font-size:12px; display: inline-block;}
	button#btn_login {padding:2px; width:140px; font-size:11px; display: inline-block;}
	#password_refresh {float:right; display:block;}

	@media all and (max-width: 600px)  {
		table.accueil{text-align:center; margin : 0px auto auto auto; padding-top:0px; width:300px; }
		input#user_login, input#user_password{padding:5px; width:290px; font-size:16px; display: block;}
		#img_spit {display:none}
		input[type='checkbox'] {   padding-left:5px;
    padding-right:5px;


    border: double 2px #00F;


    width:25px;
    height:25px;}
	}

</style>
    <!--[if lte IE 8]><script type="text/javascript" src="plugins/DD_roundies_0.0.2a.js"></script><script>DD_roundies.addRule('.cadre', '20px');
</script><![endif]-->



</head>
<body>
<? 
	echo "<div id='main_waiting_screen'>".\widget\Widget::FULL_WAITING_SCREEN."</div>";
		


	// Fonctions pour les miniatures d'images
		include_once($_SERVER['DOCUMENT_ROOT'] . "/plugins/libMiniature.php");
		$this->_displayNav($this->_organisation);		
		//Page Index de OMO
		if (is_array($this->_organisation)) {			
				echo "<div id='organisationBrowser' class='main'>";
				echo "<table class='accueil grey_design'><tr>";
				echo "<td>";

				echo "<div style='padding:5px; font-weight:bold;' class='ui-state-default ui-state-active ui-corner-top '>";
				echo "Choisissez une organisation";
				echo "</div>";
				echo "<div style='padding:15px; margin-bottom:6px;' class=' ui-helper-reset ui-widget-content ui-corner-bottom '>";

	echo "<div class='omo-help-title'><b>Organisations:</b> Liste des organisations auxquelles vous êtes affiliés. <a href='#' class='omo_act_more_help'>Afficher plus...</a></div>";
	echo "<div class='omo-more-help'>Les rôles, cercles et premiers liens peuvent partager l'état de réalisation d'un certain nombre de tâches récurrentes afin que tous les membres aient une vision réaliste du status des tâches importantes. Chaque rôle/cercle est tenu de maintenir à jour ces informations avec la régularité spécifiée (hebdomadaire, mensuel,...). Voir les articles <a href='https://dev.openmyorganization.com/constitution.php?q=check-list#idx_3_0_0' target='_constitution'>4.1.1</a> et les <a href='https://dev.openmyorganization.com/constitution.php?q=check-list#idx_5' target='_constitution'>redevabilités des rôles structurels</a>.</div>";


				$publicorg="";
				$countOrg=0;
				foreach ($this->_organisation as $orga) {
					echo "<!-- test -->";
					// Affiche toutes les orga dont on est membre, et garde les autres pour l'outils de recherche en dessous
					$admin=$_SESSION["currentUser"]->isAdmin($orga);
					echo "<!-- test 2 -->";
					$member=$_SESSION["currentUser"]->isMember($orga);
					echo "<!-- test 3 -->";
					
					if ($admin || $member) {
						$countOrg++;
						echo "<a href='organisation.php?id=".$orga->getId()."' class='ui-corner-all";
						if ($admin) { 
							echo " omo-org-admin";
						} else	if ($member) { 
							echo " omo-org-member";
						} else {
							echo " omo-org-visitor";
						}
						echo "'><h3>".$orga->getName()."</h3>".$orga->getDescription();
						
							// Si c'est un administrateur, lui donne accès à la page d'administration de l'organisation
						if ($admin) { 
							echo "<div class='omo-btn-container omo-right'>";
							echo "<span class='omo-delete-btn omo-btn'  omo-param='".$orga->getId()."' title='Supprimer cette organisation'></span>";
							echo "<span class='omo-edit-btn omo-btn' omo-param='".$orga->getId()."' title='Editer cette organisation'>Editer</span>";
							echo "</div><div style='clear: both;'></div>";
						}
						
						echo "</a>";
					} else {
						$publicorg.="<option value='".$orga->getId()."'>".$orga->getName()."</option>";
					}
				}

				// Aucune organisation affichée?
				if ($countOrg==0) {
					echo "<div class='omo-warning-title'>Vous n'êtes affilié à aucune organisation. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
					echo "<div class='omo-more-warning'>";
						echo "Si vous souhaitez rejoindre une organisation existante, regardez avec l'un de ses membre pour vous faire inviter. Vous pouvez également découvrir les organisations publiques en les choisissant dans la liste déroulante, ou créer une nouvelle organisation en cliquant sur le bouton. Découvrez ci-dessous comment créer une nouvelle organisation.";
						echo "<div class='videolist'><div class='video'>";
						echo "<h1>Créer une organisation</h1>";
						echo "<hr>";
						echo '<iframe width="280" height="157" src="https://www.youtube.com/embed/YqMEZZEz1-Y?rel=0" frameborder="0" allowfullscreen></iframe>';
						echo "</div></div>";
					echo "</div>";
				} 				
				
				// S'il y a des organisation publiques, les affiche dans une liste à choix multiple
				if ($publicorg!="") {
						echo "<div class='ui-corner-all omo-org-visitor'>";
						echo "<h3>Organisations publiques</h3>";
						echo "<div class='omo-btn-container omo-right'>";

							echo "<select id='select_vieworg'  style='width:75%' name='select_vieworg'>";
							echo "<option></option>";
							echo $publicorg;
							echo "</select>";
							echo "<span class='omo-btn'  id ='omo-btn-view' title='Afficher cette organisation'>Afficher</span>";
						echo "</div><div style='clear: both;'></div>";
					echo "</div>";
	?>
	<script>
		$("#select_vieworg").select2({
			placeholder: "Sélectionnez une organisation",
			allowClear: true,
			minimumResultsForSearch: 10
		});
	</script>
	<?				
					
				}
				
				// Affiche le bouton pour ajouter une organisation, seulement si pas un visiteur
				if ($_SESSION["currentUser"]->getId()>1) {
						echo "<div class='omo-btn-container'>";
						echo "<span class='omo-new-btn omo-btn' id='btn_new_org' title='Ajouter une nouvelle organisation'>Créer une organisation</span>";
						echo "</div>";
				}
				
				
				echo "</div>";
			echo "</td></tr></table></div>";
	
		} 
		
		//Voir une ORG 
		else {
			

?>

		
		<div class='omo-maindiv' >
			<div class='omo-leftcol' >
			
<div id="tabs_gauche">
  <ul>
    <li><a name="tabsG-1" href="#tabsG-1"><? print T_("Agenda"); ?></a></li>
    <li><a name="tabsG-2" href="#tabsG-2"><? print T_("Membres"); ?></a></li>
    </ul>
     <div id="tabsG-1">
		
		
			<?php 
	
	
	// Affichage de l'agenda des prochains rendez-vous

			$this->_displayMeeting($this->_organisation);
?>

	</div>
     <div id="tabsG-2">
<?
			echo "<div id='organisation_members'>";
			echo "".\widget\Widget::WAITING_SCREEN."";
			echo "</div>";
?>
	</div>
</div>
</div> <!-- omo-leftcol -->
			<div class='omo-rightcol' >

			
			<div id="tabs_droite">
 <ul>
    <li><a name="tabs-1" href="#tabs-1"><span class='omo-purpose'><span class='omo-tab-label'>Notre raison d'Être</span></span></a></li>
 <?
	if (isset($isMember) && $isMember) {
 ?>
   <li><a name="tabs-2" href="#tabs-2"><span class='omo-role'><span class='omo-tab-label'><? print T_("Tous mes r&ocirc;les"); ?></span></span></a></li>
    <li><a name="tabs-4" href="#tabs-4"><span class='omo-checklist'><span class='omo-tab-label'><? print T_("Check-lists"); ?></span></span></a></li>
    <li><a name="tabs-5" href="#tabs-5"><span class='omo-metrics'><span class='omo-tab-label'><? print T_("Indicateurs"); ?></span></span></a></li>
    <li><a name="tabs-6" href="#tabs-6"><span class='omo-projects'><span class='omo-tab-label'><? print T_("Projets"); ?></span></span></a></li>
<?
	} else {
		echo "   <li><a name='tabs-2' href='#tabs-2'><span class='omo-structure'><span class='omo-tab-label'>Structure holarchique</span></span></a></li>";
	}
?>
  </ul>
  <div id="tabs-1">
<?

 	echo "<div class='omo-help-title'><b>Raison d'Être:</b> Raison pour laquelle l'organisation existe. <a href='#' class='omo_act_more_help'>Afficher plus...</a></div>";
	echo "<div class='omo-more-help'>Exprimée sous la forme d'une vision, d'une mission et d'une liste de valeurs afin d'aider et soutenir chaque membre de l'organisation dans l'adéquation entre ses actions et le but commun. Voir l'articles <a href='https://dev.openmyorganization.com/constitution.php?q=raison%20d%27etre#idx_4_1_2' target='_constitution'>5.2.3</a> de la Constitution.</div>";

	// Affichage des infos d'orientation
	if ($this->_organisation->getVision()!="" || $this->_organisation->getMission()!="") {
		// Affichage du duo vision/mission si renseigné
		if ($this->_organisation->getVision()!="") {
		echo '<fieldset><legend><div id="mask1"></div><span class=\'omo-vision\'>Notre vision</span><a class=\'omo-help\' target=\'help\' href=\'help.php?key=vision\'>&nbsp;</a><div id="mask2"></div></legend>';
		echo '<div class="vision">';
		echo str_replace("\n","<br>",str_replace("<br/>","",str_replace("<br>","",$this->_organisation->getVision())));
		echo "</div>";	
		echo "</fieldset>";
		}		
		if ($this->_organisation->getMission()!="") {
		echo '<fieldset id="slidecontainer"><legend><div id="mask1"></div><span class=\'omo-mission\'>Notre mission</span><a class=\'omo-help\' target=\'help\' href=\'help.php?key=mission\'>&nbsp;</a><div id="mask2"></div></legend>';
		echo '<div class="mission">';
		echo str_replace("\n","<br>",str_replace("<br/>","",str_replace("<br>","",$this->_organisation->getMission())));
		echo "</div>";	
		echo "</fieldset>";

		
		// Affichage des valeurs, en animation
	$values=$this->_organisation->getValues();
	
	if (count($values)>0) {	
				
		echo '<fieldset><legend><div id="mask1"></div><span class=\'omo-values\'>Nos valeurs</span><div id="mask2"></div></legend>';
	
?>
		    <!-- #region Jssor Slider Begin -->
    <!-- Generator: Jssor Slider Maker -->
    <!-- Source: https://www.jssor.com -->
    <script src="js/jssor.slider-26.1.5.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        jssor_1_slider_init = function() {

            var jssor_1_options = {
              $AutoPlay: 1,
              $DragOrientation: 2,
              $PlayOrientation: 2,
              $Align: 0,
              $BulletNavigatorOptions: {
                $Class: $JssorBulletNavigator$,
                $SpacingY: 2,
                $Orientation: 2
              }
            };

            var jssor_1_slider = new $JssorSlider$("jssor_1", jssor_1_options);

            /*#region responsive code begin*/

            var MAX_WIDTH = "980px";

            function ScaleSlider() {
				
				
				
                var containerElement = jssor_1_slider.$Elmt.parentNode;
                var containerWidth = $("#slidecontainer").innerWidth();//containerElement.clientWidth;
                if (containerWidth) {
					

                    jssor_1_slider.$ScaleWidth(containerWidth-20);
                }
                else {
                    window.setTimeout(ScaleSlider, 30);
                }
            }

            ScaleSlider();

            $Jssor$.$AddEvent(window, "load", ScaleSlider);
            $Jssor$.$AddEvent(window, "resize", ScaleSlider);
            $Jssor$.$AddEvent(window, "orientationchange", ScaleSlider);
            /*#endregion responsive code end*/
        };
    </script>
    <style>
        /* jssor slider loading skin spin css */
        .jssorl-009-spin img {
            animation-name: jssorl-009-spin;
            animation-duration: 1.6s;
            animation-iteration-count: infinite;
            animation-timing-function: linear;
        }

        @keyframes jssorl-009-spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

		.omo_value {
			font-size:250%;
		}
		.omo_principle {
			width:95%;
			font-size:120%;
			box-sizing:border-box;
		}
		.vision, .mission {
			font-size:120%;
		}
        .jssorb031 {position:absolute;}
        .jssorb031 .i {position:absolute;cursor:pointer;}
        .jssorb031 .i .b {fill:#000;fill-opacity:0.5;stroke:#fff;stroke-width:1200;stroke-miterlimit:10;stroke-opacity:0.3;}
        .jssorb031 .i:hover .b {fill:#fff;fill-opacity:.7;stroke:#000;stroke-opacity:.5;}
        .jssorb031 .iav .b {fill:#fff;stroke:#000;fill-opacity:1;}
        .jssorb031 .i.idn {opacity:.3;}
    </style>
   <div id="jssor_1" style="position:relative;margin:0 auto;top:0px;left:0px;width:800px;height:165px;overflow:hidden;visibility:hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" class="jssorl-009-spin" style="position:absolute;top:0px;left:0px;width:100%;height:100%;text-align:center;background-color:rgba(0,0,0,0.7);">
            <img style="margin-top:-19px;position:relative;top:50%;width:38px;height:38px;" src="img/spin.svg" />
        </div>
        <div data-u="slides" style="cursor:default;position:relative;top:0px;left:0px;width:800px;height:165px;overflow:hidden;">

<?

	foreach ($values as $value) {
	    
           echo " <div>";
            echo "     <div class='omo_value'>".$value->getLabel()."</div>";
            $principles=$value->getPrinciples();
            if (count($principles)>0) {
				echo "    <div class='omo_principle'>".$principles[0]->getDescription()."</div>";
            }
            echo "</div>	";
	}
?>


            <a data-u="any" href="https://www.jssor.com" style="display:none">html slideshow</a>
        </div>
        <!-- Bullet Navigator -->
        <div data-u="navigator" class="jssorb031" style="position:absolute;bottom:0px;right:0px;" data-scale="0.5">
            <div data-u="prototype" class="i" style="width:12px;height:12px;">
                <svg viewbox="0 0 16000 16000" style="position:absolute;top:0;left:0;width:100%;height:100%;box-sizing:border-box">
                    <circle class="b" cx="8000" cy="8000" r="5800"></circle>
                </svg>
            </div>
        </div>
    </div>
    <script type="text/javascript">jssor_1_slider_init();</script>
    <!-- #endregion Jssor Slider End -->
<?		
				echo "</fieldset>";
			}
		}
	} else {

	// Affichage de la raison d'être complète

	if ($this->_organisation->getPurpose()!="") { 
			?>
				<fieldset><legend><div id="mask1"></div><span class='omo-purpose'><? print T_("Raison d'&ecirc;tre"); ?></span><a class='omo-help' target='help' href='help.php?key=raison_etre'>&nbsp;</a><div id="mask2"></div></legend>
			<div class="content" id="content_4" style='font-weight:bold'><?php echo str_replace("\n","<br>",str_replace("<br/>","",str_replace("<br>","",$this->_organisation->getPurpose())));?></div>
<?
		if ($this->_organisation->getPurposeDescription()!="") { 
	?>
			<hr style='width:50%'/>
			<div class="content" id="content_4"><?php echo str_replace("\n","<br>",str_replace("<br/>","",str_replace("<br>","",$this->_organisation->getPurposeDescription())));?></div></fieldset>
<? } echo "</fieldset>";} else {

		// Affichage du tutoriel de ce qu'est une raison d'être
		echo "<div class='omo-warning-title'>Il n'y a pas de raison d'Être définie pour cette organisation. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
	echo "<div class='omo-more-warning'>";
			echo "La raison d'Être permet à chacun d'oeuvrer individuellement en direction d'un but commun. Découvrez ci-dessous pourquoi et comment la mettre en place.";
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
}	 
?> 
  </div>
<!-- Onglet 2, avec les différents rôles -->  
  <?
	if (isset($isMember) && $isMember) {
 ?>
  <div id="tabs-2">
<?
 	echo "<div class='omo-help-title'><b>Rôles:</b> Liste de tous les rôles que vous énergétisez. <a href='#' class='omo_act_more_help'>Afficher plus...</a></div>";
	echo "<div class='omo-more-help'>Liste de tous les rôles auquels vous êtes assignés dans l'organisation, avec la possibilité de voir leur raison d'Être, leurs redevabilités et leurs domaines. Pour chacun de ces rôles, vous êtes tenus de remplir un certain nombre d'obligations telles que définies dans la constitution dans les articles <a href='https://dev.openmyorganization.com/constitution.php?q=role#idx_0_1' target='_constitution'>1.2</a> et <a href='https://dev.openmyorganization.com/constitution.php?q=role#idx_3_0_0' target='_constitution'>4.1.1</a>.</div>";
?>


 
<?php
	// Affichage des autres rôles (non-structurels)
	$liste=$_SESSION["currentUser"]->getRoles($this->_organisation,\holacracy\Role::STANDARD_ROLE | \holacracy\Role::LINK_ROLE | \holacracy\Role::STRUCTURAL_ROLES);
	if (count($liste)>0) {
		echo ' <fieldset><legend><div id="mask1"></div><? print T_("R&ocirc;les"); ?><div id="mask2"></div></legend>';
		echo '<div id="accordion">';		
		$this->listeRole ($liste,1,1);
		echo '  </div>';
		echo ' </fieldset>';
	} else {
		
		// Affichage du tutoriel de ce qu'est une checkliste et comment la créer

		echo "<div class='omo-warning-title'>Vous n'avez aucun rôle attribué. <a href='#' class='omo_act_more_warning'>En savoir plus...</a></div>";
		echo "<div class='omo-more-warning'>";
				echo "L'organisation ne vous a pas encore attribué de rôle. C'est à travers les rôles que vous pourrez agir dans et pour l'organisation. Les rôles viennent définir un champ d'autorité et de liberté dans lequel vous pourrez évoluer et faire preuve d'initiatives. Découvrez ci-dessous pourquoi et comment prendre des rôles en charge.";
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

<!-- Autres onglets  -->  
 <!-- <div id="tabs-2">
     <? //include "wg_circlebrowser/onglet_politique.php" ?>
  </div>
  <div id="tabs-3">
    <? //include "wg_circlebrowser/onglet_historique.php" ?>
  </div>-->
  <div id="tabs-4">
     <? 
      	$checklist=$_SESSION["currentUser"]->getChecklists(false, $this->_organisation);
	 	include "wg_circlebrowser/onglet_checklist.php"; 
	?>
  </div>
  <div id="tabs-5">
    <? 
		$metrics=$_SESSION["currentUser"]->getMetrics(false, $this->_organisation);
		include "wg_circlebrowser/onglet_metrics.php"; 
	?>
  </div>
  <div id="tabs-6">
    <? include "wg_organisationbrowser/onglet_projets.php" ?>
  </div>
 <?
	} // Fin de l'affichage des onglets uniquement pour les membres
	else {
?>
  <div id="tabs-2">
    <? 
		include "wg_organisationbrowser/onglet_structure.php"; 
	?>
  </div>	
 <?	
	}
?>
			  </div>
			  
			  
			</div> <!-- omo-rightcol -->
</div> <!-- omo-maindiv -->
<script>
			$(document).ready(function(){

				$("#tabs_gauche").tabs({heightStyle: "fill"});
				$("#tabs_droite").tabs({heightStyle: "fill"});
				
				$(window).resize(function () {
						$("#tabs_gauche").tabs({heightStyle: "fill"});
						$("#tabs_droite").tabs({heightStyle: "fill"});
				
				})
				
			});
</script>
<?


		/*	}
			else{
			echo '<meta http-equiv="refresh" content="0;URL=/">';}*/

			?>
			<script>
				// Script nécessitant des variables PHP
				$("#organisation_members").load("ajax/deleteuserorganisation.php?orga=<?=$this->_organisation->getId()?>");
	
			</script>
<?
		}
		include_once("class/widget/nav/nav.php");
?>		


	</body>
	</html>
<?		
	}
}

?>



	
