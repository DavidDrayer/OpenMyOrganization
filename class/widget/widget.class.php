<?php
	namespace widget;
// Cette classe affiche un browser HTML permettant de parcourir un objet de type "cercle" dans son
// intégralité : liste de rôles, sous-cercles, projets, liste de membres, etc...
class  Widget
{

	const WAITING_SCREEN = "<div style='position:absolute; z-index: 6; top:0px; left:0px; height:100%; width:100%; background-color:#FFFFFF'><table width='100%' height='100%'><tr><td style='vertical-align:middle; text-align:center'><img src='/images/loading_ajax.gif'><br/>Chargement...</td></tr></table></div>";
	const FULL_WAITING_SCREEN = "<div style='position:fixed; z-index: 9010; top:0px; left:0px; height:100%; width:100%; background-color:#FFFFFF'><table width='100%' height='100%'><tr><td style='vertical-align:middle; text-align:center'><img src='/images/loading_ajax.gif'><br/>Chargement...</td></tr></table></div>";
	const OBJECT_DELETED_SCREEN = "<div style='position:fixed; z-index: 2; top:0px; left:0px; height:100%; width:100%; 	background-color: rgba(174, 196, 216, 0.5);'><table width='100%' height='100%'><tr><td style='vertical-align:middle; text-align:center'><span style='font-size:70px; color: rgba(133, 150, 165, 1);'>Élément supprimé</span></td></tr></table></div>";
	
	private function getPath($object) {
				switch (get_class($object))	{

					case "holacracy\\Role" :
					case "holacracy\\Circle" :

							$tmp_str=$object->toHTMLString()."</td>";
							if (get_class($object)=="holacracy\\Circle") $tmp_str= "<span class='omo-circle'/>".$tmp_str."</span>";
							$tmp_str= "<td class='nav_std'>".$tmp_str;
						
							if ($object->hasSuperCircle()) {
								$tmp_str=$this->getPath($object->getSuperCircle()).$tmp_str;
							} else {
								$tmp_str=$this->getPath($object->getOrganisation()).$tmp_str;
							}
							return $tmp_str;
							break;
					case "holacracy\\Organisation" :

						if ($object->getName()=="")
							return "<td class='nav_std'>"."Nouvelle organisation"."</td>";
						else
							return "<td class='nav_std'><a href='organisation.php?id=".$object->getId()."'>".$object->getName()."</a></td>";
						break;					
						
					default:
						$tmp_str="<td>Aucune navigation pour ce type d'objet</td>";
				}	
	}
	
	// Procédure interne pour afficher une liste de rôles
	// Entrée: la liste de rôles à afficher sous forme d'array()
	// Sortie : à l'écran
	public function listeRole ($liste, $column=1, $display=0) {
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
			$roleFillers=$role->getRoleFillers();
			if ($display==1) {
				// Affichage du cercle du rôle
				if (strpos(strtoupper($role->getSuperCircle()->getName()), "CERCLE")===FALSE)
					echo "Cercle ".$role->getSuperCircle()->getName();
				else
					echo $role->getSuperCircle()->getName();
			} else {
			// Charge la liste des gens en charge du rôle

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
			
			// Possibilité de réafecter le rôle uniquement dans un cercle
			if (isset($this->_circle)) {
				if ( isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$this->_circle) && $type != 2 || $_SESSION["currentUser"]->isAdmin()) && !($role->getSourceCircleId()!="")) { //Si le role n'est pas un 1er Lien
					echo "<img src='images/edit-user.png' href='formulaires/form_affect_user.php?circle=".$this->_circle->getId()."&role=".$role->getId()."' class='dialogPage' alt='".T_("Assigner le r&ocirc;le ").$role->getName()."'/>";
				}
			}
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
				// Attribué à personne
				echo "<fieldset class='light'><legend><div id='mask1'></div><span class='omo-user'>".T_("Energ&eacute;tis&eacute; par")."</span><div id='mask2'></div></legend>";
				echo "Affecté par défaut au 1er lien";
				echo "</fieldset>";
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
	
	// Fonction pour rajouter des liens HTML sur les noms de rôles précédés de @ - DDr, 18.6.2014
	public function addLinkToTxt($txt, $context) {
		// Expression régulière pour trouver les éléments - DDr, 18.6.2014
		preg_match_all('/@([^$\s\n\r\.<>]*)[<$\s\n\r\.>]?/',$txt, $out, PREG_SET_ORDER);
		foreach ($out as $find) {
			// Recherche le rôle associé - DDr, 18.6.2014
			
			// Remnplace le texte  - DDr, 18.6.2014
			$txt=str_replace("@".trim($find[1]),"<a href='role.php?id='>".trim($find[1])."</a>",$txt);
		}
		return $txt;
		
	}
	
		protected function _displayCircles($organisation, $level=0) {
			
	// Fonctions pour les miniatures d'images
		include_once($_SERVER['DOCUMENT_ROOT'] . "/plugins/libMiniature.php");

		$circles=$organisation->getCircles();
			foreach ($circles as $circle) {
				echo "<div class='cadre cadre".(isset($_SESSION["currentUser"]) && $_SESSION["currentUser"]->isMember($circle)?"jaune":"gris")."_".$level."'><table>";
				//echo "<tr><td colspan=2></td></tr><h3><a href='circle.php?id=".$circle->getId()."'>".$circle->getName()."</a></h3>";
				echo "<tr><td><div id='omo-org-picture'><h3><a href='circle.php?id=".$circle->getId()."'>".$circle->getName()."</a></h3>";
				if ($circle->getLeadLink()) {
					if ($circle->getUserId()>0) {
						$member=$circle->getUser();
						if (checkMini("/images/user/".$member->getId().".jpg",30,30,"mini",1,5)) {
							echo "<a title='Premier lien: ".str_replace("'","&#39;",$member->getFirstName()." ".$member->getLastName())."' href='/user.php?id=".$member->getId()."&organisation=".$organisation->getId()."' class='dialogPage' alt='".$member->getFullName()."'><img class='omo-user-img' src='/images/user/mini/".$member->getId().".jpg'/></a>";
						} else {
							echo "<a title='Premier lien: ".str_replace("'","&#39;",$member->getFirstName()." ".$member->getLastName())."' href='/user.php?id=".$member->getId()."&organisation=".$organisation->getId()."' class='dialogPage' alt='".$member->getFullName()."'><img class='omo-user-img' src='/images/user/mini/0.jpg'/></a>";
						}

					} else {
							echo "<img title='Premier lien non attribué' class='omo-user-img' src='/images/user/mini/0.jpg'/>";
					}
				} else {echo "";}

				if ($circle->getRepLink()) {
					if ($circle->getRepLink()->getUserId()>0) {
						$member=$circle->getRepLink()->getUser();
						if (checkMini("/images/user/".$member->getId().".jpg",30,30,"mini",1,5)) {
							echo "<a title='Second lien: ".str_replace("'","&#39;",$member->getFirstName()." ".$member->getLastName())."' href='/user.php?id=".$member->getId()."&organisation=".$organisation->getId()."' class='dialogPage' alt='".$member->getFullName()."'><img class='omo-user-img' src='/images/user/mini/".$member->getId().".jpg'/></a>";
						} else {
							echo "<a  title='Second lien: ".str_replace("'","&#39;",$member->getFirstName()." ".$member->getLastName())."' href='/user.php?id=".$member->getId()."&organisation=".$organisation->getId()."' class='dialogPage' alt='".$member->getFullName()."'><img class='omo-user-img' src='/images/user/mini/0.jpg'/></a>";
						}

					} else {
							echo "<img title='Second lien non attribué' class='omo-user-img' src='/images/user/mini/0.jpg'/>";
					}
				} else {echo "";}
				//echo "<br/>";
				$roles=$circle->getRoles(\holacracy\Role::STANDARD_ROLE | \holacracy\Role::LINK_ROLE);
				$title="<b>".count($roles)." role(s) : </b><br/>";
				foreach ($roles as $role) {
					$title.=$role->toString()."<br/>";
				}
				echo "<span title='".str_replace("'","&#39;",$title)."' style='display:inline-block; margin:2px; text-align:center; font-size:20px; padding-top:3px; width:30px; height:27px; border:2px solid black'>".count($roles)."</span>";
			
				
				echo "</td><td>";
				$this->_displayCircles($circle,$level+1);
				echo "</td></tr></table></div>";
			}
	}
	
	
	protected function _displayNav($object, $edit=false) {
		if (!isset($_SESSION["currentUser"]) ) {
			$_SESSION["currentUser"]=$_SESSION["currentManager"]->loadUser(1);
			//Si la session existe on active la langue
			$languser = $_SESSION["currentUser"]->getUserLangue();
			if (isset($basepath)) $_SESSION["currentUser"]->ActiveLanguage($languser,$basepath);
		} 
		
		// Défini la visibilité de certains éléments en fonction de l'utilisateur
		if (!isset($_SESSION["currentUser"]) || $_SESSION["currentUser"]->getId()<2) {
			echo "<style>\n";
			echo ".omo-help-title {display:none;}\n";
			echo ".omo-warning-title {display:none;}\n";
			echo ".getmoney {display:none;}\n";
			echo "</style>\n";
		}

		if (!isset($_SESSION["currentUser"]) || $_SESSION["currentUser"]->getId()!=3) {
			echo "<style>\n";
			echo ".video {display:none !important}\n";

			echo "</style>\n";
		}
		
		// Affiche la levée de fonds
		echo "<div class='getmoney' ".(isset($_COOKIE["noCrowdfunding"])?"style='top:-120px'":"") .">";
		echo "<div class='getmoney_text'>";
		echo "<div class='getmoney_title'>Soutenez le développement et l'amélioration d'OpenMyOrganization</div>";
		
		echo "<div class='getmoney_bar' style='background-size:2%'>160 sur 10'000</div>";
		echo "<table><tr><td width='50%'>Open my organization a besoin de votre contribution pour devenir stable avant de rejoindre la grande famille des logiciels libres.</td><td><input type='button' id='btn_pay' value='Faire un don'> ou <input type='button' id='btn_paymore' value='Voir la liste des améliorations'> <input type='checkbox' id='chk_nomorewindow' style='vertical-align:bottom' ".(isset($_COOKIE["noCrowdfunding"])?" checked":"") ."> Ne plus afficher cette fenêtre</td></tr></table>";
		echo "</div>";
		echo "<div class='getmoney_onglet' >Soutenez OMO!</div>";
		echo "</div>";
		
		// Affiche la navigation par cercle
		//if (isset($_SESSION["currentUser"])) {
			if (isset($object) && is_object($object)) {
		echo "<div id='circle_map'><div id='map_onglet'></div><div id='inside_map'>";
		
		// Affichage des cercles
			echo "<table style='height:100%'><tr><td style='vertical-align:middle'>";
			switch (get_class($object))	{
				case "holacracy\\Organisation" :
					$this->_displayCircles($object);
					break;					
				case "holacracy\\Meeting" :
				case "holacracy\\Role" :
				case "holacracy\\Circle" :
					$this->_displayCircles($object->getOrganisation());
					break;
			}

			echo "</td></tr></table>";			
		
		echo "</div></div>";
	}
		echo "<div class='mainNav'><div class='title'>";
		//}

				echo "<script>\n";
  echo "var HW_config = {\n";
 echo "   selector: \".changelog\", // CSS selector where to inject the badge\n";
  echo "  account: \"Q7kAPy\", // your account ID\n";
  echo "translations: {\n";
   echo " title: \"Toutes les nouveautés\",\n";
   echo " labels: {\n";
   echo "   \"new\": \"Nouveau\",\n";
   echo "   \"improvements\": \"Améliorations\",\n";
    echo "  \"fix\": \"Corrections\"\n";
   echo " }\n";
  echo "}\n";
echo "  };\n";
echo "</script>\n";
echo "<script async src=\"//cdn.headwayapp.co/widget.js\"></script>\n";

		echo "<span id='logo'></span>";
		echo "<span class='changelog' style='position:absolute; top:-4px; padding-left:13px;'></span>";


		
	//	if (isset($_SESSION["currentUser"])) {
		
			echo "<div class='login'><form id='form_login'>";
			// Affichage de l'image si elle existe
			include_once($_SERVER['DOCUMENT_ROOT'] . "/plugins/libMiniature.php");
			echo "<table><tr>";
			//Affichage BUG
			
				echo "<td><input name='search_field' id='search_field' style='width:190px;' placeholder='Rechercher...'/>";
				
				// En fonction de la navigation, choisi le contexte le plus approprié
				// Pour l'instant, simplement l'ID de l'organisation
				$context="";
				if (isset($object)) {
					// Est-ce un objet?
					if (is_object($object)) {
					
						switch (get_class($object))	{
							case "holacracy\\Organisation" :
								$context=$object->getId();
								break;					
							case "holacracy\\Meeting" :
							case "holacracy\\Role" :
							case "holacracy\\Circle" :
								$context=$object->getOrganisation()->getId();
								break;
						}
					} 
				}
				echo "<input type='hidden' name='search_context' id='search_context' value='$context'>";
				echo "</td><td><div><div>";
			    echo "<button id='search_button' href='/formulaires/form_search.php?querystring=[search_field]&context=[search_context]' class='dialogPage' alt='".T_("Lancer la recherche")."'>&nbsp;</button>";
			    //echo "<button id='detail_search_button'>&nbsp;</button>";
			 echo " </div> ";
			// echo "<ul>";
			 //   echo "<li><a href='/formulaires/form_search.php' class='dialogPage' alt='".T_("Ouvrir la recherche avanc&eacute;e")."'>".T_("Recherche avanc&eacute;e")."</a></li>";
			 //echo " </ul>";
			 echo "</div></td>";	
			 
			 //Affiche la fenêtre des tensions pour les cercles	
			 // Supprimé pour la version publique	
			/* if (isset($object)) {
				if (is_object($object)) {	
						switch (get_class($object))	{
						case "holacracy\\Circle" :
						echo "<td><a href='/formulaires/form_tension.php?id=".$object->getId()."' alt='".T_("Mes tensions dans le cercle ").$object->getName()." !' class='dialogPage'><img src='/images/mestensions.png' class='imgtension' title='".T_("Mes tensions dans ce cercle !")."'></a></td>";
						break;}
						}
			}*/
			 	
			 // Affiche le formulaire de bug et la gestion du compte seulement si ce n'est pas un invité							 
			if ($_SESSION["currentUser"]->getId()>1) {		
				echo "<td><a href='/formulaires/form_bug.php' alt='".T_("Un bug ? Une suggestion ? Merci de nous les partager !")."' class='dialogPage'><img src='/images/bug.png' class='imgreport' title='".T_("Un bug ? Une suggestion ? Merci de nous les partager !")."'></a></td>";
			
			
			if (checkMini("/images/user/".$_SESSION["currentUser"]->getId().".jpg",30,30,"mini",1,5)) {
				echo "<td><a href='/user.php?id=".$_SESSION["currentUser"]->getId()."' class='dialogPage' alt='".T_("Mon profil")."'><img class='imgprofilhaut' style='border:1px solid black' src='/images/user/mini/".$_SESSION["currentUser"]->getId().".jpg'/></a></td>";
			}
			// Affiche quelques infos et le menu USER
				echo "<td><div><div>";
			    echo "<button id='profil_button' href='/user.php?id=".$_SESSION["currentUser"]->getId()."' class='dialogPage' alt='".T_("Mon profil")."'>".$_SESSION["currentUser"]->getFirstName()." ".$_SESSION["currentUser"]->getLastName()."</button>";
			    echo "<button id='detail_profil_button'>&nbsp;</button>";
			 echo " </div> <ul>";
			 
			 
			 // Si c'est un administrateur, propose de prendre l'identité d'un autre user
			 if ($_SESSION["currentUser"]->isAdmin()) {
			    echo "<li><a href='/formulaires/form_changeidentity.php' class='dialogPage' alt='".T_("Changer d&apos;identit&eacute;")."'>".T_("Changer d&apos;identit&eacute;")."</a></li>";
			 
			 } else {
				// Sinon, possibilité de modifier le profil
			    echo "<li><a href='/formulaires/form_edituser.php' class='dialogPage' alt='".T_("Modifier mon profil")."'>".T_("Modifier mon profil")."</a></li>";
			 }
			  //Affiche le bouton pour gérer l'admin d'une ORG		
			if (isset($object)) {
				if (is_object($object)) {	
						switch (get_class($object))	{
						case "holacracy\\Circle" :
						$org = $object->getOrganisation();
						$orgId = $org->getId();
						if($_SESSION["currentUser"]->isAdmin($org)){
						 echo "<li><a href='/editorg.php?id=".$orgId."' alt='".T_("Modifier l'ORG")."'>".T_("Modifier l'ORG")."</a></li>";
						}
						break;
						case "holacracy\\Organisation" :
						$org=$object;
						$orgId = $object->getId();
						if($orgId>0 && $_SESSION["currentUser"]->isAdmin($org)){
						 echo "<li><a href='editorg.php?id=".$orgId."' alt='".T_("Modifier l'ORG")."'>".T_("Modifier l'ORG")."</a></li>";
						}
						break;}
						}
				}
				
				 echo "<li><a href='#' id='btn_login'>".T_("Me d&eacute;connecter")."</a></li>";
				 echo " </ul></div></td>";
			} else {
			// Affiche les champs pour la connexion
			echo "<td style='padding-left:20px;'>";
			
			echo "<input name='user_login' id='user_login' type='text' style='width:90px;' placeholder='User'>  <input style='width:90px;' placeholder='Password' name='user_password' id='user_password' type='password'> <button id='btn_login'>Se Connecter</button>";
			
			echo "</td>";
			//header("location:http://".$_SERVER["HTTP_HOST"]); 
			
		}
						
			//echo "<td style='text-align:right'><b>".$_SESSION["currentUser"]->getFirstName()." ".$_SESSION["currentUser"]->getLastName()."</b><button id='btn_login'>Me Déconnecter</button><br/><a href='/editProfil.php' class='dialogPage'>Modifier mon profil</a></td>"
			echo "</tr></table></form></div>";
	//	} 
?>

<?php //on inclus le script pour la barre de navigateur
include_once("class/widget/nav/nav.php"); 
?>
		
<?php

		
		
		echo "</div><table cellspacing=0 cellpadding=0 class='treeNav'><tr>";	
		if (!isset($object)) {
			// Pas d'objet passé, affiche une navigation de base
			echo "<td>Nav par défaut</td>";
		} else {
			// Est-ce un objet?
			if (is_object($object)) {
			
				$tmp_str="";
				switch (get_class($object))	{
					case "holacracy\\Meeting" :
						$tmp_str.= "<td  class='nav_title'><span class='omo-meeting'/>Réunion de ".$object->getMeetingType()." du ".$object->getDate()->format("d.m.y")."</span></td>";
						$tmp_str=$this->getPath($object->getCircle()).$tmp_str;				
						break;
					case "holacracy\\Role" :
					case "holacracy\\Circle" :

						$tmp_str.=  $object->toString()."</td>";
						if (get_class($object)=="holacracy\\Circle") $tmp_str= "<span class='omo-circle'/>".$tmp_str."</span>";
						if (get_class($object)=="holacracy\\Role") $tmp_str= "<span class='omo-role'/>".$tmp_str."</span>";
						$tmp_str= "<td class='nav_title'>".$tmp_str;
						if ($object->hasSuperCircle()) {
							$tmp_str=$this->getPath($object->getSuperCircle()).$tmp_str;
						} else {
							$tmp_str=$this->getPath($object->getOrganisation()).$tmp_str;
						}
						// Ajoute un élément de navigation
						if (get_class($object)=="holacracy\\Circle") {
							$circles=$object->getCircles();
							$roles=$object->getRoles(\holacracy\Role::STANDARD_ROLE | \holacracy\Role::LINK_ROLE);
							if (count($circles)>0 || count($roles)>0) {
								$tmp_str.= "<td style='font-weight:normal'>&nbsp;/ <select id='nav_select'>";
								$tmp_str.="<option>naviguer vers...</option>";
								
								if (count($circles)>0) {
									$tmp_str.= '<optgroup label="Sous-cercles">';
									foreach ($circles as $circle) {
										$tmp_str.="<option value='c_".$circle->getId()."'>".$circle->getName()."</option>";
									}
									$tmp_str.= '</optgroup>';
								}
								if (count($roles)>0) {
									$tmp_str.= '<optgroup label="Roles">';
									foreach ($roles as $role) {
										$tmp_str.="<option value='r_".$role->getId()."'>".$role->getName()."</option>";
									}
									$tmp_str.= '</optgroup>';
								}
								$tmp_str.="</select></td>";
							}
						}
						break;
						
					case "holacracy\\Organisation" :
						// En mode édition ou consultation?
						if ($edit) {
							$tmp_str.="<td class='nav_std'><a href='organisation.php?id=".$object->getId()."'>".$object->getName()."</a></td>";
							$tmp_str.= "<td class='nav_title'><span/>&nbsp;Edition</span></td>";
						} else {
						$tmp_str.= "<td class='nav_title'><span class='omo-organisation'/>".$object->getName()."</span></td>";
						// Ajoute un élément de navigation

							$tmp_str.= "<td style='font-weight:normal'>&nbsp;/ <select id='nav_select'>";
							$tmp_str.="<option>naviguer vers...</option>";
							$circles=$object->getCircles();
							foreach ($circles as $circle) {
								$tmp_str.="<option value='".$circle->getId()."'>".$circle->getName()."</option>";
							}
							$tmp_str.="</select></td>";
						}
						break;
					
						
					default:
						$tmp_str="<td>Aucune navigation pour ce type d'objet</td>";
				}
				echo "<td class='nav_home'><a href='/organisation.php'><img src='style/templates/".$_SESSION["template"]."/images/home.png' title='Retour à la liste des organisations'/></a></td>".$tmp_str;
			} else {
				echo "<td class='nav_home'><a href='/'><img src='style/templates/".$_SESSION["template"]."/images/home.png' title='Retour sur la page d&apos;accueil'/></a></td>";
			}
		}
		echo "</tr></table></div>";
		
		
	}
}
?>
