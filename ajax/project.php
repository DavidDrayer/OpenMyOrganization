<?
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once("../include.php");


	function displayActions ($actions, $projet) {
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
						
				// Si je suis en charge du projet 
				$option="";
				if ($_SESSION["currentUser"]->getId()==$projet->getUserId()) {
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
								} else {
									// Affichage en gris, version passée
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
					//if ($_SESSION["currentUser"]->getId()==$action->getProposerId()) {
						// Checké?
						if($action->isCheck()) {
							// Affichage en gris
							echo "<div id='acti_".$action->getId()."' class='action-list arrondi checkedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
						} else {
							// Au moins une personne?
							if ($action->getCheckCount()>0) {
								// Affichage en noir avec V jaune	
								echo "<div id='acti_".$action->getId()."' class='action-list arrondi partcheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a> (".$action->getCheckCount()."/".count($action->getCheckList()).")".$option."</div>";
							} else {
								// Affichage noir avec X en rouge
								echo "<div id='acti_".$action->getId()."' class='action-list arrondi nocheckedimg' name='".$action->getId()."' ><a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a> (".$action->getCheckCount()."/".count($action->getCheckList()).")".$option."</div>";
							}
						}
					//} 
				}
			}
		}	
					
	
		}
 	
	// ********************************************************************
	// ********* Zone POST : formulaire envoyé et données à manipuler  ****
	// ********************************************************************
	
	
	// Post d'un formulaire de création/modification de projets
	if (isset($_POST["acti_title"])) {
		// Vérification du contenu des champs du formulaire
		if ($_POST["acti_title"]=="") {echo "<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />\nVeuillez compléter le descriptif de l'action"; exit;}

		// Sauve le projet
		if (!isset($_POST["acti_id"]) || $_POST["acti_id"]=="") {
			$action=new \holacracy\Action();
		} else {
			$action=$_SESSION["currentManager"]->loadActions($_POST["acti_id"]);
		}
		$action->setTitle(utf8_decode($_POST["acti_title"]));
		// Défini qui a proposé le projet
		if (isset($_POST["user_id_proposer"])) {
			$action->setProposer($_POST["user_id_proposer"],$_POST["role_id_proposer"]);
		}
		
		$action->setRole($_POST["role_id"]);
		if (isset ($_POST["user_id"]) && $_POST["user_id"]!="") $action->addUser($_POST["user_id"]);

		$returnidaction = $_SESSION["currentManager"]->save($action);
		
		echo "ok"; exit;


		
	} else 
	if (isset($_POST["proj_title"])) {
		// Vérification du contenu des champs du formulaire
		if ($_POST["proj_title"]=="") {echo "<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />\nVeuillez compléter le résultat attendu"; exit;}
		// ---- Actuellement facultatif ---- //
		//if ($_POST["proj_description"]=="") {echo "<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />\nVeuillez compléter la description du projet"; exit;}
		
		// Sauve le projet
		if (!isset($_POST["proj_id"]) || $_POST["proj_id"]=="") {
			$projet=new \holacracy\Project();
		} else {
			$projet=$_SESSION["currentManager"]->loadProjects($_POST["proj_id"]);
		}
		$projet->setTitle(utf8_decode($_POST["proj_title"]));
		$projet->setDescription(utf8_decode($_POST["proj_description"]));
		// Défini la visibilité du projet
		$projet->setVisibility($_POST["proj_visibility"]);
		// Défini qui a proposé le projet
		if (isset($_POST["user_id_proposer"])) {
			$projet->setProposer($_POST["user_id_proposer"],$_POST["role_id_proposer"]);
		}
		
		$projet->setRole($_POST["role_id"]);
		if (isset ($_POST["user_id"]) && $_POST["user_id"]!="") $projet->setUser($_POST["user_id"]);
		if (isset ($_POST["prst_id"]) && $_POST["prst_id"]!="") {
			$projet->setStatus($_POST["prst_id"]);
		} else {
			$projet->setStatus(\holacracy\Project::PROPOSED_PROJECT);
			$projet->setStatusDate(new DateTime());
		}

		$returnidprojet = $_SESSION["currentManager"]->save($projet);
		
		// Si le projet est important, lui rajoute une étoile
		if (isset($_POST['proj_important']) && $_POST['proj_important']) {
			$projet->setImportant(true,$_POST["user_id_proposer"]);
		}
		
		//si le status est vide, c'est un projet proposé. On envoi la notification email pour la suggestion de projet
		if ((empty ($_POST["prst_id"]) || $_POST["prst_id"]=="") && $_POST["role_id"]!="") {
		
		$role=$projet->getRole();
		$fillers=$role->getRoleFillers();
		$circle = $role->getSuperCircleId();
		$urlok = "http://".$_SERVER["HTTP_HOST"]."/ajax/projectvalid.php?circle=".$circle."&id=".$projet->getId()."&check=true";
		$urlnok = "http://".$_SERVER["HTTP_HOST"]."/ajax/projectvalid.php?circle=".$circle."&id=".$projet->getId()."&check=false";

		$prenom = $_SESSION["currentUser"]->getFirstName();
		$nom = $_SESSION["currentUser"]->getLastName();	

		$message = "Vous avez recu une nouvelle suggestion de projet de la part de <b>".$prenom." ".$nom."</b> pour le rôle <b>".$role->getName()."</b> qui vous est affecté.<br/><h3>".$projet->getTitle()."</h3><i>".$projet->getDescription()."</i><br/><br/>Pour valider ce projet <a href='".$urlok."'>cliquez ici</a> <br/><br/>Pour refuser ce projet <a href='".$urlnok."'>cliquez ici</a>";
		$subject = "Nouvelle proposition de projet";
		
		// Différents cas d'envois: - DDr, 4.9.2014
		if (isset($_POST["user_id"])&& $_POST["user_id"]!="") {
			// On a sélectionné le destinataire- DDr, 4.9.2014
			$user=$_SESSION["currentManager"]->loadUser($_POST["user_id"]);
			$user->sendMessage($subject,$message);
		} else {
			// Envoie au 1er lien du cercle
			if ($role->getSuperCircle()->getUserId()>0) {
				$message = "Un nouveau projet a été proposé par <b>".$prenom." ".$nom."</b> pour le rôle <b>".$role->getName()."</b> qui vous est affecté par défaut.<br/><h3>".$projet->getTitle()."</h3><i>".$projet->getDescription()."</i><br/><br/>Pour valider ce projet <a href='".$urlok."'>cliquez ici</a> <br/><br/>Pour refuser ce projet <a href='".$urlnok."'>cliquez ici</a>";
				$role->getSuperCircle()->getUser()->sendMessage($subject,$message);
			}

		}
		
		
		}
		
		
		echo "ok"; exit;
	} else
	// Traite les post AJAX pour ce qui concerne le changement de status des projets
	if (isset($_POST["action"])) {
		if ($_POST["action"]=="SetNoImportant") {
			$projet=$_SESSION["currentManager"]->loadProjects($_POST["proj_id"]);
			$projet->setImportant(false,$_SESSION["currentUser"]);
		}
		if ($_POST["action"]=="SetImportant") {
			$projet=$_SESSION["currentManager"]->loadProjects($_POST["proj_id"]);
			$projet->setImportant(true,$_SESSION["currentUser"]);
		}
		if ($_POST["action"]=="ChangeStatus") {
			// Charge le projet
			$projet=$_SESSION["currentManager"]->loadProjects($_POST["proj_id"]);
			//if ($projet->getStatusId()!=$_POST["typr_id"] || $projet->getPosition()!=$_POST["proj_position"] || $projet->getRoleId()!=$_POST["role_id"]) {
				// La position a-t-elle changé? Si oui, modifie la position de tous les projets dans la colonne de destination
				if (isset($_POST["role_id"])) {
					$role=$_SESSION["currentManager"]->loadRole($_POST["role_id"]);
				} else {
					$role=$projet->getRole();
				}
				$projects=$role->getProjects();
				$pos=0;
				if (isset($_POST["proj_position"])) $newpos=$_POST["proj_position"]; else $newpos=99;
				foreach ($projects as $proj) {

					// Est-ce un projet dans la même colonne de destination?
					if ($proj->getStatusId()==$_POST["typr_id"] && $proj->getId()!=$projet->getId()) {


						if ($pos<$newpos) {
								$proj->setPosition($pos);
						} else {
							$proj->setPosition($pos+1);
						}
						$_SESSION["currentManager"]->save($proj);
						$pos+=1;
					}
				}

				if ($projet->getStatusId()!=$_POST["typr_id"] ||  $projet->getRoleId()!=$_POST["role_id"]) {
					$projet->setStatusDate(new DateTime());				
					$projet->setStatus($_POST["typr_id"]);
				}
				if (isset($_POST["role_id"])) $projet->setRoleId($_POST["role_id"]);
				$projet->setPosition($newpos);

				$_SESSION["currentManager"]->save($projet);
			
			//echo $_POST["proj_id"]." ".$_POST["typr_id"];
		}
		
		if ($_POST["action"]=="TransferProject") {
			// Charge le projet
			$projet=$_SESSION["currentManager"]->loadProjects($_POST["proj_id"]);
			echo ($projet->getPosition()." ".$_POST["proj_position"]);
			if ($projet->getStatusId()!=$_POST["typr_id"] || $projet->getPosition()!=$_POST["proj_position"] || $projet->getRoleId()!=$_POST["role_id"]) {

				$projet->setStatus(16);
				$projet->setRoleId($_POST["role_id"]);
				$projet->setPosition($_POST["proj_position"]);
				$projet->setStatusDate(new DateTime());
				$_SESSION["currentManager"]->save($projet);
			}
			//echo $_POST["proj_id"]." ".$_POST["typr_id"];
		}
		
		if ($_POST["action"]=="addAction") {
			// Ajoute l'action dans la DB
			$action=new \holacracy\Action;
			$action->setTitle(utf8_decode($_POST["actionString"]));
			$action->setProject($_POST["proj_id"]);
			$_SESSION["currentManager"]->save($action);
			// Affiche le résultat
			$option="<a href='/ajax/deleteaction.php?id=".$action->getId()."' alt='Supprimer' check='Êtes-vous sûr de vouloir supprimer cette action?' class='omo-delete ajax'></a>";	 
			$option="<div style='float:right'>".$option."</div>";
			$title="";

			echo "<div id='acti_".$action->getId()."' class='action-list arrondi' name='".$action->getId()."' ><input type='checkbox'/ style='vertical-align:-2px'> <a href='#' onclick='return false;' title='".$title."'>".$action->getTitle()."</a>".$option."</div>";
		}
		
		if ($_POST["action"]=="addComment") {
			// Ajoute le commentaire dans la DB
			$comment=new \holacracy\Comment;
			$comment->setDescription(utf8_decode($_POST["comment"]));
			
			// Gestion du temps, pas disponible dans la version 8
	/*		if (isset($_POST["comm_tt"]) && $_POST["comm_tt"]>0) {
				$comment->setTT($_POST["comm_tt"]);
				$comment->setTTUnite($_POST["comm_tt_unite"]);
			}
			if (isset($_POST["comm_tr"]) && $_POST["comm_tr"]>0) {
				$comment->setTR($_POST["comm_tr"]);
				$comment->setTRUnite($_POST["comm_tr_unite"]);
			}*/
			$comment->setProjectId($_POST["proj_id"]);
			$_SESSION["currentManager"]->save($comment);
			// Affiche le résultat
			echo $comment->getDescription();	
			echo "<div class='omo-signature'>Créé par ".$_SESSION["currentUser"]->getUserName()." le ".date("d.m.Y");
			echo "</div><hr>";		
		}
		
		
	// ***************************************************************
	// *********** Zone GET : Affichage de formulaires      **********
	// ***************************************************************	
		
	} else 	if (isset($_GET["action"])) {
	
		if ($_GET["action"]=="deleteAction") {
			// Anciennes ou nouvelles actions?
			if (is_numeric($_GET["id"])) {
				$action=$_SESSION["currentManager"]->loadActions($_GET["id"]);
				$action->check($_SESSION["currentUser"]);
				$_SESSION["currentManager"]->save($action,true);
			} else {
				$action=$_SESSION["currentManager"]->loadActionsMoi($_GET["id"]);
				$action->setStatus(16); //terminée
				$action->setTimeStampDelete();
				$_SESSION["currentManager"]->save($action);
			}
			
		}
		
		if ($_GET["action"]=="restoreAction") {
			$action=$_SESSION["currentManager"]->loadActionsMoi($_GET["id"]);
			$action->setStatus(1); //en cours
			$action->setTimeStampDelete();
			$_SESSION["currentManager"]->save($action);
		}
		
		if ($_GET["action"]=="List") {
		
			// Affiche la liste des projets
			$role=$_SESSION["currentManager"]->loadRole($_GET["rId"]);
			$projects=$role->getProjects($_GET["filter"]);
			
			// Classe par date de fin
			
			
			foreach ($projects as $project) {
			
				echo "<div>".($project->getStatusDate()!=""?$project->getStatusDate()->format("d.m.Y"):"<i>indéfini</i>")." : <a class='dialogPage' href='/ajax/project.php?action=FormViewProject&proj_id=".$project->getId()."&back=".urlencode($_SERVER["REQUEST_URI"])."'>".$project->getTitle()."</a></div>";
			
				// Si je suis responsable de ce rôle, 
				if ($_SESSION["currentUser"]->isRole($role)) {
					//affiche de quoi l'accepter
					
				}
			}
			?>
			<script>
			$( "#dialogStd" ).dialog({ buttons: [ { text: "Fermer", click: function() { $( this ).dialog( "close" ); } } ] });
			</script>
			<?
		
		
		} else	
		if ($_GET["action"]=="ArchiveProject") {
				if (isset($_GET["proj_id"])) {
					$projet=$_SESSION["currentManager"]->loadProjects($_GET["proj_id"]);
					$projet->setStatus(\holacracy\Project::ARCHIVED_PROJECT);
					$projet->setStatusDate(new DateTime());
					$_SESSION["currentManager"]->save($projet);
					
					$actions=$projet->getActionsMoi ();
					foreach($actions as $action){
					$_SESSION["currentManager"]->delete($action);
					}
					
					echo "refreshProjects(".$projet->getRoleId().");";
				}
		} else
		if ($_GET["action"]=="DeleteProject") {
				if (isset($_GET["proj_id"])) {
					$projet=$_SESSION["currentManager"]->loadProjects($_GET["proj_id"]);
					$_SESSION["currentManager"]->delete($projet);
					echo "deleteProject(".$projet->getId().");";
				}
		} else
		if ($_GET["action"]=="FormViewProject") {
			if (isset($_GET["proj_id"])) {
					$projet=$_SESSION["currentManager"]->loadProjects($_GET["proj_id"]);
					
					// Défini si l'utilisateur courant a les droits d'édition sur le projet courant
					$canEdit=($projet->getUserId()==$_SESSION["currentUser"]->getId() || (!$projet->getUserId()>0 && $_SESSION["currentUser"]->isRole($projet->getRole())) || (!$projet->getRole()->getUserId()>0 && $projet->getRole()->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId()) || $_SESSION["currentUser"]->isAdmin($projet->getRole()->getSuperCircle()));
					
					echo "<fieldset><legend><div id='mask1'></div><span>".T_("R&eacute;sultat attendu")."</span><div id='mask2'></div></legend>";
					echo $projet->getTitle();
					if ($projet->getProposerId()>0) {
						echo "<div style='text-align:right; color:#999999; font-size:smaller'><i>Proposé par <b>".$projet->getProposer()->getUserName()."</b>";
						if ($projet->getProposerRoleId()>0) {
							if ($projet->getProposer()->isRole($projet->getProposerRole())) {
								echo " dans son rôle ";
							} else {
								echo " dans son ancien rôle ";
							}
							echo "<b>".$projet->getProposerRole()->getName()."</b></div>";
						}	
						echo "</i></div>";				
					}
					echo "</fieldset>";	
					if (trim($projet->getDescription())!="") {				
						echo "<fieldset><legend><div id='mask1'></div><span>".T_("Informations compl&eacute;mentaires")."</span><div id='mask2'></div></legend>";
						// Affichage des statistiques temporelles sur le projet
						//echo "<div>Temps estimé: 3 jours - Temps travaillé: 3 jours, 4 heures et 45 minutes - Temps restant: 2 heures</div>";
						echo $projet->getDescription();
						echo "</fieldset>";
					}

		// ******************************************************
		// Nouvelles actions
		$actions = $projet->getActions();
		echo "<fieldset><legend><div id='mask1'></div><span>Actions</span><div id='mask2'></div></legend><div id='action-tab2'>";



		displayActions($actions,$projet);
		echo "</div></fieldset>";
?>
				<script>
	
					function deleteAction(id) {
						$("#acti_"+id).hide();
					}	
				</script>
<?
					// **************************************33
					// Formulaire pour ajouter l'action
					echo "<form id='formulaire2'>";
					echo "<div id='code-formulaire2-display' style='display:none'>";
					echo "<input type='hidden' id='form_target2' value='/ajax/project.php'/>";
					echo "<input type='hidden' name='proj_id' value='".$_GET["proj_id"]."'/>";
					echo "<input type='hidden' name='action' value='addAction'/>";
					?>
					<span class='omo-label-text'><? echo T_("Ajouter une action:"); ?></span>
					<input name='actionString' id='actionString' style='width:100%'/>
					  
					  </div>
<?					if (($projet->getStatusId() & (\holacracy\Project::ACTIVE_PROJECTS | \holacracy\Project::PROPOSED_PROJECT)) && $canEdit) {

					  echo "<div style='text-align:right'><button id='addAction' >".T_("Ajouter une action...")."</button></div>";
					}

					echo "</form>";	

					
					$comments=$projet->getComments();
     				if (count($comments)>0 ||  $canEdit) {

					echo "<fieldset><legend><div id='mask1'></div><span>".T_("Journal")."</span><div id='mask2'></div></legend>";

					echo "<form id='formulaire'>";
					echo "<div id='code-formulaire-display' style='display:none'>";
					echo "<input type='hidden' id='form_target' value='/ajax/project.php'/>";
					echo "<input type='hidden' name='proj_id' value='".$_GET["proj_id"]."'/>";
					echo "<input type='hidden' name='action' value='addComment'/>";
?>
					<style>
						.ui-state-highlight { height: 1.25em; line-height: 1.2em; }
					</style>
					<span class='omo-label-text'><? echo T_("Ajouter une entrée de journal:"); ?></span>
					Temps travaillé: <input id='comm_tt' name='comm_tt'><select name='comm_tt_unite' id='comm_tt_unite'><option value='1'>minutes</option><option value='60'>heures</option><option value='480'>jours</option></select>
					Temps restant: <input id='comm_tr' name='comm_tr'><select name='comm_tr_unite' id='comm_tr_unite'><option value='1'>minutes</option><option value='60'>heures</option><option value='480'>jours</option></select>
					<textarea name='comment' class='tinymce' style='width:100%'></textarea>
					<!-- visibilité (cercle, rôle, org...) pas dans la version 8 -->
					  <div id="visibility">
					  <span class='omo-label-fields'><? echo T_("Visibilit&eacute;:"); ?></span>
					    <input type="radio" id="proj_visibility1" name="proj_visibility" value="1"/><label for="radio1"><? echo T_("Public"); ?></label>
					    <input type="radio" id="proj_visibility2" name="proj_visibility" value="2"/><label for="radio2"><? echo T_("Organisation"); ?></label>
					    <input type="radio" id="proj_visibility3" name="proj_visibility" value="3"/><label for="radio3"><? echo T_("Cercle"); ?></label>
					    <input type="radio" id="proj_visibility4" name="proj_visibility" value="4"/><label for="radio4"><? echo T_("R&ocirc;le"); ?></label>
					    <input type="radio" id="proj_visibility5" name="proj_visibility" value="5" checked="checked"/><label for="radio5"><? echo T_("Priv&eacute;"); ?></label>
					  </div> 
					  </div>
<?					if (($projet->getStatusId() & (\holacracy\Project::ACTIVE_PROJECTS | \holacracy\Project::PROPOSED_PROJECT)) && $canEdit) {

					  echo "<div style='text-align:right'><button id='addComment' >".T_("Ajouter une note...")."</button></div>";
					}
?>
					</form>
<?				
					// Affiche la liste des entrées de journal
					$comments=$projet->getComments();
					foreach ($comments as $comment) {
						// Affiche les infos temporelles (temps de travail et temps restant)
//						echo "Temps travaillé: ".$comment->getTT()." ".($comment->getTTUnite()==60?"heures":$comment->getTTUnite()==480?"jours":"minutes");
//						echo "Temps restant: ".$comment->getTR()." ".($comment->getTRUnite()==60?"heures":$comment->getTRUnite()==480?"jours":"minutes");
						echo $comment->getDescription();
						echo "<div class='omo-signature'>".T_("Créé par ");
						echo $comment->getAuthor()->getUserName();
						echo T_(" le ");
						echo date("d.m.Y",strtotime($comment->getCreationDate()));
						if ($comment->getModificationDate()!="") {
							echo T_(", Modifié par ");
							echo $comment->getModifier()->getUserName();
							echo T_(" le ");
							echo date("d.m.Y",strtotime($comment->getModificationDate()));

						}
						echo "</div><hr>";
					}

?>
					</fieldset>
<?
	}
?>
					<script src="plugins/tinymce/jquery.tinymce.min.js"></script>
					<script>
						$( "#visibility" ).buttonset();
						
						  $( "#action-tab" ).sortable({axis: 'y', 
      placeholder: "ui-state-highlight"
    });
						// *******************************************333333333
    					// Observateur sur action-list2
					$("#action-tab2").on("click",".action-list input", function() {
						if (!$(this).is(':checked')) {
							// Efface réellement (en ajax) l'élément
							$.ajax({
								url: "/ajax/project.php?action=restoreAction&id="+$(this).closest("div").attr("name")
							}).done(function(data) {});

							// Restaure l'état
							$(this).closest("div").stop(true,false);
							$(this).closest("div").css("text-decoration","");
							$(this).closest("div").css("opacity","1");
							//$(this).closest("div").removeClass("checkedimg");
							
						} else {
							// Efface réellement (en ajax) l'élément
							$.ajax({
								url: "/ajax/project.php?action=deleteAction&id="+$(this).closest("div").attr("name")
							}).done(function(data) {});
							// Cache la ligne
							$(this).closest("div").css("text-decoration","line-through");
							$(this).closest("div").animate({opacity:0.5},5000,function() {
								$(this).find("input").attr("disabled","true");


							});
						}
					});	

					// Observateur sur action-list
					$("#action-tab").on("click",".action-list input", function() {
						if (!$(this).is(':checked')) {
							// Efface réellement (en ajax) l'élément
							$.ajax({
								url: "/ajax/project.php?action=restoreAction&id="+$(this).closest("div").attr("name")
							}).done(function(data) {});

							// Restaure l'état
							$(this).closest("div").stop(true,false);
							$(this).closest("div").css("text-decoration","");
							$(this).closest("div").css("opacity","1");
							//$(this).closest("div").removeClass("checkedimg");
							
						} else {
							// Efface réellement (en ajax) l'élément
							$.ajax({
								url: "/ajax/project.php?action=deleteAction&id="+$(this).closest("div").attr("name")
							}).done(function(data) {});
							// Cache la ligne
							$(this).closest("div").css("text-decoration","line-through");
							$(this).closest("div").animate({opacity:0.5},5000,function() {
								$(this).find("input").attr("disabled","true");


							});
						}
					});	
						
			        $('textarea.tinymce').tinymce({
                    // Location of TinyMCE script
	                    script_url : 'plugins/tinymce/tinymce.min.js',
						menubar : false,
					plugins: "paste",
					extended_valid_elements : "p/div/tr/li,br/td",
                    invalid_elements : "span, table, tr, img, button, input, form, ul, li",
					paste_auto_cleanup_on_paste : true,
					paste_remove_styles: true,
		            paste_remove_styles_if_webkit: true,
		            paste_strip_class_attributes: true,						toolbar: "undo redo | bold italic | bullist numlist outdent indent",
						statusbar : false

            		});
            		$("#addComment").button()
            		.click(function() {   
            			if ($("#code-formulaire-display").css("display")=="none") {
					      	// Premier click, affiche le formulaire
					      	$("#code-formulaire-display").css("display","");
					      	$("span", this).text("Ajouter");
					      	return false;
				      	} else {
				      		$("#formulaire").submit();
				      		return false;
				      	}
					});
					
           		$("#addAction").button()
            		.click(function() {   
            			if ($("#code-formulaire2-display").css("display")=="none") {
							
					      	// Premier click, affiche le formulaire
					      	$("#code-formulaire2-display").css("display","");
					      	$("span", this).text("Ajouter");
					      	return false;
				      	} else {
							// Désactive le bouton
							$(this).prop( "disabled", true );
							// Envoie le formulaire
				      		$("#formulaire2").submit();
				      		return false;
				      	}
					});

					// Formulaire de la création d'actions
					 $("#formulaire2").submit(function() {
						// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
					 	$.post($("#form_target2")[0].value, $("#formulaire2").serialize()) 
					        .done(function(data, textStatus, jqXHR) {
					            if (textStatus="success")
					            {

					            	// Ajoute la nouvelle action
					            	$("#action-tab2").append(data);
					            	// Restaure le formulaire dans son état initial
					            	$("#code-formulaire2-display").css("display","none");
					            	$("#actionString").val("");
					      			$("span", $("#addAction")).text("Ajouter une action...");
					      			$("#addAction").prop( "disabled", false );
					                // Exécute le code si nécessaire
					                eval($("#formulaire").find("script").text());
								}
					            else {
					            	// Problème d'envoi
					            	$(this).prop( "disabled", false );
					            	alert("Echec lors de l'envoi du formulaire. Essayez encore.");
					            
					            }
					        });
					        // Bloque la procédure standard d'envoi
					        return false;
					});

					// Formulaire des commentaires					
					 $("#formulaire").submit(function() {
						// Envoie le formulaire en AJAX (méthode POST), la destination étant définie par l'élément à l'ID form_target 
					 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
					        .done(function(data, textStatus, jqXHR) {
					            if (textStatus="success")
					            {
					            	// Affiche les données en retour en remplacement du contenu du formulaire (le contenant reste) 
					                $("#formulaire")[0].innerHTML=data;
					                eval($("#formulaire").find("script").text());
								}
					            else {
					            	// Problème d'envoi
					            	alert("Echec!");
					            
					            }
					        });
					        // Bloque la procédure standard d'envoi
					        return false;
					});
					
					// Boutons de dialogue
					
					$( "#dialogStd" ).dialog({ buttons: [<?			

					// Le bouton modifier n'est disponible que pour les projets actifs, et propriété de l'utilisateur courant, ou de la personne en charge du rôle, ou du 1er lien
					if (($projet->getStatusId() & \holacracy\Project::ACTIVE_PROJECTS) && $canEdit) {
					echo ' { id: "button_edit", text: "Modifier", click: function() { $("#dialogStdContent").load("/ajax/project.php?action=FormAddProject&proj_id='.$_GET["proj_id"].'&back='.urlencode($_SERVER["REQUEST_URI"]).'")} },';
					}
					
					// Le bouton ACCEPTER s'affiche s'il s'agit d'un projet proposé
					if (($projet->getStatusId() & \holacracy\Project::PROPOSED_PROJECT) && ($_SESSION["currentUser"]->isRole($projet->getRole()) || (!$projet->getRole()->getUserId()>0 && $projet->getRole()->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId())  ) ) {
						echo ' { id: "button_edit", text: "Accepter", click: function() {$.post( "/ajax/project.php", { action: "ChangeStatus", proj_id: "'.$projet->getId().'", typr_id: "'.\holacracy\Project::CURRENT_PROJECT.'" }).done(function( data ) { $("#dialogStdContent").load("'.$_SERVER["REQUEST_URI"].'")  }) }},';
						echo ' { id: "button_edit", text: "Refuser", click: function() {$.post( "/ajax/project.php", { action: "ChangeStatus", proj_id: "'.$projet->getId().'", typr_id: "'.\holacracy\Project::REFUSED_PROJECT.'" }).done(function( data ) {  $("#dialogStdContent").load("'.$_SERVER["REQUEST_URI"].'")  }) }},';
						
					
					}
					
					// Le bouton RETOUR s'affiche si un URL est passé avec le paramètre BACK
					if (isset($_GET["back"])) {  
						echo ' { id: "button_back", text: "Retour", click: function() { $("#dialogStdContent").load("'.urldecode($_GET["back"]).'") } },';
					}
					echo '{ text: "Fermer", click: function() { $( this ).dialog( "close" ); } }';
?>]});				
					</script>
					<?
					//$role_id]=$projet->getRoleId();
					//$_GET["prst_id"]=$projet->getStatus();
			}
		} else 
		if ($_GET["action"]=="FormAddAction") {
				$titre="";
					$currentUserId="";
				// Si le projet est spécifié, le charge
				if (isset($_GET["proj_id"])) {
					$projet=$_SESSION["currentManager"]->loadProjects($_GET["proj_id"]);
					$_GET["role_id"]=$projet->getRoleId();
					$_GET["prst_id"]=$projet->getStatusId();
					$titre=$projet->getTitle();
					$description=$projet->getDescription();
					$currentUserId=$projet->getUserId();
					$visibility=$projet->getVisibility();
				}
				if (isset($_GET["role_id"])) {
					$role=$_SESSION["currentManager"]->loadRole($_GET["role_id"]);
					// Lit également les RolleFillers
					$roleFillers=$role->getRoleFillers();
				}
?>
				<!-- Pour les accents -->
				<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />				
				<form id="form_create_action">
				<div class='omo-fieldset omo-fullwidth'><div class='omo-label'><? echo "Description de l'action:"; ?></div><div class='omo-field'><input style='width:100%' type="text" id="acti_title" name="acti_title" value="<?=$titre?>"/></div></div>

				<input type='hidden' id='proj_id' name='proj_id' value='<?=(isset($_GET["proj_id"])?$_GET["proj_id"]:"") ?>'/>
				<input type='hidden' id='role_id' name='role_id' value='<?=$_GET["role_id"] ?>'/>
				
				<? 
					// Choix uniquement si nouvelle action
					if (!isset($_GET["proj_id"])) {
						// Personne qui propose le projet (personne connectée si nouveau projet)
						echo "<div class='omo-fieldset'><div class='omo-label'>Proposé par:</div><div class='omo-field'>";
						echo "<input type='hidden' name='user_id_proposer' value='".$_SESSION["currentUser"]->getId()."'/>";
						// Affiche le nom de la personne connectée
						echo "<b>".$_SESSION["currentUser"]->getUserName()."</b> (moi-même)";

						// Affiche la liste de ses rôles pour spécification
						echo " dans mon rôle ";
						echo "<select id='role_id_proposer' name='role_id_proposer'>";
						echo "<option value=''>non spécifié</option>";
						$roles=$_SESSION["currentUser"]->getRoles($role->getSuperCircle());
						foreach ($roles as $eachrole) {
								echo "<option value='".$eachrole->getId()."'";
								if ($eachrole->getId()==$role->getId()) echo " selected ";
								echo ">".$eachrole->getName()."</option>";
						}
						echo "</select>";
								
								
						echo "<input type='checkbox' checked name='proj_important' style='width:inherit; vertical-align:baseline'> Marquer le projet comme important";				
						echo "</div></div>";
					}

						// Personne en charge
						echo "<div class='omo-fieldset'><div class='omo-label'>".T_("Personne en charge:")."</div><div class='omo-field'><select style='max-width:100%' id='user_id' name='user_id'>";
						if ($role->getUserId()>0) {
							echo "<option value='".$role->getUserId()."'>".$role->getUser()->getUserName()."</option>";
						} else {
							echo "<option value=''>Premier Lien</option>";
						}
						if (!($role->getType() & \holacracy\Role::STRUCTURAL_ROLES)) {
							foreach ($roleFillers as $roleFiller) {
								echo "<option value='".$roleFiller->getUserId()."'";
								if ($currentUserId!="") {
									if ($roleFiller->getUserId()==$currentUserId) echo " selected";
								} 
								else
									if ($roleFiller->getUserId()==$_SESSION["currentUser"]->getId() && $role->getUserId()!=$_SESSION["currentUser"]->getId()) echo " selected";
								echo ">".$roleFiller->getUserName()." (focus ".$roleFiller->getFocus().")</option>";
							}
						}
						echo "</select></div></div>";
			?>	
				
			
			
				</form>
				<!-- Script jquery du formulaire -->
				<script>

							
					$( "#dialogStd" ).dialog({ buttons: [<?			

					echo ' { id: "button_create_action", text: "'.(isset($_GET["proj_id"])?"Sauver cette":"Créer une").' Action", click: function() { $( "#form_create_action").submit(); } },';
		
					if (isset($_GET["back"])) {  
						echo ' { id: "button_back", text: "Retour", click: function() { $("#dialogStdContent").load("'.urldecode($_GET["back"]).'") } },';
					}
					echo '{ text: "Fermer", click: function() { $( this ).dialog( "close" ); } }';
?>]});					
				
					$( "#form_create_action").submit(function (event) {
						// Poste le formulaire en Ajax
						elem=$("#button_create_project");

						$("#button_create_project").prop('disabled', true).addClass("ui-state-disabled");
						$.ajax({
					       url : "ajax/project.php",
					       type : "POST",
					       data : $("#form_create_action").serialize(),
					       success : function(code_html, statut){ 
					       	code_html=code_html.trim();
					           if (code_html=="ok" ) {
								   refreshProjects($("#role_id").val());
					           		$( "#dialogStd" ).dialog( "close" );
							   }  else {
							   		// Alerte personnalisée
							   			
									$("<div><div class='ui-state-error'>"+code_html+"</div></div>").dialog({modal: true,title:"Erreur"});
							   		$(elem).removeAttr('disabled').removeClass( 'ui-state-disabled' );;
							   }
					       }
					    }); 
						event.preventDefault();
					});
				</script>
			
			<?					
			

		} else
		if ($_GET["action"]=="FormAddProject") {
					$titre="";
					$description="";
					$currentUserId="";
					$visibility=2;
				// Si le projet est spécifié, le charge
				if (isset($_GET["proj_id"])) {
					$projet=$_SESSION["currentManager"]->loadProjects($_GET["proj_id"]);
					$_GET["role_id"]=$projet->getRoleId();
					$_GET["prst_id"]=$projet->getStatusId();
					$titre=$projet->getTitle();
					$description=$projet->getDescription();
					$currentUserId=$projet->getUserId();
					$visibility=$projet->getVisibility();
				}
				if (isset($_GET["role_id"])) {
					$role=$_SESSION["currentManager"]->loadRole($_GET["role_id"]);
					// Lit également les RolleFillers
					$roleFillers=$role->getRoleFillers();
				}
				
				$status=\holacracy\Project::getAllStatus();
			
			?>
				<!-- Pour les accents -->
				<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />				
				<form id="form_create_project">
				<div class='omo-fieldset omo-fullwidth'><div class='omo-label'><? echo T_("R&eacute;sultat attendu: "); ?></div><div class='omo-fieldhelp'>aide à la formulation : <i>cet objectif est atteint</i> et non <i>atteindre cet objectif</i>.</div><div class='omo-field'><input style='width:100%' type="text" id="proj_title" name="proj_title" value="<?=$titre?>"/></div></div>
						<div id="visibility">
					  <span class='omo-label-fields'><? echo T_("Visibilit&eacute;:"); ?></span>
					    <input type="radio" id="proj_visibility1" name="proj_visibility" value="1"<? if ($visibility==1) echo 'checked="checked"';?>/><label for="radio1"><? echo T_("Public"); ?></label>
					    <input type="radio" id="proj_visibility2" name="proj_visibility" value="2"<? if ($visibility==2) echo 'checked="checked"';?>/><label for="radio2"><? echo T_("Organisation"); ?></label>
					    <input type="radio" id="proj_visibility3" name="proj_visibility" value="3"<? if ($visibility==3) echo 'checked="checked"';?>/><label for="radio3"><? echo T_("Cercle"); ?></label>
					    <input type="radio" id="proj_visibility4" name="proj_visibility" value="4"<? if ($visibility==4) echo 'checked="checked"';?>/><label for="radio4"><? echo T_("R&ocirc;le"); ?></label>
					    <input type="radio" id="proj_visibility5" name="proj_visibility" value="5"<? if ($visibility==5) echo 'checked="checked"';?>/><label for="radio5"><? echo T_("Priv&eacute;"); ?></label>
					  </div>
				<div class='omo-fieldset omo-fullwidth'><div class='omo-label'><? echo T_("Informations compl&eacute;mentaires: "); ?></div><div class='omo-field'><textarea style='width:100%; height:100px;' id="proj_description" name="proj_description"><?=$description?></textarea></div></div>
				<input type='hidden' id='proj_id' name='proj_id' value='<?=(isset($_GET["proj_id"])?$_GET["proj_id"]:"") ?>'/>
				<input type='hidden' id='role_id' name='role_id' value='<?=$_GET["role_id"] ?>'/>
				
				<? 
					// Choix uniquement si nouveau projet
					if (!isset($_GET["proj_id"])) {
						// Personne qui propose le projet (personne connectée si nouveau projet)
						echo "<div class='omo-fieldset'><div class='omo-label'>Proposé par:</div><div class='omo-field'>";
						echo "<input type='hidden' name='user_id_proposer' value='".$_SESSION["currentUser"]->getId()."'/>";
						// Affiche le nom de la personne connectée
						echo "<b>".$_SESSION["currentUser"]->getUserName()."</b> (moi-même)";

						// Affiche la liste de ses rôles pour spécification
						echo " dans mon rôle ";
						echo "<select id='role_id_proposer' name='role_id_proposer'>";
						echo "<option value=''>non spécifié</option>";
						$roles=$_SESSION["currentUser"]->getRoles($role->getSuperCircle());
						foreach ($roles as $eachrole) {
								echo "<option value='".$eachrole->getId()."'";
								if ($eachrole->getId()==$role->getId()) echo " selected ";
								echo ">".$eachrole->getName()."</option>";
						}
						echo "</select>";
								
								
						echo "<input type='checkbox' checked name='proj_important' style='width:inherit; vertical-align:baseline'> Marquer le projet comme important";				
						echo "</div></div>";
					}

						// Personne en charge
						$display = array();
						echo "<div class='omo-fieldset'><div class='omo-label'>".T_("Personne en charge:")."</div><div class='omo-field'><select id='user_id' name='user_id'>";
						echo "<optgroup label='Personne(s) en charge'>";
						if ($role->getUserId()>0) {
							echo "<option value='".$role->getUserId()."'>".$role->getUser()->getUserName()."</option>";
							$display[]=$role->getUserId();
						} else {
							echo "<option value=''>Premier Lien</option>";
						}
						if (!($role->getType() & \holacracy\Role::STRUCTURAL_ROLES)) {
							foreach ($roleFillers as $roleFiller) {
								echo "<option value='".$roleFiller->getUserId()."'";
								if ($currentUserId!="") {
									if ($roleFiller->getUserId()==$currentUserId) echo " selected";
								} 
								else
									if ($roleFiller->getUserId()==$_SESSION["currentUser"]->getId() && $role->getUserId()!=$_SESSION["currentUser"]->getId()) echo " selected";
								$display[]=$roleFiller->getUserId();
								echo ">".$roleFiller->getUserName()." (focus ".$roleFiller->getFocus().")</option>";
							}
						}
						echo "</optgroup>";
						echo "<optgroup label='Autres membres du cercle'>";
						$members=$role->getSuperCircle()->getMembers();
						foreach ($members as $member) {
							if (!in_array($member->getId(),$display)) {
								echo "<option value='".$member->getId()."'";
								if ($currentUserId!="") {
									if ($member->getId()==$currentUserId) echo " selected";
								} 
								else
									if ($member->getId()==$_SESSION["currentUser"]->getId() && $role->getUserId()!=$_SESSION["currentUser"]->getId()) echo " selected";
								echo ">".$member->getUserName()."</option>";
							}
						}
						
						echo "</optgroup>";				
						echo "</select></div></div>";
					
				?>
				
				<?
					// Demande le status uniquement pour les projets de ses propres rôles
					if ($_SESSION["currentUser"]->isAdmin() || (isset($role) && $_SESSION["currentUser"]->isRole($role)) || (!$role->getUserId()>0 && $role->getSuperCircle()->getUserId()==$_SESSION["currentUser"]->getId())) {
						echo "<div class='omo-fieldset omo-inlineblock'><div class='omo-label'>Status:</div><div class='omo-field'><select id='prst_id' name='prst_id'>";
						foreach ($status as $statu) {
							echo "<option value='".$statu->getId()."'";
							 if (isset($_GET["prst_id"]) && $_GET["prst_id"]==$statu->getId()) echo " selected";
							echo ">".$statu->getLabel()."</option>";
						}
						echo "	</select></div></div>";
					}
				?>	
				
			
			
				</form>
				<!-- Script jquery du formulaire -->
				<script>

							
					$( "#dialogStd" ).dialog({ buttons: [<?			

					echo ' { id: "button_create_project", text: "'.(isset($_GET["proj_id"])?"Enregistrer":"Créer").' Projet", click: function() { $( "#form_create_project").submit(); } },';
		
					if (isset($_GET["back"])) {  
						echo ' { id: "button_back", text: "Retour", click: function() { $("#dialogStdContent").load("'.urldecode($_GET["back"]).'") } },';
					}
					echo '{ text: "Fermer", click: function() { $( this ).dialog( "close" ); } }';
?>]});					
				
					$( "#form_create_project").submit(function (event) {
						// Poste le formulaire en Ajax
						elem=$("#button_create_project");

						$("#button_create_project").prop('disabled', true).addClass("ui-state-disabled");
						$.ajax({
					       url : "ajax/project.php",
					       type : "POST",
					       data : $("#form_create_project").serialize(),
					       success : function(code_html, statut){ 
					       	code_html=code_html.trim();
					           if (code_html=="ok" ) {
								   refreshProjects($("#role_id").val());
					           		$( "#dialogStd" ).dialog( "close" );
							   }  else {
							   		// Alerte personnalisée
							   			
									$("<div><div class='ui-state-error'>"+code_html+"</div></div>").dialog({modal: true,title:"Erreur"});
							   		$(elem).removeAttr('disabled').removeClass( 'ui-state-disabled' );;
							   }
					       }
					    }); 
						event.preventDefault();
					});
				</script>
			
			<?
		}
	} 

?>
