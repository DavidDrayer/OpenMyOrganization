<?
			$mainCircle=$_SESSION["currentManager"]->loadCircle($_POST["circle"]);
			
			// Contrôle de l'authorisation de l'utilisateur courant à effectuer cette opération - à implémenter
			
			// Contrôle de la validité du formulaire - à implémenter
			
			// Sauvegarde effective
			echo "<h1>Sauvegarde effectuée</h1>";

			// Création d'une entrée d'historique
			$history=new \holacracy\History();
			$history->setCircle($mainCircle);
			$history->setMeetingId($_POST["meeting"]);

			foreach ($_POST["formulaire"] as $id) {
				$strAction="";
				switch ($_POST["form_type_".$id]) {
					case 10:
						// Charge les infos sur les cercles et rôles concernés
						$role=$_SESSION["currentManager"]->loadRole($_POST["idRole_".$id]);
						$parentCircle=$role->getSuperCircle();
						$circle=$_SESSION["currentManager"]->loadCircle($_POST["idCircle_".$id]);
						
						// Déplace si nécessaire les check-listes, indicateurs et projets
						$projects=$role->getProjects();
						foreach ($projects as $project) {
							switch ($_POST["optProject_".$id]) {
								case 0: // Déplace avec
								break; // Ne fait rien, comportement par défaut
								case 1: // 1er lien
									$project->setRoleId($parentCircle->getLeadLink()->getId());
									$project->setUser($parentCircle->getLeadLink()->getUserId());
									$_SESSION["currentManager"]->save($project);
								break;
								case 2: // Supprimer
									$_SESSION["currentManager"]->delete($project);
								break;
							}
						}
						
						$metrics=$role->getMetrics();
						foreach ($metrics as $metric) {
							switch ($_POST["optInd_".$id]) {
								case 0: // Déplace avec
									$metric->setCircleId($circle->getId());
									$_SESSION["currentManager"]->save($metric);
								break; 
								case 1: // 1er lien
									$metric->setRoleId($parentCircle->getLeadLink()->getId());
									$_SESSION["currentManager"]->save($metric);
								break;
								case 2: // Supprimer
									$_SESSION["currentManager"]->delete($metric);
								break;
							}
						}
						
						$checklists=$role->getCheckLists();
						foreach ($checklists as $checklist) {
							switch ($_POST["optCheck_".$id]) {
								case 0: // Déplace avec
									$checklist->setCircleId($circle->getId());
									$_SESSION["currentManager"]->save($checklist);
								break; 
								case 1: // 1er lien
									$checklist->setRoleId($parentCircle->getLeadLink()->getId());
									$_SESSION["currentManager"]->save($checklist);
								break;
								case 2: // Supprimer
									$_SESSION["currentManager"]->delete($checklist);
								break;
							}
						}
						
						
						// Effectue le déplacement
						$role->setSuperCircleId($_POST["idCircle_".$id]);
						$_SESSION["currentManager"]->save($role);

						// Construit la chaîne d'information
						$strAction="Le role [".$role->getName()."] a été déplacé dans le cercle [".$circle->getName()."].";
						$historyEntry=$history->attachChild();
						$historyEntry->setTitle($strAction);
						$historyEntry->setLink("/circle.php?id=".$circle->getId());
						$historyEntry->setRole($role);
						
						// Construit une chaine d'information pour le sous-cercle également
						$history2=new \holacracy\History();
						$history2->setCircle($circle);
						$history2->setMeetingId($_POST["meeting"]);
						$strAction="Le role [".$role->getName()."] a été déplacé depuis le cercle [".$parentCircle->getName()."].";
						$historyEntry=$history2->attachChild();
						$historyEntry->setTitle($strAction);
						$historyEntry->setLink("/role.php?id=".$role->getId());
						$historyEntry->setRole($role);
						$_SESSION["currentManager"]->save($history2,true);
						
					break;
					// Crée un nouveau cercle
					case 1:
						if ($_POST["modeCreation_".$id]==1) {
							// Création d'un cercle à partir de rien - DDr, 19.6.2014
							if (isset($_POST["nomCircle_".$id]) && $_POST["nomCircle_".$id]!="") {
							// Sauvegarde un nouveau cercle
							$circle=new \holacracy\Circle();
							$circle->setName(utf8_decode($_POST["nomCircle_".$id]));
							$circle->setPurpose(utf8_decode($_POST["purposeCircle_".$id]));
							$circle->setType(\holacracy\Role::CIRCLE);
							// Recherche le cercle associé à la tension
							//$mainCircle=$action->getTension()->getMeeting()->getCircle();
							$mainCircle->attachRole($circle);
							$_SESSION["currentManager"]->save($circle);
	
							if (isset($_POST["ligne_".$id])) {
								foreach ($_POST["ligne_".$id] as $ligne) {
									if (isset($_POST["description_".$id."_".$ligne]) && $_POST["description_".$id."_".$ligne]!="") {
										//echo "Ligne: ".$ligne." : Ajoute la redevabilité ".$_POST["description_".$ligne];
										$redevabilite=new \holacracy\Accountability();
										$redevabilite->setDescription(utf8_decode($_POST["description_".$id."_".$ligne]));
										// Recherche le cercle associé à la tension
										$circle->attachAccountability($redevabilite);
										$_SESSION["currentManager"]->save($redevabilite);
									}
								}
							}
							if (isset($_POST["scope_ligne_".$id])) {
								foreach ($_POST["scope_ligne_".$id] as $ligne) {
									if (isset($_POST["scope_description_".$id."_".$ligne]) && $_POST["scope_description_".$id."_".$ligne]!="") {
										//echo "Ligne: ".$ligne." : Ajoute la redevabilité ".$_POST["description_".$ligne];
										$scope=new \holacracy\Scope();
										$scope->setDescription(utf8_decode($_POST["scope_description_".$id."_".$ligne]));
										// Recherche le cercle associé à la tension
										$scope->setRole($circle);
										$_SESSION["currentManager"]->save($scope);
									}
								}
							}
							}						
						} else {
							if ($_POST["idCopyRole_".$id]>0) {
								// Création par transformation d'un rôle en cercle - DDr, 19.6.2014
								$circle=$_SESSION["currentManager"]->loadCircle($_POST["idCopyRole_".$id]);
								$circle->setType(\holacracy\Role::CIRCLE);
								$_SESSION["currentManager"]->save($circle);
								

							}
						}
		
						if (isset($circle)) {
						
							// Construit la chaîne d'information
							$strAction="Le cercle [".$circle->getName()."] a été créé.";
							$historyEntry=$history->attachChild();
							$historyEntry->setTitle($strAction);
							$historyEntry->setLink("/circle.php?id=".$circle->getId());
							$historyEntry->setRole($circle);
						
							// Ajoute les rôles élus
							// Nouveau, crée un rôle premier lien - DDr, 29.8.2014
							$premier=new \holacracy\Role();
							$premier->setType(\holacracy\Role::LEAD_LINK_ROLE);
							$premier->setName("Premier lien");
							$premier->setSourceId(\holacracy\Role::LEAD_LINK_ROLE);
							$circle->attachRole($premier);
							$_SESSION["currentManager"]->save($premier);
							
							$second=new \holacracy\Role();
							$second->setType(\holacracy\Role::REP_LINK_ROLE);
							$second->setName("Second lien");
							$second->setSourceId(\holacracy\Role::REP_LINK_ROLE);
							$circle->attachRole($second);
							$_SESSION["currentManager"]->save($second);
							
							$secretaire=new \holacracy\Role();
							$secretaire->setType(\holacracy\Role::SECRETARY_ROLE);
							$secretaire->setName("Secrétaire");
							$secretaire->setSourceId(\holacracy\Role::SECRETARY_ROLE);
							$circle->attachRole($secretaire);
							$_SESSION["currentManager"]->save($secretaire);
							
							$facilitateur=new \holacracy\Role();
							$facilitateur->setType(\holacracy\Role::FACILITATOR_ROLE);
							$facilitateur->setName("Facilitateur");
							$facilitateur->setSourceId(\holacracy\Role::FACILITATOR_ROLE);
							$circle->attachRole($facilitateur);
							$_SESSION["currentManager"]->save($facilitateur);
						}
						
							// Deuxième partie dans la transformation d'un rôle en cercle
							if ($_POST["idCopyRole_".$id]>0) {

								
								// Transformer les focus en membres du cercle?
								switch($_POST["optFocus_".$id]) {
									case 0:
										// Transformer en membres du sous-cercle
										// Lit tous les focus
										$fillers=$circle->getRoleFillers();
										foreach ($fillers as $filler) {
											// Ajoute un membre
											$circle->addMember($filler);
											// Supprime le focus
											$_SESSION["currentManager"]->delete($filler);
										}
									break;
									case 1:
										// Transformer en rôles
										// Lit tous les focus
										$fillers=$circle->getRoleFillers();
										foreach ($fillers as $filler) {
											// Ajoute un rôle
											$newRole=new \holacracy\Role();
											$newRole->setType(\holacracy\Role::STANDARD_ROLE);
											$newRole->setName($filler->getFocus());											
											$circle->attachRole($newRole);
											// Défini la personne en charge en fnction du focus
											$newRole->setUserId($filler->getUserId());
											$_SESSION["currentManager"]->save($newRole);
											// Supprime le focus
											$_SESSION["currentManager"]->delete($filler);
										}
									break;
									case 2:
										// Supprimer
										// Lit tous les focus
										$fillers=$circle->getRoleFillers();
										foreach ($fillers as $filler) {
											// Supprime le focus
											$_SESSION["currentManager"]->delete($filler);
										}
									break;
								}
								
								// Déplacer les indicateurs?
								switch($_POST["optInd_".$id]) {
									case 0:
									// Conserver dans le super-cercle
									// A priori on ne fait rien - DDr 26.12.2014
									break;
									case 1:
									// Dubliquer dans le sous-cercle
									// Utilisation de la fonction clone sur la liste des projets
									$metrics=$circle->getMetrics();
									foreach ($metrics as $metric) {
										$newmetric=clone($metric);
										// nouveau cercle
										$newmetric->setCircle($metric->getRole());
										// Attribué au premier lien
										$newmetric->setRole($premier);
										$newmetric->setId();
										$_SESSION["currentManager"]->save($newmetric);
									}
									break;
									case 2:
									// Déplacer dans le sous-cercle
									// Modification du role vers celui de 1er lien (nécessite que le premier lien soit créé)
									$metrics=$circle->getMetrics();
									foreach ($metrics as $metric) {
										// nouveau cercle
										$metric->setCircle($metric->getRole());
										// Attribué au premier lien
										$metric->setRole($premier);
										$_SESSION["currentManager"]->save($metric);
									}
									break;
								}
								
								// Déplacer les check-listes?
								switch($_POST["optCheck_".$id]) {
									case 0:
									// Conserver dans le super-cercle
									// A priori on ne fait rien - DDr 26.12.2014
									break;
									case 1:
									// Dubliquer dans le sous-cercle
									// Utilisation de la fonction clone sur la liste des projets
									$checklists=$circle->getCheckLists();
									foreach ($checklists as $checklist) {
										$newchecklist=clone($checklist);
										// nouveau cercle
										$newchecklist->setCircle($checklist->getRole());
										// Attribué au premier lien
										$newchecklist->setRole($premier);
										$newchecklist->setId();
										$_SESSION["currentManager"]->save($newchecklist);
									}
									break;
									case 2:
									// Déplacer dans le sous-cercle
									// Modification du role vers celui de 1er lien (nécessite que le premier lien soit créé)
									$checklists=$circle->getCheckLists();
									foreach ($checklists as $checklist) {
										// nouveau cercle
										$checklist->setCircle($checklist->getRole());
										// Attribué au premier lien
										$checklist->setRole($premier);
										$_SESSION["currentManager"]->save($checklist);
									}					
									break;
								}
								
								// Déplacer les projets?
								switch($_POST["optProject_".$id]) {
									case 0:
									// Conserver dans le super-cercle
									// A priori on ne fait rien - DDr 26.12.2014
									break;
									case 1:
									// Dubliquer dans le sous-cercle
									// Utilisation de la fonction clone sur la liste des projets
									$projects=$circle->getProjects();
									foreach ($projects as $project) {
										$newproject=clone($project);
										// Attribué au premier lien
										$newproject->setRole($premier);
										$newproject->setId();
										$_SESSION["currentManager"]->save($newproject);
									}
									break;
									case 2:
									// Déplacer dans le sous-cercle
									// Modification du role vers celui de 1er lien (nécessite que le premier lien soit créé)
									$projects=$circle->getProjects();
									foreach ($projects as $project) {
										// Attribué au premier lien
										$project->setRole($premier);
										$_SESSION["currentManager"]->save($project);
									}
									break;
								}
							}

						
						break;
						// Sauve un lien transverse
						case 13:
						// Sauve un cercle existant
						case 2:
							// Même fonction pour sauver cercles et rôles
						case 5:
							if ((isset($_POST["idCircle_".$id]) && $_POST["idCircle_".$id]>0) || (isset($_POST["role_".$id]) && $_POST["role_".$id]>0)) {
							// Met à jour le cercle
							if (isset($_POST["idCircle_".$id])) {
								$circle=$_SESSION["currentManager"]->loadCircle($_POST["idCircle_".$id]);
								$strAction="Le cercle [".$circle->getName()."] a été modifié.";
								$historyEntry=$history->attachChild();
								$historyEntry->setTitle($strAction);
								$historyEntry->setLink("/circle.php?id=".$circle->getId());
								$historyEntry->setRole($circle);
							}
							if (isset($_POST["role_".$id])) {
								$circle=$_SESSION["currentManager"]->loadRole($_POST["role_".$id]);
								$strAction="Le rôle [".$circle->getName()."] a été modifié.";
								$historyEntry=$history->attachChild();
								$historyEntry->setTitle($strAction);
								$historyEntry->setLink("/role.php?id=".$circle->getId());
								$historyEntry->setRole($circle);
							}

							// Sauve nom et raison d'être uniquement s'il n'a pas un rôle référence
							$isTarget=($circle->getSourceId()>0 || $circle->getSourceCircleId()>0);
							if (!$isTarget) {
								// Construit la chaîne d'information
								if (isset($_POST["nomCircle_".$id])) 
								if ($circle->getName()!=utf8_decode($_POST["nomCircle_".$id])) {
									$circle->setName(utf8_decode($_POST["nomCircle_".$id]));
									$detail=$historyEntry->attachChild();
									$detail->setTitle("Le nom devient [".utf8_decode($_POST["nomCircle_".$id])."]");
								}							
								if (isset($_POST["purposeCircle_".$id])) 
								if ($circle->getPurpose()!=utf8_decode($_POST["purposeCircle_".$id])) {
									$text1=$circle->getPurpose();
									$circle->setPurpose(utf8_decode($_POST["purposeCircle_".$id]));
									$detail=$historyEntry->attachChild();
									$detail->setTitle("La raison d'être a été modifiée.");
									// Calcul la différence entre les 2 descriptions
									include_once '../plugins/inline_function.php';
	
									
									$text2=$circle->getPurpose();
									$diff = @inline_diff($text1, $text2, '');
									$detail->setDescription($diff);
								}
							}
							$_SESSION["currentManager"]->save($circle);
							
							// Sauve les redevabilités
							if (isset($_POST["ligne_".$id])) {
								foreach ($_POST["ligne_".$id] as $ligne) {
									if (isset($_POST["id_".$id."_".$ligne]) && $_POST["id_".$id."_".$ligne]!="") {
										if (isset($_POST["description_".$id."_".$ligne])) {
											if ($_POST["description_".$id."_".$ligne]!="") {
												//echo "Met à jour la redevabilité ".$_POST["id_".$ligne]." à ".$_POST["description_".$ligne];
												$redevabilite=$_SESSION["currentManager"]->loadAccountability($_POST["id_".$id."_".$ligne]);
												
												// Historique
												$detail=$historyEntry->attachChild();
												$detail->setTitle("Une redevabilité à été modifiée.");
												// Calcul la différence entre les 2 descriptions
												include_once '../plugins/inline_function.php';
												$text1=utf8_decode($_POST["description_".$id."_".$ligne]);
												$text2=$redevabilite->getDescription();
												$diff = @inline_diff($text2, $text1, '');
												$detail->setDescription($diff);
												
												$redevabilite->setDescription($text1);
												$_SESSION["currentManager"]->save($redevabilite);
												
												
											} else {
												//echo "Supprime la redevabilité ".$_POST["id_".$ligne];
												$redevabilite=$_SESSION["currentManager"]->loadAccountability($_POST["id_".$id."_".$ligne]);

												// Historique
												$detail=$historyEntry->attachChild();
												$detail->setTitle("Une redevabilité à été supprimée.");
												// Calcul la différence entre les 2 descriptions
												include_once '../plugins/inline_function.php';
												$text1="";
												$text2=$redevabilite->getDescription();
												$diff = @inline_diff($text2, $text1, '');
												$detail->setDescription($diff);

												$_SESSION["currentManager"]->delete($redevabilite);
											}
										}
									} else {
										if (isset($_POST["description_".$id."_".$ligne]) && $_POST["description_".$id."_".$ligne]!="") {
											//echo "Ligne: ".$ligne." : Ajoute la redevabilité ".$_POST["description_".$ligne];
											$redevabilite=new \holacracy\Accountability();

												// Historique
												$detail=$historyEntry->attachChild();
												$detail->setTitle("Une redevabilité à été ajoutée.");
												// Calcul la différence entre les 2 descriptions
												include_once '../plugins/inline_function.php';
												$text1=utf8_decode($_POST["description_".$id."_".$ligne]);
												$text2="";
												$diff = @inline_diff($text2, $text1, '');
												$detail->setDescription($diff);
												
												$redevabilite->setDescription($text1);
												$_SESSION["currentManager"]->save($redevabilite);


											$redevabilite->setDescription(utf8_decode($_POST["description_".$id."_".$ligne]));
											// Recherche le cercle associé à la tension
											$circle->attachAccountability($redevabilite);
											$_SESSION["currentManager"]->save($redevabilite);
										
										}
									}
								}
							}
							// Sauve les domaines
							if (isset($_POST["scope_ligne_".$id])) {
								foreach ($_POST["scope_ligne_".$id] as $ligne) {
									if (isset($_POST["scope_id_".$id."_".$ligne]) && $_POST["scope_id_".$id."_".$ligne]!="") {
										if (isset($_POST["scope_description_".$id."_".$ligne])) {
											if ($_POST["scope_description_".$id."_".$ligne]!="") {
												$scope=$_SESSION["currentManager"]->loadScope($_POST["scope_id_".$id."_".$ligne]);

												// Historique
												$detail=$historyEntry->attachChild();
												$detail->setTitle("Un domaine à été modifié.");
												// Calcul la différence entre les 2 descriptions
												include_once '../plugins/inline_function.php';
												$text1=utf8_decode($_POST["scope_description_".$id."_".$ligne]);
												$text2=$scope->getDescription();
												$diff = @inline_diff($text2, $text1, '');
												$detail->setDescription($diff);

												$scope->setDescription(utf8_decode($_POST["scope_description_".$id."_".$ligne]));
												$_SESSION["currentManager"]->save($scope);
											} else {
												//echo "Supprime la redevabilité ".$_POST["id_".$ligne];
												$scope=$_SESSION["currentManager"]->loadScope($_POST["scope_id_".$id."_".$ligne]);
												
												// Historique
												$detail=$historyEntry->attachChild();
												$detail->setTitle("Un domaine à été supprimé.");
												// Calcul la différence entre les 2 descriptions
												include_once '../plugins/inline_function.php';
												$text1="";
												$text2=$scope->getDescription();
												$diff = @inline_diff($text2, $text1, '');
												$detail->setDescription($diff);

												
												$_SESSION["currentManager"]->delete($scope);
											}
										}
									} else {
										if (isset($_POST["scope_description_".$id."_".$ligne]) && $_POST["scope_description_".$id."_".$ligne]!="") {
											//echo "Ligne: ".$ligne." : Ajoute la redevabilité ".$_POST["description_".$ligne];
											$scope=new \holacracy\Scope();
											
											// Historique
											$detail=$historyEntry->attachChild();
											$detail->setTitle("Un domaine à été ajouté.");
											// Calcul la différence entre les 2 descriptions
											include_once '../plugins/inline_function.php';
											$text1=utf8_decode($_POST["scope_description_".$id."_".$ligne]);
											$text2="";
											$diff = @inline_diff($text2, $text1, '');
											$detail->setDescription($diff);

											$scope->setDescription(utf8_decode($_POST["scope_description_".$id."_".$ligne]));
											// Recherche le cercle associé à la tension
											$scope->setRole($circle);
											$_SESSION["currentManager"]->save($scope);
										
										}
									}
								}
							}
							}
						break;
						case 3:
							if (isset($_POST["idCircle_".$id]) && $_POST["idCircle_".$id]!="") {
								$circle=$_SESSION["currentManager"]->loadCircle($_POST["idCircle_".$id]);
								$supercircle=$circle->getSuperCircle();
					
							// Déplace si nécessaire les check-listes, indicateurs et projets
							$projects=$circle->getProjects();
							foreach ($projects as $project) {
								switch ($_POST["optProject_".$id]) {
									case 0: // 1er lien
										$project->setRoleId($supercircle->getLeadLink()->getId());
										$project->setUser($supercircle->getLeadLink()->getUserId());
										$_SESSION["currentManager"]->save($project);
									break;
									case 1: // Supprimer
										$_SESSION["currentManager"]->delete($project);
									break;
								}
							}
						
						$metrics=$circle->getMetrics();
						foreach ($metrics as $metric) {
							switch ($_POST["optInd_".$id]) {
								case 0: // 1er lien
									$metric->setRoleId($supercircle->getLeadLink()->getId());
									$_SESSION["currentManager"]->save($metric);
								break;
								case 1: // Supprimer
									$_SESSION["currentManager"]->delete($metric);
								break;
							}
						}
						
						$checklists=$circle->getCheckLists();
						foreach ($checklists as $checklist) {
							switch ($_POST["optCheck_".$id]) {
								case 0: // 1er lien
									$checklist->setRoleId($supercircle->getLeadLink()->getId());
									$_SESSION["currentManager"]->save($checklist);
								break;
								case 1: // Supprimer
									$_SESSION["currentManager"]->delete($checklist);
								break;
							}
						}
						
								
								$strAction="Le cercle [".$circle->getName()."] a été supprimé.";
								$historyEntry=$history->attachChild();
								$historyEntry->setTitle($strAction);
								$historyEntry->setRole($circle);
								$circle->delete();
								
							}
						break;
					case 4:
						if (isset($_POST["nomCircle_".$id]) && $_POST["nomCircle_".$id]!="") {
						// Sauvegarde un nouveau rôle
						$circle=new \holacracy\Role();
						$circle->setName(utf8_decode($_POST["nomCircle_".$id]));
						$circle->setPurpose(utf8_decode($_POST["purposeCircle_".$id]));
						// Recherche le cercle associé à la tension
						//$mainCircle=$action->getTension()->getMeeting()->getCircle();
						$mainCircle->attachRole($circle);
						$_SESSION["currentManager"]->save($circle);
						
						// Construit la chaîne d'information
						$strAction="Le rôle [".$circle->getName()."] a été créé.";
						$historyEntry=$history->attachChild();
						$historyEntry->setTitle($strAction);
						$historyEntry->setLink("/role.php?id=".$circle->getId());
						$historyEntry->setRole($circle);
						
						if (isset($_POST["ligne_".$id])) {
							foreach ($_POST["ligne_".$id] as $ligne) {
								if (isset($_POST["description_".$id."_".$ligne]) && $_POST["description_".$id."_".$ligne]!="") {
									//echo "Ligne: ".$ligne." : Ajoute la redevabilité ".$_POST["description_".$ligne];
									$redevabilite=new \holacracy\Accountability();
									$redevabilite->setDescription(utf8_decode($_POST["description_".$id."_".$ligne]));
									// Recherche le cercle associé à la tension
									$circle->attachAccountability($redevabilite);
									$_SESSION["currentManager"]->save($redevabilite);
								}
							}
						}
						if (isset($_POST["scope_ligne_".$id])) {
							foreach ($_POST["scope_ligne_".$id] as $ligne) {
								if (isset($_POST["scope_description_".$id."_".$ligne]) && $_POST["scope_description_".$id."_".$ligne]!="") {
									//echo "Ligne: ".$ligne." : Ajoute la redevabilité ".$_POST["description_".$ligne];
									$scope=new \holacracy\Scope();
									$scope->setDescription(utf8_decode($_POST["scope_description_".$id."_".$ligne]));
									// Recherche le cercle associé à la tension
									$scope->setRole($circle);
									$_SESSION["currentManager"]->save($scope);
								}
							}
						}
						}
						break;
					case 6:
							if (isset($_POST["idRole_".$id]) && $_POST["idRole_".$id]!="") {
								$circle=$_SESSION["currentManager"]->loadRole($_POST["idRole_".$id]);
								$supercircle=$circle->getSuperCircle();
								
															// Déplace si nécessaire les check-listes, indicateurs et projets
							$projects=$circle->getProjects();
							foreach ($projects as $project) {
								switch ($_POST["optProject_".$id]) {
									case 0: // 1er lien
										$project->setRoleId($supercircle->getLeadLink()->getId());
										$project->setUser($supercircle->getLeadLink()->getUserId());
										$_SESSION["currentManager"]->save($project);
									break;
									case 1: // Supprimer
										$_SESSION["currentManager"]->delete($project);
									break;
								}
							}
						
						$metrics=$circle->getMetrics();
						foreach ($metrics as $metric) {
							switch ($_POST["optInd_".$id]) {
								case 0: // 1er lien
									$metric->setRoleId($supercircle->getLeadLink()->getId());
									$_SESSION["currentManager"]->save($metric);
								break;
								case 1: // Supprimer
									$_SESSION["currentManager"]->delete($metric);
								break;
							}
						}
						
						$checklists=$circle->getCheckLists();
						foreach ($checklists as $checklist) {
							switch ($_POST["optCheck_".$id]) {
								case 0: // 1er lien
									$checklist->setRoleId($supercircle->getLeadLink()->getId());
									$_SESSION["currentManager"]->save($checklist);
								break;
								case 1: // Supprimer
									$_SESSION["currentManager"]->delete($checklist);
								break;
							}
						}

								
								$strAction="Le rôle [".$circle->getName()."] a été supprimé.";
								$historyEntry=$history->attachChild();
								$historyEntry->setTitle($strAction);
								$historyEntry->setRole($circle);
								$circle->delete();
							}
						break;
					// Sauve une policy
					case 7:
							if (isset($sourceRole)) unset($sourceRole);
							if (isset($_POST["policyType_".$id]) && $_POST["policyType_".$id]==2) {
								if (substr($_POST["idSourceCircle_".$id],0,2)=="R_") {
									$sourceRole=$_SESSION["currentManager"]->loadRole(substr($_POST["idSourceCircle_".$id],2));
									$sourceCircle=$sourceRole->getSuperCircle();
								} else {
									$sourceCircle=$_SESSION["currentManager"]->loadCircle($_POST["idSourceCircle_".$id]);
								}
								if (isset($_POST["idUser_".$id])) {
									$sourceUserId=$_POST["idUser_".$id];
								}
								$targetCircle=$_SESSION["currentManager"]->loadCircle($_POST["idTargetCircle_".$id]);
							
							
								$targetRole=new \holacracy\Role();
								if (isset($sourceRole)) {
									$targetRole->setName($sourceRole->getName());
								} else {
									$targetRole->setName($sourceCircle->getName());
								}
								$targetRole->setType(\holacracy\Role::LINK_ROLE);
		
								$targetCircle->attachRole($targetRole);
								
								$targetRole->setMaster($mainCircle);
								$targetRole->setSourceCircle($sourceCircle); 
								if (isset($sourceRole)) {
									$targetRole->setSource($sourceRole);
								}
								if (isset($sourceUserId)) {
									$targetRole->setUserId($sourceUserId);
								}
								
		
								// Sauvegarde pour les ID des cercles nouvellement créés
								$_SESSION["currentManager"]->save($targetRole);
								
								// Construit la chaîne d'information
								$strAction="Un lien transverse de [".$sourceCircle->getName()."] vers [".$targetCircle->getName()."] a été créé.";
								$historyEntry=$history->attachChild();
								$historyEntry->setTitle($strAction);
								$historyEntry->setLink("/role.php?id=".$targetRole->getId());
								$historyEntry->setRole($sourceCircle->getLeadLink());
						
							
							} else {
					
								if (isset($_POST["titrePolicy_".$id]) && $_POST["titrePolicy_".$id]!="") {
									// Sauvegarde une nouvelle policy
									$policy=new \holacracy\Policy();
									$policy->setTitle(utf8_decode($_POST["titrePolicy_".$id]));
									$policy->setDescription(utf8_decode($_POST["descriptionPolicy_".$id]));
									$policy->setCircle($mainCircle);
									$_SESSION["currentManager"]->save($policy);
									
									// Construit la chaîne d'information
									$strAction="La politique [".$policy->getTitle()."] a été créé.";
									$historyEntry=$history->attachChild();
									$historyEntry->setTitle($strAction);
									//$historyEntry->setLink("/circle.php?id=".$circle->getId()); // Peut-être plus tard
								}
							}
						break;	
						case 8:
						if (isset($_POST["idSourceRole_".$id]) && $_POST["idSourceRole_".$id]>0) {
							
							// Assigne l'ID à la policy
							
							$policy=$_SESSION["currentManager"]->loadRole($_POST["policy_".$id]);
							$role=$_SESSION["currentManager"]->loadRole($_POST["idSourceRole_".$id]);
							$policy->setSource($_POST["idSourceRole_".$id]);
							$_SESSION["currentManager"]->save($policy);

							$strAction="Le lien transverse vers [".$policy->getSuperCircle()->getName()."] a été attachée au rôle [".$role->getName()."].";
							$historyEntry=$history->attachChild();
							$historyEntry->setTitle($strAction);
							
							
						} else
							if (isset($_POST["titrePolicy_".$id]) && $_POST["titrePolicy_".$id]!="") {
							// Met à jour la politique
							$policy=$_SESSION["currentManager"]->loadPolicy($_POST["policy_".$id]);
							// Construit la chaîne d'information
							$strAction="La politique [".$policy->getTitle()."] a été modifiée.";
							$historyEntry=$history->attachChild();
							$historyEntry->setTitle($strAction);
							//$historyEntry->setLink("/circle.php?id=".$circle->getId());  // Plus tard peut-être
							if ($policy->getTitle()!=utf8_decode($_POST["titrePolicy_".$id])) {
								$detail=$historyEntry->attachChild();
								$detail->setTitle("Le titre devient [".utf8_decode($_POST["titrePolicy_".$id])."]");
							}							
							if ($policy->getDescription()!=utf8_decode($_POST["descriptionPolicy_".$id])) {
								$detail=$historyEntry->attachChild();
								$detail->setTitle("La description a été modifiée.");
								// Calcul la différence entre les 2 descriptions
								include_once '../plugins/inline_function.php';

								$text1=$policy->getDescription();
								$text2=utf8_decode($_POST["descriptionPolicy_".$id]);
								$diff = @inline_diff($text1, $text2, '');
								$detail->setDescription($diff);
							}
							//$role=$manager->loadRole($action->getSourceId());
							$policy->setTitle(utf8_decode($_POST["titrePolicy_".$id]));
							$policy->setDescription(utf8_decode($_POST["descriptionPolicy_".$id]));
							// champs pour les cercles
							$_SESSION["currentManager"]->save($policy);
							}
						break;					
						case 9:
							if (isset($_POST["policy_".$id]) && $_POST["policy_".$id]!="") {
								// Est-ce une vrai politique ou un lien transverse?
								if (substr($_POST["policy_".$id],0,2)=="LT") {
									// Charge le role et le supprime
									$lt=$_SESSION["currentManager"]->loadCircle(substr($_POST["policy_".$id],2));
									$strAction="Le lien transverse [".$lt->getName()."] a été supprimé.";
									$historyEntry=$history->attachChild();
									$historyEntry->setTitle($strAction);
									$historyEntry->setRole($lt);
									$lt->delete();
									
								} else {
									$policy=$_SESSION["currentManager"]->loadPolicy($_POST["policy_".$id]);
									$strAction="La politique [".$policy->getTitle()."] a été supprimée.";
									$historyEntry=$history->attachChild();
									$historyEntry->setTitle($strAction);
									$_SESSION["currentManager"]->delete($policy);
								}
							}
						break;
					default:
						echo "<div>Pas de sauvegarde pour le formulaire ".$_POST["form_type_".$id]." en position $id</div>";
				}
				echo "<div>".$strAction."</div>";
			}
			if (count($history->getChilds())==0) $history->setTitle("Aucune modification effectuée.");
			$_SESSION["currentManager"]->save($history,true);

				// Le bouton fermer recharge la page, nouelle tension retourne sur le formulaire de base

			?>
    <script>$( "#dialogStd" ).dialog({ buttons: [{ text: "ScratchPad", click:showHideScratchPad},  {text: "Nouvelle tension", click: function() {$("#formulaire").load('/formulaires/form_gouvernance.php?action=new&meeting=<?=$_POST["meeting"]?>&circle=<?=$mainCircle->getId()?>'); }}, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });</script>
			
