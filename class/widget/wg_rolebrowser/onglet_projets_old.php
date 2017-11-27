<?php

	if (isset($_GET["action"])) {
	
		include_once("../../../include.php");

		  	
		if ($_GET["action"]=="refresh") {
			if (isset($_GET["role"])) {
				$role=$_SESSION["currentManager"]->loadRole($_GET["role"]);
		  		$status=\holacracy\Project::getAllStatus();	
				displayProjects($role, $status);
			}
			if (isset($_GET["project"])) {
				$project=$_SESSION["currentManager"]->loadProjects($_GET["project"]);
				displayProject($project);
			}
		}
		exit;
	}
    	// ********************************************************************/
    	// *********** PROJETS                                     ************/
     	// ********************************************************************/
 
 	function displayProject($projet) {
    	$isRole=(isset( $_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole($projet->getRole()) || $_SESSION["currentUser"]->isAdmin($projet->getRole()->getSuperCircle())));
		

		
			// Affichage format projet
			echo "<div class='project_main' ";
			if (strlen($projet->getDescription())>110) echo "title=\"".str_replace("\"","&#34;","<b>".$projet->getTitle()."</b><br/>".$projet->getDescription())."\" ";
			echo ">";
			
			// Affiche la visibilité du projet
			
			echo "<div class='omo-project-visibility".$projet->getVisibility()."'";
			switch ($projet->getVisibility()) {
				case 1: echo " title=\"Visibilité: publique\"";
				case 2: echo " title=\"Visibilité: toute l'organisation\"";
				case 3: echo " title=\"Visibilité: limitée au cercle et à ses sous-cercles\"";
				case 4: echo " title=\"Visibilité: limitée au rôle\"";
				case 5: echo " title=\"Visibilité: uniquement vous\"";
			}
			echo "></div>";
			
			// Affichage de l'étoile des projets importants
			$il=$projet->getImportantList();
			// Aucune recommandation, affiche l'étoile vide
			if (count($il)<1) {
				echo "<div class='omo-project-important3'></div>";
			} else {
				$me=false;
				$txt="<i>Ce projet est important pour:</i>";
			// Si recommandation, établi la liste
				foreach ($il as $ii) {
					if ($ii->getId()==$_SESSION["currentUser"]->getId()) {
						$me=true;
					} else {
						$txt.="<div>".$ii->getFirstName()." ".$ii->getLastName()."</div>";
					}
				}	
					
			// Est-il défini comme important pour moi? Si oui, affiche l'étoile jaune
			if ($me) 
				echo "<div class='omo-project-important1' title='Ce projet est important pour moi'>";
			else 
			// Sinon, affiche l'étoile plus marquée, et indique qui considère ce projet comme important.
				echo "<div class='omo-project-important2'>";
			
			// Affiche le nombre de personnes
			if (count($il)>1 || !$me)
				echo "<span title='".str_replace("'","&#39;",$txt)."'>".count($il)."</span>";
			
			echo "</div>";
			}
			// Affichage du resultat attendu
			echo "<div class='project_title' proj_id='".$projet->getId()."'>";
			// Affichage de la description
			if (strlen($projet->getTitle())>110) {
				echo "<div title='".str_replace("'","&rsquo;",$projet->getTitle())."'>".substr($projet->getTitle(),0,100)."...</div>";
			} else {
				echo $projet->getTitle();
			}
			// Debug, affiche la position de l'objet (DDr - 26.1.2015)
			//echo "[".$projet->getPosition()."]";
			$actions=$projet->getActionsMoi(\holacracy\ActionMoi::CURRENT_ACTION | \holacracy\ActionMoi::BLOCKED_ACTION | \holacracy\ActionMoi::TRIGGER_ACTION);
			if (count($actions)>0) {
				
				echo " <span title='".count($actions)." actions à compléter'>(".count($actions).")</span>";
			}
			
			echo "</div>";
			// Affiche les dates
			if ($projet->getStatusDate()!="") {
				$time=(date_diff($projet->getStatusDate(), new DateTime())->format('%a'));
				echo "<div class='omo-projet-status'><i>".$projet->getStatus()." depuis <span>".($time=="0"?"aujourd'hui":($time=="1"?"hier":$time."j"))."</span></i></div>";
			} else {
				echo "<div class='omo-projet-status'><i>Créé le <span title='".(date_diff($projet->getCreationDate(), new DateTime())->format('%R%a jours'))."'>".$projet->getCreationDate()->format('d.m.y')."</span></i></div>";
			}
			// Affichage des infos complémentaires, comme ici la personne en charge
			echo "</div><div id='personneencharge'><span";
	
			if ($isRole && ($projet->getUserId()<1 || $projet->getUserId()==$_SESSION["currentUser"]->getId())) {
					echo " class='omo-me' ";
			}
	
			echo ">".($projet->getUserId()>0?$projet->getUser()->getUserName():"-")."</span>";
	
			// Menu si nécessaire, lorsqu'il est possible d'éditer
			if ($isRole && ($projet->getUserId()<1 || $projet->getUserId()==$_SESSION["currentUser"]->getId()) || (!$projet->getRole()->getUserId()>0 && $projet->getRole()->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId()) || $_SESSION["currentUser"]->isAdmin($projet->getRole()->getSuperCircle())) {
				echo "<div class='menuProjet'><div><button class='buttonProjet'></button></div><ul><li><a class='dialogPage' alt='".T_("Modifier un projet")." : ".$projet->getTitle()."' href='/ajax/project.php?action=FormAddProject&proj_id=".$projet->getId()."'>".T_("Editer")."</a></li>";
				if ($projet->getStatusId() == \holacracy\Project::FINISHED_PROJECT) echo "<li><a class='ajax' href='/ajax/project.php?action=ArchiveProject&proj_id=".$projet->getId()."'>".T_("Archiver")."</a></li>";
				echo "<li><a class='ajax' check='".T_("Voulez-vous effacer le projet")." ".$projet->getTitle()."?' href='/ajax/project.php?action=DeleteProject&proj_id=".$projet->getId()."'>".T_("Supprimer...")."</a></li></ul></div>";
			}
			
			echo "</div>";

	
	}
 
 	function displayProjects($role,$status) {
			// Défini si c'est le rôle en charge, ou le premier lien dans le cas d'un rôle non affecté, ou un administrateur
    		$isRole=(isset( $_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole($role) || (!$role->getUserId()>0 && $_SESSION["currentUser"]->getId()==$role->getSuperCircle()->getUserId()) || $_SESSION["currentUser"]->isAdmin($role->getSuperCircle())));
 
 
    		$projets=$role->getProjects(\holacracy\Project::ACTIVE_PROJECTS);
			
			$fillers=$role->getRoleFillers();
				if (count($fillers)>0) {//Si plusieurs focus
					$actions = $role->getActionsMoi($_SESSION["currentUser"]);
				} else {//Un seul filler
				$actions = $role->getActionsMoi();
				}
				
   			echo "<table style='width:100%;' class='containment-wrapper' cellspacing=0>";
  /*    		if (count($projets)==0 && (!$isRole || count($actions)==0)) {
     			echo "<tr><td colspan=4 class='code-empty'>";
     			    			echo "<tr>";				//Affiche l'ajout ou proposer projet
				 echo T_("Aucun projet...")."</td></tr>";
     		} else {*/
    			echo "<tr";
				if (count($projets)==0 && (!$isRole || count($actions)==0)) {echo " class='code-empty'";}

    			echo ">";
				
     			// Affiche le nombre de colonnes nécessaires
     			foreach ($status as $stat) {
		     		echo "<td class='project typr_".$stat->getId();
		     		echo "'>";
		     		
		     		if ($isRole && $stat->getId()==\holacracy\Project::CURRENT_PROJECT) {
//						if (count($actions)>0 ||  $_SESSION["currentUser"]->isRole($projet->getRole())) {
//						echo "<fieldset><legend><div id='mask1'></div><span>".T_("Actions")."</span><div id='mask2'></div></legend><div id='action-tab'>";
						
	
						if (count($actions)>0) {
							foreach ($actions as $action) {
								if($action->getStatusId() != 16){ //Si c'est pas une action terminé
								echo "<div class='action-list arrondi' style='' name='".$action->getId()."' ><input type='checkbox'/ style='vertical-align:-2px'> ".$action->getTitle()."</div>";
								} else{
								echo "<div class='action-list arrondi checkedimg' name='".$action->getId()."' > ".$action->getTitle()."</div>";
								}
							}
						}

		     		}
		     		
		     		
		    		$projets=$role->getProjects($stat->getId());
		    		if (count($projets)>0) {
		    			foreach($projets as $projet) {
	    					echo "<div ";
					
							echo " class='arrondi ".($isRole && ($projet->getUserId()<1 || $projet->getUserId()==$_SESSION["currentUser"]->getId()) || (!$projet->getRole()->getUserId()>0 && $projet->getRole()->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId()) || $_SESSION["currentUser"]->isAdmin($projet->getRole()->getSuperCircle())?"":"nodrag")."' id='proj_".$projet->getId()."'>";	
		    				displayProject($projet);
		    				echo "</div>"; 	
						}
					}
		     		echo "</td>";
	     		}
	     		echo "</tr>";
				 //}
				echo "</table><div style='text-align:right'>";

				//Affiche l'ajout ou proposer projet
				if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isMember($role->getSuperCircle()) || $_SESSION["currentUser"]->isAdmin($role->getSuperCircle()))) {
				if ($isRole || (!$role->getUserId()>0 && $role->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId())) { 
	    		$projets=$role->getProjects(\holacracy\Project::PROPOSED_PROJECT);
	    		if (count($projets)>0) {
	    			echo "<span ";
					if ($_SESSION["currentUser"]->isRole($role)) echo "class='omo-warning'>Vous avez "; else echo ">"; 
					echo "<a class='dialogPage' alt='Liste des projets à valider' href='ajax/project.php?action=List&filter=".\holacracy\Project::PROPOSED_PROJECT."&rId=".$role->getId()."'>".count($projets)." projet".(count($projets)>1?"s":"")." proposé".(count($projets)>1?"s":"")."</a> à valider</span>";
				}
	    		$projets2=$role->getProjects(\holacracy\Project::ARCHIVED_PROJECT);
	    		if (count($projets2)>0) {
	    			if (count($projets)>0) {echo " - ";}
	    			echo "<a  class='dialogPage' alt='Liste des projets archivés' href='ajax/project.php?action=List&filter=".\holacracy\Project::ARCHIVED_PROJECT."&rId=".$role->getId()."'>".count($projets2)." projet".(count($projets2)>1?"s</a> ont été archivés":"</a> a été archivé");
				}	

					echo " <a class='add_project dialogPage' href='/ajax/project.php?action=FormAddProject&role_id=".$role->getId()."' alt='".T_("Ajouter un projet")."'> ".T_("Ajouter un projet")."</a>";
					// echo " <a class='add_project dialogPage' href='/ajax/project.php?action=FormAddAction&role_id=".$role->getId()."' alt='".T_("Ajouter une action")."'> ".T_("Ajouter une action")."</a>";
				}
				else {
					echo " <a class='add_project dialogPage' href='/ajax/project.php?action=FormAddProject&role_id=".$role->getId()."' alt='".T_("Proposer un projet")."'> ".T_("Proposer un projet")."</a>";
				}
				

				}
      		echo "</div>";
      		echo "<script>";
      		echo " $('.add_project').button();";
      		echo "</script>";
 		}
 
   
    	// Charge les différents status possibles
    	$status=\holacracy\Project::getAllStatus();
    	
    	// Affichage du titre
		echo "<div id='test'><table style='width:100%;padding: 0em 2.2em 1em 2.2em;'><tr>";
		
		foreach ($status as $stat) {
     		echo "<td class='project_title typr_".$stat->getId()."'>";
     		echo "<div class='project_title_header'>".T_($stat->getLabel())."</div>";
     		echo "</td>";
   		}
   		echo "</tr></table></div><div id='omo-projectContent'>";
   		
    	// Affichage des projets de chaque rôles
    	$role=$this->_role;

    		$isRole=(isset( $_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole($role) || $_SESSION["currentUser"]->isAdmin($this->_role)));
    		echo "<div class='accordion'>"; 
    		echo "<h3>";
			echo "<span ";
			if (isset( $_SESSION["currentUser"]) && $_SESSION["currentUser"]->isRole($role)) {
					echo " class='omo-me' ";
			}
			echo "><b>".$role->getName()."</b>";
			
			

			
			
			
			echo "</span>";
			
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
					echo "<span title='".str_replace("'","&#39;",$role->getUser()->getFirstName()." ".$role->getUser()->getLastName())."'>".$role->getUser()->getUserName()." </span>";

					//for ($i=0;($i<count($roleFillers) && count($roleFillers)<=3) || ($i<2 && count($roleFillers)>3); $i++){
					//	if ($i>0) {echo " , ";}
					//	echo "<span title='".str_replace("'","&#39;",$roleFillers[$i]->getFirstName()." ".$roleFillers[$i]->getLastName())."'>".$roleFillers[$i]->getUserName()." </span>";
					//	$type = $role->getType();
					//	}
					//}
					$type = $role->getType();
					if ($type & \holacracy\Role::STANDARD_ROLE ) {
						if (count($roleFillers)>0) {
								$txt="";
								foreach ($roleFillers as $roleFiller)
								{
										$txt.="<div>".$roleFiller->getFirstName()." ".$roleFiller->getLastName()."</div>";
								}
								echo T_(" et ")."<span title='".str_replace("'","&#39;",$txt)."'>".(count($roleFillers)).T_(" autre(s) personne(s)")."</span>";
							}
					}
				}
			}
			else{  //Si on a pas de role affecté on affiche non affecté et le bouton pour affecter un membre du cercle
				// echo " (".$role->getSuperCircle()->getLeadLink()->getUser()->getUserName().")";
				if ($role->getSourceId()>0 ) {
					if ($role->getSource()->getUserId()>0) {
						print T_(" Energ&eacute;tis&eacute; par : ");
						echo "<span title='".str_replace("'","&#39;",$role->getSource()->getUser()->getFirstName()." ".$role->getSource()->getUser()->getLastName())."'>".$role->getSource()->getUser()->getUserName()." </span>";
					} else {
						print T_("Non affect&eacute;");
					}
				} else 
				if ($role->getSourceCircleId()>0) {
					if ($role->getSourceCircle()->getUserId()>0) {
						print T_(" Energ&eacute;tis&eacute; par : ");
						echo "<span title='".str_replace("'","&#39;",$role->getSourceCircle()->getUser()->getFirstName()." ".$role->getSourceCircle()->getUser()->getLastName())."'>".$role->getSourceCircle()->getUser()->getUserName()." </span>";
					} else {
						print T_("Non affect&eacute;");
					}
				} else {
				$type = $role->getType();
				if ($type & \holacracy\Role::STRUCTURAL_ROLES) {
					print T_("Non affect&eacute;");
				} else {
					print T_("Affect&eacute; par d&eacute;faut au Premier Lien");
				}
			}
			}
			$type = $role->getType();

			echo "</span>";
			
			echo "</h3>";
			
			echo "</h3>";
				echo "<div class='code-project-content'>";

					// Si c'est un premier lien
			if ($type & \holacracy\Role::LEAD_LINK_ROLE) {
				// Affiche les projets du cercle supérieur
				$circle=$role->getSuperCircle();
				echo "<div id='project_role_".$circle->getId()."' role='".($isRole || (!$role->getUserId()>0 && $role->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId())?"yes":"no")."'>";
				
				displayProjects($circle,$status);
				echo "</div><hr>";
				
			} 
			echo "<div id='project_role_".$role->getId()."' role='".($isRole || (!$role->getUserId()>0 && $role->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId())?"yes":"no")."'>";
			
			displayProjects($role,$status);
			echo "</div></div>";
		  
			echo "</div>";
  		
    
		echo "</div>";
    
    ?>
