<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
	// Inclus les éléments partagés entre plusieurs pages: base de donnée, instanciation de classe, etc...
	include_once($_SERVER['DOCUMENT_ROOT'] . "/include.php");
	include_once($_SERVER['DOCUMENT_ROOT'] . "/plugins/libMiniature.php");
	


	if (isset($_GET["refresh"])) {
		switch ($_GET["refresh"]) {
			case "scratchpad" :
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				$meeting=$manager->loadMeeting($_GET["meeting"]);
				echo $meeting->getScratchpad();
				break;
			case "chat" :
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				$meeting=$manager->loadMeeting($_GET["meeting"]);
				include_once("content_chat.php");

				break;
			case "tension" :
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				$meeting=$manager->loadMeeting($_GET["meeting"]);
				include_once("content_tension.php");

				break;
			case "history" :
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				$meeting=$manager->loadMeeting($_GET["meeting"]);
				
				$historique=$meeting->getHistory();
				include_once("content_history.php");
				break;
			case "metrics" :
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				$circle=$manager->loadCircle($_GET["circle"]);
				$metrics=$circle->getAllMetrics();
				include_once("onglet_metrics.php");
				break;
			case "checklist" :
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				$circle=$manager->loadCircle($_GET["circle"]);
				$checklist=$circle->getChecklist();
				include_once("onglet_checklist.php");
				break;
			case "roleContent" :
				// Instantiation du gestionnaire de base de donnée
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				// Chargement de l'élément cercle sélectionné
				$role=$manager->loadRole($_GET["role"]);			
				echo "<table style='width:100%'><tr><td style='width:66%'>";
				// S'il y a une raison d'être, l'affiche
				if ($role->getPurpose()!="") {
					echo "<fieldset class='light'><legend><div id='mask1'></div><span class='omo-purpose'>".T_("Raison d'&ecirc;tre")."</span><div id='mask2'></div></legend>";
					echo $role->getPurpose()."</fieldset>";
				}
				
				// S'il y a une raison d'être, l'affiche
				if ($role->getType()==\holacracy\Role::CIRCLE && $role->getStrategy()!="") {
					echo "<div class='omo-light-accordion light'><h3><span class='omo-strategy omo-label'>".T_("Strat&eacute;gie")."</span></h3><div>".$role->getStrategy()."</div></div>";
				}
	
				// S'il y a un domaine, l'affiche
				if (count($role->getScopes())>0) {
					echo "<div class='omo-light-accordion light'><h3><span class='omo-scope omo-label'>".T_("Domaines")."</span></h3><div>";
					echo "<ul>";
					foreach ($role->getScopes() as $scope) {
						echo "<li>".$scope->getDescription()."</li>";
					}			
					echo "</ul>";
				}
				echo "</div></div>";
				
				// S'il y a un domaine, l'affiche
				if (count($role->getAccountabilities())>0) {
					echo "<div class='omo-light-accordion light'><h3><span class='omo-accountabilities omo-label'>".T_("Redevabilit&eacute;s")."</span></h3><div>";
					echo "<ul>";
					foreach ($role->getAccountabilities() as $accountability) {
						echo "<li>".$accountability->getDescription()."</li>";
					}			
					echo "</ul>";
				}
				echo "</div></div></td>";
				
				//if ($column>1) {echo "</tr><tr>";}
				echo "<td>";
				
				// Affiche le détail des rôles fillers avec l'intégralité du nom, ainsi que les focus
				$roleFillers=$role->getRoleFillers();
if ($role->getUserId()>0 || ($role->getSourceId()>0 && $role->getSource()->getUserId()>0) || count($roleFillers)>0) {
				echo "<fieldset class='light'><legend><div id='mask1'></div><span class='omo-user'>".T_("Energ&eacute;tis&eacute; par")."</span><div id='mask2'></div></legend>";
				if ($role->getUserId()>0 || ($role->getSourceId()>0 && $role->getSource()->getUserId()>0)) {
					if ($role->getUserId()>0) 
						$roleD=$role;
					else
						$roleD=$role->getSource();
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
			}
				echo "</td></tr></table>";
				// Lien vers le détail d'un sous-cercle ou d'un rôle
				if ($role instanceof Circle) {
					echo "<a class='ui-icon-circle-plus' href='circle.php?id=".$role->getId()."'>".T_("Afficher le détail du cercle")."</a>";
				}
				if ($role instanceof Role) {
					echo "<a class='ui-icon-circle-plus' href='role.php?id=".$role->getId()."'>".T_("Afficher le détail du r&ocirc;le")."</a>";
				}
			break;
			case "role" :
				// Instantiation du gestionnaire de base de donnée
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				// Chargement de l'élément cercle sélectionné
				$role=$manager->loadRole($_GET["role"]);					
				
				echo "<span class='omo-role-".$role->getType()."'>";

			echo "<b>".$role->toHTMLString()."</b></span>";
			echo "<span id='role_".$role->getId()."' style='float:right;' class='omo-accordion-info'>";
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
			
			if (isset($_SESSION["currentUser"]) && ($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$role->getSuperCircle()) && $type != 2 || $_SESSION["currentUser"]->isAdmin())){ //Si le role n'est pas un 1er Lien

				echo "<img src='images/edit-user.png' href='formulaires/form_affect_user.php?circle=".$role->getSuperCircle()->getId()."&role=".$role->getId()."' class='dialogPage' alt='".T_("Assigner le r&ocirc;le ").$role->getName()."'/>";
			}
			
			echo "</span>";
			?>
				<script>
					$('.dialogPage').click(function(event) {
						event.preventDefault(); event.stopPropagation();
						openDialog ($(this).attr("href"),$(this).attr("alt"));
					});
				</script>
			<?
				
			break;
			case "member" :
				// Instantiation du gestionnaire de base de donnée
				$manager=new \datamanager\SqlManager($_SESSION["currentDB"]);
				// Chargement de l'élément cercle sélectionné
				$circle=$manager->loadCircle($_GET["circle"]);			
				// Affichage de la liste des membres
				foreach ($circle->getMembers() as $member) {

				echo "<p class='omo-user-block user_draggable ui-corner-all'>";
				
				
				if (checkMini("/images/user/".$member->getId().".jpg",30,30,"mini",1,5)) {
					echo "<a href='/user.php?id=".$member->getId()."&circle=".$circle->getId()."' class='dialogPage omo-user-img' alt='".$member->getFullName()."'><img src='/images/user/mini/".$member->getId().".jpg'/></a>";
				} else {
					echo "<a href='/user.php?id=".$member->getId()."&circle=".$circle->getId()."' class='dialogPage omo-user-img' alt='".$member->getFullName()."'><img src='/images/user/mini/0.jpg'/></a>";
				}
	
				echo "<b>".$member->getFullName()." </b>";
				if ((isset($_SESSION["currentUser"]) && (($_SESSION["currentUser"]->isRole(\holacracy\Role::LEAD_LINK_ROLE,$circle)) AND ($member->getId()!= $_SESSION["currentUser"]->getId() ) AND (count($member->getRoles($circle,255,true))==0)))) {
					echo "<span style='float:right;  position: relative;'><img src='images/delete.png' id='".$member->getId()."' class='imgdeleteusercircle' style='cursor:pointer;vertical-align:middle;'/>";
					echo "</span>";
					echo "<form id='formdeletemembercircle_".$member->getId()."' action=''><input type='hidden' name='userid' value='".$member->getId()."'/><input type='hidden' name='roleid' value='".$circle->getId()."'/></form>";
				}

				echo "</p>";
				}
				?><script>
				
				//Lancement de la fenêtre suppression membre cercle
				$("img.imgdeleteusercircle").on("click",function(event){
				event.preventDefault();
				
				// Cache le bouton
				$(this).css("display","none");
				$(this).closest("p").animate({opacity: 0 }, 3000);
				
				var id_member = $(this).attr("id"); //On recupère l'ID circle
				var id_formmember = 'formdeletemembercircle_'+id_member; //On prepare l'ID du form
				//alert("ON VA ENVOYER LE FORMULAIRE : "+id_formmember);
				
				//on envoi le formulaire vers cette page pour desaffecter le membre du role
				  $.post("/ajax/deleteusercircle.php",  
				  $("#"+id_formmember).serialize(),   //On recuperer les datas du formulaire
				  function(data,status){
					if (status === "success") {
					//On modifie l'affichage
					//alert(data);
					refreshMembers(<?=$circle->getId()?>);
					//alert("ON RAFFRAICHIT LA PAGE");
					}
					else{
						alert("Impossible de supprimer cet utilisateur");
						$(this).css("display","");
						$(this).closest("p").animate({opacity: 1 }, 500);
						
					}
				 }); 
				 return false;
				});

				</script>
				<?
			break;
		}
	}
?>
