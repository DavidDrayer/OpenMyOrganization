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

			// Si projet actif, affiche les actions
			if ($projet->getStatusId()==1) {
				$actions=$projet->getActions();
				$actions2=$projet->getActions(\holacracy\Action::UNCHECKED_ACTION);
				if (count($actions2)>0) {
					
					echo " <span class='project_nb_actions' title='".count($actions)." actions à compléter'>".count($actions2)."/".count($actions)."</span>";
				} else {
					// Problème, pas d'action en cours, affichage en rouge
					echo " <span class='project_nb_actions_red'  title='".count($actions)." actions à compléter'>".count($actions2)."/".count($actions)."</span>";
				}
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
 
	function displayActions ($actions) {
		if (count($actions)>0) {
			foreach ($actions as $action) {

				// Récapitulatif de l'action
				$title="";

				// Y a-til un proposeur défini?
				if ($action->getProposerId()>0) {
					// Est-ce moi?
					if ($action->getProposerId()==$_SESSION["currentUser"]->getId()) {
						$title.="Créé le ".$action->getCreationDate()->format("d.m.Y");
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
				if (count($action->getCheckList())>1) {
					$title.= "<br/><br/>";
					$title.= "Pour ";
					$i=0;
					foreach ($action->getCheckList() as $check) {
						if ($i>0) $title.= ", ";
						if ($check->isCheck()) {$title.=  "<b>";}
						$title.= $check->getUser()->getUserName();
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
						echo "<!--1--><div id='acti_".$action->getId()."' class='action-list arrondi' name='".$action->getId()."' ><input type='checkbox'/ style='vertical-align:-2px'> <a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
					} else {
						// En attente d'autres personnes?
						if($action->isCheck()) {
							// Proposé par moi ou l'un de mes rôle
							if ($_SESSION["currentUser"]->getId()==$action->getProposerId()) {
								// Affichage en gris
								echo "<!--2--><div id='acti_".$action->getId()."' class='action-list arrondi checkedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
								
							} else {
								// Depuis combien de temps c'est checké
								if ($action->getCheckDate()->diff(new DateTime("now"))->format("%d")<1) {
									// Affichage en gris
									echo "<!--3--><div id='acti_".$action->getId()."' class='action-list arrondi checkedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
								} 
							}
						} else {
							// Affichage jaune
							echo "<!--4--><div id='acti_".$action->getId()."' class='action-list arrondi partcheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()." </a> (".$action->getCheckCount()."/".count($action->getCheckList()).")".$option."</div>";
						}
					}
				} else {
					// Proposé par moi ou l'un de mes rôle 
					if ($_SESSION["currentUser"]->getId()==$action->getProposerId()) {
						// Checké?
						if($action->isCheck()) {
							// Affichage en gris
							echo "<!--5--><div id='acti_".$action->getId()."' class='action-list arrondi checkedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
						} else {
							// Au moins une personne?
							if (count($action->getCheckList())>0) {
								// Affichage en noir avec V jaune	
								echo "<!--6--><div id='acti_".$action->getId()."' class='action-list arrondi partcheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a> (".$action->getCheckCount()."/".count($action->getCheckList()).")".$option."</div>";
							} else {
								// Affichage noir avec X en rouge
								echo "<!--7--><div id='acti_".$action->getId()."' class='action-list arrondi nocheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a> (".$action->getCheckCount()."/".count($action->getCheckList()).")".$option."</div>";
							}
						}
					} 
				}
			}
		}	
					
	
		}
 
 	function displayProjects($role,$status) {
			// Défini si c'est le rôle en charge, ou le premier lien dans le cas d'un rôle non affecté, ou un administrateur
    		$isRole=(isset( $_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole($role) || (!$role->getUserId()>0 && $_SESSION["currentUser"]->getId()==$role->getSuperCircle()->getUserId()) || $_SESSION["currentUser"]->isAdmin($role->getSuperCircle())));
 
 
    		$projets=$role->getProjects(\holacracy\Project::ACTIVE_PROJECTS);
			$actions = $role->getActions();
			displayActions($actions);

			
   			echo "<table style='width:100%;' class='containment-wrapper' cellspacing=0>";

    			echo "<tr";
				if (count($projets)==0 && (!$isRole || count($actions)==0)) {echo " class='code-empty'";}

    			echo ">";
		    	// Compteur pour les projets non affichés
		    	$nbPasAffiches=0;				
     			// Affiche le nombre de colonnes nécessaires
     			foreach ($status as $stat) {
		     		echo "<td class='project typr_".$stat->getId();
		     		echo "'>";
		     		
 		
		     		
		    		$projets=$role->getProjects($stat->getId());

		    		if (count($projets)>0) {
		    			foreach($projets as $projet) {
	    					
	    					// Lit la liste des recommandation comme important
							if ($projet->getVisibility()<4)

								echo "<div ";
						
								echo " class='arrondi ".($isRole && ($projet->getUserId()<1 || $projet->getUserId()==$_SESSION["currentUser"]->getId()|| $projet->getRole()->getUserId()==$_SESSION["currentUser"]->getId()) || $_SESSION["currentUser"]->isAdmin($projet->getRole()->getSuperCircle())?"":"nodrag")."' id='proj_".$projet->getId()."'>";	
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
	
						// Liste des projets proposés (Est-ce utile dans cette vue? )
						$projets=$role->getProjects(\holacracy\Project::PROPOSED_PROJECT);
						if (count($projets)>0) {
							if ($nbPasAffiches>0) {echo " - ";}
							echo "<span ";
							if ($_SESSION["currentUser"]->isRole($role)) echo "class='omo-warning'>Vous avez "; else echo ">"; 
							echo "<a class='dialogPage' alt='Liste des projets à valider' href='ajax/project.php?action=List&filter=".\holacracy\Project::PROPOSED_PROJECT."&rId=".$role->getId()."'>".count($projets)." projet".(count($projets)>1?"s":"")." proposé".(count($projets)>1?"s":"")."</a> à valider</span>";
						}
						// Liste des projets archivés (Est-ce utile dans cette vue?)
						$projets2=$role->getProjects(\holacracy\Project::ARCHIVED_PROJECT);
						if (count($projets2)>0) {
							if (count($projets)>0 || (count($projets)==0 && $nbPasAffiches>0)) {echo " - ";}
							echo "<a  class='add_project dialogPage archive_image' alt='Liste des projets archivés' href='ajax/project.php?action=List&filter=".\holacracy\Project::ARCHIVED_PROJECT."&rId=".$role->getId()."'><span class='archive_count'>".count($projets2)."</span></a>";;
						}	
									
					if ($isRole || (!$role->getUserId()>0 && $role->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId())) { 
					
					// Si c'est le rôle qui gère le projet
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
    	echo "<div style='position:absolute; z-index:99; left:6px; right:15px; top:48px; background:#FFFFFF; border-bottom:1px solid #CCCCCC; padding:5px; height:30px;'>";

		echo "<table style='width:100%;padding: 0em 2.2em 1em 2.2em;'><tr>";
		
		foreach ($status as $stat) {
     		echo "<td class='project_title typr_".$stat->getId()."'>";
     		echo "<div class='project_title_header'>".T_($stat->getLabel())."</div>";
     		echo "</td>";
   		}
   		echo "</tr></table>";
   		echo "</div>";
   		
   		echo "<div id='omo-projectContent' style='padding-top:28px;'>";
   		
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
