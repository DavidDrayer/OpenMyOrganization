<?php

	if (isset($_GET["action"])) {
	
		include_once("../../../include.php");

		  	
		if ($_GET["action"]=="refresh") {
			if (isset($_GET["role"])) {
				$role=$_SESSION["currentManager"]->loadRole($_GET["role"]);
				$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["meeting"]);
		  		$status=\holacracy\Project::getAllStatus();	
				displayProjects($role, $status, $meeting);
			}
			if (isset($_GET["project"])) {
				$project=$_SESSION["currentManager"]->loadProjects($_GET["project"]);
				$meeting=$_SESSION["currentManager"]->loadMeeting($_GET["meeting"]);
				displayProject($project, $meeting);
			}
		}
		exit;
	}
    	// ********************************************************************/
    	// *********** PROJETS                                     ************/
     	// ********************************************************************/
 
 	function displayProject($projet, $meeting) {
    	$isRole=(isset( $_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole($projet->getRole()) || $_SESSION["currentUser"]->isAdmin($projet->getRole()->getSuperCircle())));
		$isSecretary=($_SESSION["currentUser"]->getId()>1 && $meeting->getSecretaryId()==$_SESSION["currentUser"]->getId());
		// Le meeting est-il en cours?
		$isInProcess=($meeting->getOpeningTime()!=null && $meeting->getClosingTime()==null);

		
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

	
			echo ">".($projet->getUserId()>0?$projet->getUser()->getUserName():"-")."</span>";
	
			// Menu si nécessaire, lorsqu'il est possible d'éditer
			if ($isSecretary && $isInProcess) {
				echo "<div class='menuProjet'><div><button class='buttonProjet'></button></div><ul><li><a class='dialogPage' alt='".T_("Modifier un projet")." : ".$projet->getTitle()."' href='/ajax/project.php?action=FormAddProject&proj_id=".$projet->getId()."'>".T_("Editer")."</a></li>";
				if ($projet->getStatusId() == \holacracy\Project::FINISHED_PROJECT) echo "<li><a class='ajax' href='/ajax/project.php?action=ArchiveProject&proj_id=".$projet->getId()."'>".T_("Archiver")."</a></li>";
				echo "<li><a class='ajax' check='".T_("Voulez-vous effacer le projet")." ".$projet->getTitle()."?' href='/ajax/project.php?action=DeleteProject&proj_id=".$projet->getId()."'>".T_("Supprimer...")."</a></li></ul></div>";
			}
			
			echo "</div>";

	
	}
 
	function displayActions ($actions) {
		if (count($actions)>0) {
			foreach ($actions as $action) {

				// Récapitulatif de l'action
				$title="";

				// Y a-til un proposeur défini?
				if ($action->getProposerId()>0) {
					// Est-ce moi?
					if ($action->getProposerId()==$_SESSION["currentUser"]->getId()) {
						$title.="Créé le ".$action->getCreationDate()->format("d.m.Y"). " par moi-même";
					} else {
						// Personne + rôle
						if ($action->getProposerRoleId()>0) {
							$title.="Demandé par ".$action->getProposer()->getUserName()." dans le rôle [".$action->getProposerRole()->getName()."] le ".$action->getCreationDate()->format("d.m.Y");
						} else {
							$title.="Demandé par ".$action->getProposer()->getUserName()." le ".$action->getCreationDate()->format("d.m.Y");
						}
					}
				} else {
					// Créé dans un rôle?
					if ($action->getProposerRoleId()>0) {
						$title.="Créé dans le rôle [".$action->getProposerRole()->getName()."] le ".$action->getCreationDate()->format("d.m.Y");
					} else {
						// Si proposer ni rôle, affiche la date de création
						$title.="Créé le ".$action->getCreationDate()->format("d.m.Y");
					}
				}
				
				// Liste des destinataires
				if (count($action->getCheckList())>0) {
					$title.= "<br/>";
					$title.= "Pour ";
					$i=0;
					foreach ($action->getCheckList() as $check) {
						if ($i>0) $title.= ", ";
						if ($check->isCheck()) {$title.=  "<b>";}
						if ($check->getUserId()>0) {
						if ($check->getUserId()==$_SESSION["currentUser"]->getId()) {
							$title.= "moi-même";
						} else {
							$title.= $check->getUser()->getUserName();
						}
						} else {$title.= "<i>inconnu</i>";}
						if ($check->isCheck()) {$title.=  "</b>";}
						$i++;
					}
				} 
						
				// Défini les options qui sont indépendantes de l'affichage
				$option="";
				// Si je suis auteur ou s'il n'y a pas d'auteur et que j'en suis le destinataire et que l'action est checkée, alors je peux effacer 
				if ($_SESSION["currentUser"]->getId()==$action->getProposerId() || !$action->getProposerId()>0) {
					$option.="<a href='/ajax/deleteaction.php?id=".$action->getId()."' alt='Supprimer' check='Êtes-vous sûr de vouloir supprimer cette action?' class='omo-delete ajax'></a>";
				}	 
				$option="<div style='float:right'>".$option."</div>";
			
						
				// Action pour moi ou pas?
				if ($action->isForUser($_SESSION["currentUser"])) {
					// Action checkée?
					if(!$action->isCheck($_SESSION["currentUser"])) {
						// Affichage standard
						echo "<div id='acti_".$action->getId()."' class='action-list arrondi' name='".$action->getId()."' ><input type='checkbox'/ style='vertical-align:-2px'> <a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
					} else {
						// En attente d'autres personnes?
						if($action->isCheck()) {
							// Proposé par moi ou l'un de mes rôle
							if ($_SESSION["currentUser"]->getId()==$action->getProposerId()) {
								// Affichage en gris
								echo "<div id='acti_".$action->getId()."' class='action-list arrondi checkedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
								
							} else {
								// Depuis combien de temps c'est checké
								if ($action->getCheckDate()->diff(new DateTime("now"))->format("%a")<1) {
									// Affichage en gris
									echo "<div id='acti_".$action->getId()."' class='action-list arrondi checkedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
								} 
							}
						} else {
							// Affichage jaune
							echo "<div id='acti_".$action->getId()."' class='action-list arrondi partcheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()." </a> (".$action->getCheckCount()."/".count($action->getCheckList()).")".$option."</div>";
						}
					}
				} else {
					// Proposé par moi ou l'un de mes rôle 
					if ($_SESSION["currentUser"]->getId()==$action->getProposerId()) {
						// Checké?
						if($action->isCheck()) {
							// Affichage en gris
							echo "<div id='acti_".$action->getId()."' class='action-list arrondi checkedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
						} else {
							if (count($action->getCheckList())==1) {
								echo "<div id='acti_".$action->getId()."' class='action-list arrondi nocheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
							
							}
							// Au moins une personne?
							if ($action->getCheckCount()>0) {
								// Affichage en noir avec V jaune	
								echo "<div id='acti_".$action->getId()."' class='action-list arrondi partcheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a> (".$action->getCheckCount()."/".count($action->getCheckList()).")".$option."</div>";
							} else {
								// Affichage noir avec X en rouge
								echo "<div id='acti_".$action->getId()."' class='action-list arrondi nocheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a> (".$action->getCheckCount()."/".count($action->getCheckList()).")".$option."</div>";
							}
						}
					} 
				}
			}
		}	
					
	
		}
 
 	function displayProjects($role,$status,$meeting) {
			// Défini si c'est le rôle en charge, ou le premier lien dans le cas d'un rôle non affecté, ou un administrateur
    		$isRole=(isset( $_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole($role) || (!$role->getUserId()>0 && $_SESSION["currentUser"]->getId()==$role->getSuperCircle()->getUserId()) || $_SESSION["currentUser"]->isAdmin($role->getSuperCircle())));
			$isSecretary=($_SESSION["currentUser"]->getId()>1 && $meeting->getSecretaryId()==$_SESSION["currentUser"]->getId());
			// Le meeting est-il en cours?
			$isInProcess=($meeting->getOpeningTime()!=null && $meeting->getClosingTime()==null);

 
    		$projets=$role->getProjects(\holacracy\Project::ACTIVE_PROJECTS);

			
   			echo "<table style='width:100%;' class='containment-wrapper' cellspacing=0>";

    			echo "<tr";
				if (count($projets)==0) {echo " class='code-empty'";}

    			echo ">";
		    	// Compteur pour les projets non affichés
		    	$nbPasAffiches=0;				
     			// Affiche le nombre de colonnes nécessaires
     			foreach ($status as $stat) {
					if ($stat->getId()!=8) {
		     		echo "<td class='project typr_".$stat->getId();
		     		echo "'>";
		     		
 		
		     		
		    		$projets=$role->getProjects($stat->getId());

		    		if (count($projets)>0) {
		    			foreach($projets as $projet) {
	    					
	    					// Lit la liste des recommandation comme important
							$il=$projet->getImportantList();
							if ($projet->getVisibility()<4)
	    					if (count($il)>0) {
								echo "<div ";
						
								// anciennement: echo " class='arrondi ".($isRole && ($projet->getUserId()<1 || $projet->getUserId()==$_SESSION["currentUser"]->getId()) || (!$projet->getRole()->getUserId()>0 && $projet->getRole()->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId()) || $_SESSION["currentUser"]->isAdmin($projet->getRole()->getSuperCircle())?"":"nodrag")."' id='proj_".$projet->getId()."'>";	
								echo " class='arrondi ".($isSecretary && $isInProcess?"":"nodrag")."' id='proj_".$projet->getId()."'>";	
								displayProject($projet, $meeting);
								echo "</div>"; 	
							} 
						}
											// Affichage du nombre de projets qui n'ont pas été affichés (pour info, avec lien sur la page)

					}
		     		echo "</td>";
	     		}}
	     		echo "</tr>";
				 //}
				echo "</table><div style='text-align:right'>";


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
			if ($stat->getId()!=8) {
     		echo "<td class='project_title typr_".$stat->getId()."'>";
     		echo "<div class='project_title_header'>".T_($stat->getLabel())."</div>";
     		echo "</td>";}
   		}
   		echo "</tr></table></div><div id='omo-projectContent'>";

		
   		
   		
    	// Affichage des projets de chaque rôles
    	$roles=$this->_meeting->getCircle()->getRoles();
    	// Pour chacun des rôles, charge les différents projets dans un accordéon
    	foreach($roles as $role) {
			// A attribuer au secrétaire pour ordonner les projets
    		$isRole=false;
    		echo "<div class='accordion'>";
    		echo "<h3>";
			echo "<span ";

			echo "><b>";
			if (get_class($role) == "holacracy\\Circle") {
				echo "<a class='ui-icon-circle-plus' href='role.php?id=".$role->getLeadLink()->getId()."#tabs-6' title='".T_("Afficher les projets du cercle")."'>".$role->getName()."</a>";
			}
			if (get_class($role) == "holacracy\\Role") {
				echo "<a class='ui-icon-circle-plus' href='role.php?id=".$role->getId()."#tabs-6' title='".T_("Afficher les projets du role")."'>".$role->getName()."</a>";
			}	
			echo "</b>";
			
			

			
			
			
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
				echo "<div class='code-project-content'>";

					// Si c'est un premier lien
			if ($type & \holacracy\Role::LEAD_LINK_ROLE) {
				// Affiche les projets du cercle supérieur
				$circle=$role->getSuperCircle();
				echo "<div id='project_role_".$circle->getId()."' role='".($isSecretary && $isInProcess?"yes":"no")."'>";
				
				displayProjects($circle,$status,$meeting);
				echo "</div><hr>";
				
			} 
			echo "<div id='project_role_".$role->getId()."' role='".($isSecretary && $isInProcess?"yes":"no")."'>";
			
			displayProjects($role,$status,$meeting);
			echo "</div></div>";
		  
			echo "</div>";
  		}
    
		echo "</div>";
    
    ?>
