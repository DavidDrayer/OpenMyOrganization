<?
			$mainCircle=$_SESSION["currentManager"]->loadCircle($_POST["circle"]);
			
			// Contrôle la validité des données avant de sauver
			foreach ($_POST["formulaire"] as $id) {
				$strAction="";
				switch ($_POST["form_type_".$id]) {
					// Crée un nouveau cercle
					case 1:
						// Création d'un projet
						// Le nom est-il renseigné?
						
					break;
					case 4:
						if (!(isset($_POST["nameProject_".$id]) && $_POST["nameProject_".$id]!="")) {
							// Erreur
						}
					break;
				}
			}
			
			// Si une erreur a été trouvée, retourne le message
			//header('HTTP/1.1 520 Web server is returning an unknown error');
    		//exit();
    		
			
			echo "<h1>Sauvegarde effectuée</h1>";
			// Création d'une entrée d'historique
			$history=new \holacracy\History();
			$history->setCircle($mainCircle);
			$history->setMeetingId($_POST["meeting"]);

			foreach ($_POST["formulaire"] as $id) {
				$strAction="";
				switch ($_POST["form_type_".$id]) {
					// Projets
					case 1:

							// Création d'un nouveau projet
							if (isset($_POST["nameProject_".$id]) && $_POST["nameProject_".$id]!="") {
								
								$project=new \holacracy\Project();
								$project->setTitle(utf8_decode($_POST["nameProject_".$id]));
								$project->setDescription(utf8_decode($_POST["descriptionProject_".$id]));
								$project->setType(\holacracy\Project::PROJECT);
								$project->setStatus($_POST["idStat_".$id]);
								
								// Rôle ou sous-rôle?
								if (isset($_POST["idSubRole_".$id]) && $_POST["idSubRole_".$id]!="") {
									$project->setRole($_POST["idSubRole_".$id]);
								} else {
									$project->setRole($_POST["idRole_".$id]);
								}
								// Personne qui va prendre en charge ce projet ?
								if (isset($_POST["idRoleFocus_".$id]) && $_POST["idRoleFocus_".$id]!=""){
									$project->setUser($_POST["idRoleFocus_".$id]);
								}
								// Personne qui a proposé le projet ?
								if (isset($_POST["idProposer_".$id]) && $_POST["idProposer_".$id]!=""){
										if (isset($_POST["idRoleProposer_".$id]) && $_POST["idRoleProposer_".$id]!=""){
											$project->setProposer($_POST["idProposer_".$id], $_POST["idRoleProposer_".$id]);
										} else {
											$project->setProposer($_POST["idProposer_".$id]);
										}
								}
					
								$_SESSION["currentManager"]->save($project);
								
								// Construit la chaîne d'information
								$strAction="Le projet [".$project->getTitle()."] a été créé.";
								$historyEntry=$history->attachChild();
								$historyEntry->setTitle($strAction);
								//$historyEntry->setLink("/role.php?id=".$circle->getId()."#tabs-6");
								$historyEntry->setRoleId($_POST["idRole_".$id]);

						
							}						
						
						break;
					// Actions
					case 4:
							// Création d'une nouvelle action
							if (isset($_POST["nameProject_".$id]) && $_POST["nameProject_".$id]!="") {
								
								$action=new \holacracy\Action();
								$action->setTitle(utf8_decode($_POST["nameProject_".$id]));
								// Défini pour qui c'est
								// Est-ce pour un rôle en particulier?
								if ($_POST["idRole_".$id]>0) {
									$action->setRole($_POST["idRole_".$id]);
									$action->setCircle($mainCircle);
									// Juste le rôle ou le focus est-il spécifié?
									if (isset($_POST["idRoleFocus_".$id])){
										$action->addUser($_POST["idRoleFocus_".$id]);
									}	
								} else {
									// Sinon, c'est pour une ou plusieurs personnes
									$action->setCircle($mainCircle);
									// Parcours la liste des personnes
									if (isset($_POST["idRoleFocus_".$id])){
										foreach ($_POST["idRoleFocus_".$id] as $f){
											$action->addUser($f);
										}
									}
								}
								// Défini qui l'a créée
								if (isset($_POST["idProposer_".$id]) && $_POST["idProposer_".$id]!="") {
									$action->setProposer($_POST["idProposer_".$id]);
								}
								// Dans quel rôle?
								if (isset($_POST["idRoleProposer_".$id]) && $_POST["idRoleProposer_".$id]!="") {
									$action->setProposerRole($_POST["idRoleProposer_".$id]);
								}
								// Et l'attache à un projet en particulier si nécessaire
								if (isset($_POST["idProjectProposer_".$id]) && $_POST["idProjectProposer_".$id]!="") {
									$action->setProject($_POST["idProjectProposer_".$id]);	
								}						
	
								$_SESSION["currentManager"]->save($action, true);
								
								// Construit la chaîne d'information
								$strAction="L'action [".$action->getTitle()."] a été créée.";
								$historyEntry=$history->attachChild();
								$historyEntry->setTitle($strAction);
								//$historyEntry->setLink("/role.php?id=".$circle->getId()."#tabs-6");
								$historyEntry->setRoleId($_POST["idRole_".$id]);

						
							}
						break;

					default:
						echo "<div>Pas de sauvegarde pour le formulaire2 ".$_POST["form_type_".$id]." en position $id</div>";
				}
				echo "<div>".$strAction."</div>";
			}
			if (count($history->getChilds())==0) $history->setTitle("Aucune modification effectuée.");
			$_SESSION["currentManager"]->save($history,true);

				// Le bouton fermer recharge la page, nouelle tension retourne sur le formulaire de base

			?>
    <script>$( "#dialogStd" ).dialog({ buttons: [{ text: "ScratchPad", click:showHideScratchPad},  {text: "Nouvelle tension", click: function() {$("#formulaire").load('/formulaires/form_triage.php?action=new&circle=<?=$mainCircle->getId()?>'); }}, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });</script>
			
