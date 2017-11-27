<?
	include_once("../include.php");
	
// Formulaire post�?
if (isset($_POST["circle"])) {

	if (isset($_POST["user_id"])) {

		// Charge le role et le cercle
		$role=$_SESSION["currentManager"]->loadRole($_POST["role_id"]);
		$circle=$_SESSION["currentManager"]->loadRole($_POST["circle"]);
		$namecircle = $circle->getName();
			// Contr�le d'erreur, et retour message erreur si n�cessaire.
			
			// Si c'est le r�le facilitateur, il ne faut pas y assigner le premier lien
			$leaplink=$circle->getLeapLink()->getUserId();
			if ($role->getType()==\holacracy\Role::REP_LINK_ROLE && $leaplink==$_POST["user_id"]) {
				echo "/* Erreur */\n alert('Erreur!! Le Premier lien ne peut �tre assign� comme second lien.');$('#user_id').focus();"; exit;
			}
			if ($role->getType()==\holacracy\Role::FACILITATOR_ROLE && $leaplink==$_POST["user_id"]) {
				echo "/* Erreur */\n alert('Erreur!! Le Premier lien ne peut �tre assign� comme facilitateur.');$('#user_id').focus();"; exit;
			}
			
			
		// Sauve la personne en charge du r�le
		// echo "r�le: ".$role->getId()." - ancien: ".$role->getUserId()." - nouveau: ".$_POST["user_id"];
		
		// Si ce n'est plus la m�me personne, envoie des notifications
		if ($role->getUserId()!=$_POST["user_id"]) {

			if ($role->getUserId()>0) {
				$olduser=$role->getUser();
				if (isset($olduser)) {
					$message= "Organisation: <b>".$circle->getOrganisation()->getName()."</b><br/>";
					$message.= "Cercle: <b>".$circle->getName()."</b><br/><br/>";
					$message .= "Vous avez �t� desaffect� du r�le <strong>".$role->getName()."</strong> dans le cercle <strong>".$circle->getName()."</strong> comme personne en charge du r�le.";
					$subject = $circle->getOrganisation()->getName().": D�saffectation du r�le ".$role->getName();
					if ($olduser->sendMessage($subject,$message)) { } 
					else { echo "Le message n'a pas �t� envoy�"; }	
				}
			}
			if ($_POST["user_id"]>0) {
				$newuser=$_SESSION["currentManager"]->loadUser($_POST["user_id"]);
				if (isset($newuser)) {
					$message= "Organisation: <b>".$circle->getOrganisation()->getName()."</b><br/>";
					$message.= "Cercle: <b>".$circle->getName()."</b><br/><br/>";
					$message .= "Vous avez �t� affect� au r�le <strong>".$role->getName()."</strong> dans le cercle <strong>".$circle->getName()."</strong> comme personne en charge du r�le.";
					$subject = $circle->getOrganisation()->getName().": Affectation du r�le ".$role->getName();
					if ($newuser->sendMessage($subject,$message)) { } 
					else { echo "Le message n'a pas �t� envoy�"; }	
				}
			}
			
		}
		
		$role->setUserId($_POST["user_id"]);
		$_SESSION["currentManager"]->save($role);
		
	
		
		// Sauve les focus si existants
		if (isset($_POST["scope_ligne"]))
		foreach ($_POST["scope_ligne"] as $ligne) {	
			if (isset($_POST["rofi_id_".$ligne]) && $_POST["rofi_id_".$ligne]!="") {
				// Modification d'un focus/assignation

				if (isset($_POST["memb_id_".$ligne])) {

					if ($_POST["memb_id_".$ligne]!="") {
						$focus=$_SESSION["currentManager"]->loadRoleFiller($_POST["rofi_id_".$ligne]);

						
						// Si mise � jour du deuxi�me lien, alors le retire �galement de la liste des membres du cercle supp�rieur
						if ($role->getType()==\holacracy\Role::REP_LINK_ROLE) {
							$user=$_SESSION["currentManager"]->loadUser($focus->getUserId());
							$roles=$user->getRoles($circle->getSuperCircle());
							if (count($roles)==0 && $user->isMember($circle->getSuperCircle())) {
								$_SESSION["currentManager"]->delMemberCircle($user->getId(),$circle->getSuperCircle()->getId());
							}
							
							$user=$_SESSION["currentManager"]->loadUser($_POST["memb_id_".$ligne]);
							$roles=$user->getRoles($circle->getSuperCircle());
							if (!$user->isMember($circle->getSuperCircle())) {

								$_SESSION["currentManager"]->addMemberCircle($user->getId(),$circle->getSuperCircle()->getId());
							}

						}
						
						// Si l'on supprime un premier lien, le supprime de la liste des membres du cercle inf�rieur
						if ($role->getType()==\holacracy\Role::CIRCLE) {
							// Le user a-t-il un r�le dans le cercle supp�rieur?
							$user=$_SESSION["currentManager"]->loadUser($focus->getUserId());
							$roles=$user->getRoles($role);
							if (count($roles)<=1 && $user->isMember($role)) {
								$_SESSION["currentManager"]->delMemberCircle($user->getId(),$role->getId());
							}
							$user=$_SESSION["currentManager"]->loadUser($_POST["memb_id_".$ligne]);
							$roles=$user->getRoles($role);
							if (!$user->isMember($role)) {
								$_SESSION["currentManager"]->addMemberCircle($user->getId(),$role->getId());
							}							

						}						
						
						// Mise � jour
						$focus->setUserId($_POST["memb_id_".$ligne]);
						$focus->setFocus(utf8_decode($_POST["rofi_focus_".$ligne]));
						$_SESSION["currentManager"]->save($focus);
						
						//Pour notifier le futur user affect�
						$usernotif = $_SESSION["currentManager"]->loadUser($_POST["memb_id_".$ligne]);
						$email = $usernotif->getEmail();
						$rolename = $role->getName();	
											
						//Envoi de la notification

						$message= "Organisation: <b>".$circle->getOrganisation()->getName()."</b><br/>";
						$message.= "Cercle: <b>".$circle->getName()."</b><br/><br/>";
						$message .= "Une modification d'affectation au r�le <strong>".$rolename."</strong> vous a �t� faite dans le cercle <strong>".$namecircle."</strong>";
						$subject = $circle->getOrganisation()->getName().": Modification d'affectation au r�le ".$rolename;
						if ($usernotif->sendMessage($subject,$message)) { } 
							else { echo "Le message n'a pas �t� envoy�"; }	
						
						
					} else {
						$focus=$_SESSION["currentManager"]->loadRoleFiller($_POST["rofi_id_".$ligne]);
						
						// Si l'on supprime un deuxi�me lien, alors le retire �galement de la liste des membres du cercle supp�rieur
						if ($role->getType()==\holacracy\Role::REP_LINK_ROLE) {
							// Le user a-t-il un r�le dans le cercle supp�rieur?
							$user=$_SESSION["currentManager"]->loadUser($focus->getUserId());
							$roles=$user->getRoles($circle->getSuperCircle());
							if (count($roles)==0 && $user->isMember($circle->getSuperCircle())) {
								$_SESSION["currentManager"]->delMemberCircle($user->getId(),$circle->getSuperCircle()->getId());
							}
						}
						
						// Si l'on supprime un premier lien, le supprime de la liste des membres du cercle inf�rieur
						if ($role->getType()==\holacracy\Role::CIRCLE) {
							// Le user a-t-il un r�le dans le cercle supp�rieur?
							$user=$_SESSION["currentManager"]->loadUser($focus->getUserId());
							$roles=$user->getRoles($role);
							if (count($roles)<=1 && $user->isMember($role)) {
								$_SESSION["currentManager"]->delMemberCircle($user->getId(),$role->getId());
							}
						}
						
						// Suppression d'une affectation
						$_SESSION["currentManager"]->delete($focus);
						
						// Pour notifier le futur desaffecte
						$usernotif =$_SESSION["currentManager"]->loadUser($focus->getUserId());
						$rolename = $role->getName();
						
						//Envoi de la notification

						$message= "Organisation: <b>".$circle->getOrganisation()->getName()."</b><br/>";
						$message.= "Cercle: <b>".$circle->getName()."</b><br/><br/>";
						$message .= "Vous avez �t� desaffect� du r�le <strong>".$rolename."</strong> dans le cercle <strong>".$namecircle."</strong>";
						$subject = $circle->getOrganisation()->getName().": D�saffectation du r�le ".$rolename;
						if ($usernotif->sendMessage($subject,$message)) { } 
						else { echo "Le message n'a pas �t� envoy�"; }	
					}
				}
			} else {
				if (isset($_POST["memb_id_".$ligne]) && $_POST["memb_id_".$ligne]!="") {
					$focus=new \holacracy\RoleFiller();
					$focus->setFocus(utf8_decode($_POST["rofi_focus_".$ligne]));
					$focus->setUserId($_POST["memb_id_".$ligne]);
					$focus->setRoleId($_POST["role_id"]);
					$_SESSION["currentManager"]->save($focus);
					
					//Pour notifier le futur user affect�
					$usernotif = $_SESSION["currentManager"]->loadUser($_POST["memb_id_".$ligne]);
					$rolename = $role->getName();							
											
					//Envoi de la notification

						$message= "Organisation: <b>".$circle->getOrganisation()->getName()."</b><br/>";
						$message.= "Cercle: <b>".$circle->getName()."</b><br/><br/>";
					$message .= "Vous avez �t� affect� au r�le <strong>".$rolename."</strong> dans le cercle <strong>".$namecircle."</strong>";
					$subject = $circle->getOrganisation()->getName().": Nouvelle affectation au r�le ".$rolename;
					if ($usernotif->sendMessage($subject,$message)) {  } 
						else { echo "Le message n'a pas �t� envoy�"; }			
				
					// Si l'on ajoute un premier lien, le rajoute dans la liste des membres
						if ($role->getType()==\holacracy\Role::CIRCLE) {
							// Le user a-t-il un r�le dans le cercle supp�rieur?
							$user=$_SESSION["currentManager"]->loadUser($focus->getUserId());
							$roles=$user->getRoles($role);
							if (!$user->isMember($role)) {
								$_SESSION["currentManager"]->addMemberCircle($user->getId(),$role->getId());
							}
						}
											
					// Si l'on rajoute un deuxi�me lien, le rajoute � la liste des membres.
						if ($role->getType()==\holacracy\Role::REP_LINK_ROLE) {
							// Le user a-t-il un r�le dans le cercle supp�rieur?
							$user=$_SESSION["currentManager"]->loadUser($focus->getUserId());
							$roles=$user->getRoles($circle->getSuperCircle());
							if (!$user->isMember($circle->getSuperCircle())) {
								$_SESSION["currentManager"]->addMemberCircle($user->getId(),$circle->getSuperCircle()->getId());
							}
						}
				
				}
			}
		}
		$role->checkIntegrity();
		
	}	
	// Ferme le dialog et rafra�chi la liste des users
?><script>
	refreshRole(<?=$role->getId()?>);
	refreshMembers(<?=$role->getSuperCircleId()?>);
	refreshProjects(<?=$role->getId()?>);
    $( "#dialogStd" ).dialog("close");

</script>
<?
	
	exit;
}

	$circle=$_SESSION["currentManager"]->loadCircle($_GET["circle"]);	// Le cercle qui contient
	$role=$_SESSION["currentManager"]->loadRole($_GET["role"]);	// Le r�le � assigner


	//Charge la liste des membres actuellement attribu�s, ainsi que leurs focus
	
	// Affiche la liste des membres
		echo "<form id='formulaire'>";
		echo "<input type='hidden' id='form_target' value='/formulaires/form_affect_user.php'/>";
		echo "<input type='hidden' name='circle' value='".$_GET["circle"]."'/>";
		echo "<input type='hidden' name='role_id' value='".$_GET["role"]."'/>";

		echo "<fieldset><legend><div id='mask1'></div><span>Personne en charge</span><div id='mask2'></div></legend>";
		$listeMembres=$circle->getMembers();
		$listeMembresOrga=$circle->getOrganisation()->getMembers();
		$add=array_udiff($listeMembresOrga,$listeMembres,'\holacracy\User::compareUser');

		echo "<select name='user_id' id='user_id'><option>Affect� par d�faut au Premier Lien</option>";
			if (count($listeMembres)>0) {
				echo "<optgroup label='Cercle'></optgroup>";
				foreach($listeMembres as $membre) {
					echo "<option value='".$membre->getId()."'";
					if ($membre->getId()==$role->getUserId()) echo " selected ";
					echo "> &nbsp;".$membre->getFirstName()." ".$membre->getLastName()."</option>";
				}
			}
			if (count($add)>0) {
				echo "<optgroup label='Organisation'></optgroup>";
				
				foreach($add as $membre) {
					echo "<option value='".$membre->getId()."'";
					if ($membre->getId()==$role->getUserId()) echo " selected ";
					echo "> &nbsp;".$membre->getFirstName()." ".$membre->getLastName()."</option>";
				}
			}	
		echo "</select>";
		if (!($role->getType() & (\holacracy\Role::STRUCTURAL_ROLES | \holacracy\Role::CIRCLE | \holacracy\Role::LINK_ROLE))) {
	
		echo "<div><br/><div><b>Equipe</b></div>";
		echo "<table id='membres' style='width:100%' cellspacing=0><tbody>";

		$count=1;
		foreach ($role->getRoleFillers() as $roleFiller) {
			echo "<tr class='tr_visible'><td><input type='hidden' class='id' name='scope_ligne[]' value='".$count."'/><input type='hidden' name='rofi_id_".$count."' value='".$roleFiller->getId()."'/><select id='memb_id_".$count."' name='memb_id_".$count."' class='select_name'><option value=''>Non affect�</option>";
			// Charge la liste de tous les types de contact
			if (count($listeMembres)>0) {
				echo "<optgroup label='Cercle'></optgroup>";
				foreach($listeMembres as $membre) {
					echo "<option value='".$membre->getId()."'";
					if ($membre->getId()==$roleFiller->getUserId()) echo " selected ";
					echo "> &nbsp;".$membre->getFirstName()." ".$membre->getLastName()."</option>";
				}
			}
			if (count($add)>0) {
				echo "<optgroup label='Organisation'></optgroup>";
				
				foreach($add as $membre) {
					echo "<option value='".$membre->getId()."'";
					if ($membre->getId()==$roleFiller->getUserId()) echo " selected ";
					echo "> &nbsp;".$membre->getFirstName()." ".$membre->getLastName()."</option>";
				}
			}
			echo "</select></td><td width='100%'>";
			// Choisir le type de membre d'�quipe (soutien, apprenant, focus,...)
			
			// Champ libre pour �crire un texte, par exemple sous la forme d'un focus
			echo "<input type='text' name='rofi_focus_".$count."' class='rofi_focus' id='rofi_focus_".$count."' value='".$roleFiller->getFocus()."' style='width:100%' placeholder='Focus'>";
			echo "</td><td><button type='button' class='delete-button' style='padding-top:1px; padding-bottom:1px;'><img src='images/delete.png' /></button></td></tr>";
			$count++;
		}
		echo "</tbody></table>";
		echo "<button type='button' id='add-member'>Ajouter un membre � l'�quipe</button>";
		echo "</div>"; // Zone focus
		}
		echo "</fieldset>";	
		echo "</form>";
	// Propose d'ajouter d'autres membres
?>
<script>
		// Boutons pour effacer une ligne
		$(".delete-button").button()
		.click(function () {
			// Efface le champ de focus
			$(this).closest("tr").find("input:text").val("");
			// Remet la valeur du champ de s�lection � "choisissez..."
			$(this).closest("tr").find("select").val("");
			// Cache la ligne
			$(this).closest("tr").css( "display", "none" )
			$(this).closest("tr").removeClass( "tr_visible" )					
			
			// Combine reste-t-il de lignes
			if (($( "#membres tr.tr_visible" )).length<2) {
				$('.rofi_focus').css('display','none');
				$( "#membres tr.tr_visible input:text" ).val("");
			} 
		
		});
		
		//
		var previous;
		$( "#membres" ).delegate( ".select_name", "focus", function() {
			previous=$(this).find("option:selected").val();

		});
		$( "#membres" ).delegate( ".select_name", "change", function() {
			var value = $(this).find("option:selected").val();
			$(".select_name option[value=" + previous + "]").removeAttr('disabled');
			if (value!="") $(".select_name option[value=" + value + "]").attr('disabled','disabled');
			previous=$(this).find("option:selected").val();
		}); 
		
        // Bouton pour ajouter un domaine
		   	$( "#add-member" )
	      .button()
	      .click(function() {
	      	// Quelle est la derni�re ligne
	      	tmp=$( "#membres .id" ).filter(":last").val()
	      	if ($.isNumeric(tmp)) {
	      		tmp++;
	      	} else {
	      		tmp=1;
	      	}
	      	// Ajoute une ligne au tableau � �diter
	      	     $( "#membres tbody" ).append( "<tr class='tr_visible'>" +
             		 "<td><input type='hidden' class='id' name='scope_ligne[]' value='" + tmp + "'/>" + 
					  "<input type='hidden' name='rofi_id_"+tmp+"' value=''/>" +
					  "<select class='select_name' name='memb_id_"+tmp+"' id='memb_id_"+tmp+"'><option  value=''>Choisissez...</option>" +
<? if (count($listeMembres)>0) { ?>
					  "<optgroup label='Cercle'></optgroup>" +
					  <?
							foreach($listeMembres as $membre) {
								echo "\"<option value='".$membre->getId()."'";
								echo "> &nbsp;".$membre->getFirstName()." ".$membre->getLastName()."</option>\"+";
							}
							}
							if (count($add)>0) {
					  ?>
					  "<optgroup label='Organisation'></optgroup>" +
					  <?
							foreach($add as $membre) {
								echo "\"<option value='".$membre->getId()."'";
								echo "> &nbsp;".$membre->getFirstName()." ".$membre->getLastName()."</option>\"+";
							}}
					  ?>
					  "</select></td><td width='100%'><input type='text' style='width:100%; ' class='rofi_focus' name='rofi_focus_"+tmp+"' id='rofi_focus_"+tmp+"' placeholder='Focus'/>" + "</td>" +
					  "<td><button type='button' class='delete-button' style='padding-top:1px; padding-bottom:1px;'><img src='images/delete.png' /></button></td>" +
           		 "</tr>" );
           		 $(".delete-button").button().click(function () {			
					$(this).closest("tr").find("input:text").val("");
						$(this).closest("tr").find("select").val("");
					$(this).closest("tr").css( "display", "none" )
					$(this).closest("tr").removeClass( "tr_visible" )					
					// Combine reste-t-il de lignes
					if (($( "#membres tr.tr_visible" )).length<2) {
						$('.rofi_focus').css('display','none');
						$( "#membres tr.tr_visible input:text" ).val("");
					} 
					});
				if (($( "#membres tr.tr_visible" )).length>1) {$('.rofi_focus').css('display','');}
				
				// S�lectionne les options gris�es

				$(".select_name option:selected").each(function( index ) {
					value=$(this).val();
					if (value!="") $(".select_name option[value=" + value + "]").attr('disabled','disabled');
				});
				
	      });
	      
	      // S�lectionne les options gris�es

		$(".select_name option:selected").each(function( index ) {
			value=$(this).val();
			if (value!="") $(".select_name option[value=" + value + "]").attr('disabled','disabled');
		});
	      
	      <? 
	      	// Si personne n'est affect�, rajoute une ligne vide
		  	//if ($count==1) echo "$( '#add-member' ).click();\n";
		  	
		  	// Si une seule ligne, cache les focus
		  	//if ($count<=2) {
			//	echo "$('.rofi_focus').css('display','none');\n";
		  	//}
		  	
		  	
		  	
			//if ($role->getType() & (\holacracy\Role::STRUCTURAL_ROLES | \holacracy\Role::CIRCLE | \holacracy\Role::LINK_ROLE)) {
			//	echo "$('#add-member').css('display','none');\n";
			//	echo "$('.delete-button').css('display','none');\n";
			//	echo "$('#rofi_focus_1').css('display','none');\n";
			//}
		?>
	      
	       $("#formulaire").submit(function() {
	// Envoie le formulaire en AJAX (m�thode POST), la destination �tant d�finie par l'�l�ment � l'ID form_target 
		$(".select_name option").removeAttr('disabled');

 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
        .done(function(data, textStatus, jqXHR) {
            if (textStatus="success")
            {
            	// Traite une �ventuelle erreur
            	if (data.indexOf("Erreur")>0) {
            		eval(data);
            	} else {
            	
            	
	            	// Affiche les donn�es en retour en remplacement du contenu du formulaire (le contenant reste) 
	                $("#formulaire")[0].innerHTML=data;
	                // Int�rpr�te les scripts retourn�s (� v�rifier si �a fonctionne)
	                eval($("#formulaire").find("script").text());
                }
			}
            else {
            	// Probl�me d'envoi
            	alert("Echec!");
            
            }
        });
        // Bloque la proc�dure standard d'envoi
        return false;
});

    $( "#dialogStd" ).dialog({ buttons: [{ text: "Enregistrer", click: function() { $( "#formulaire").submit(); } }, {text: "Annuler", click: function() { $( this ).dialog( "close" ); }} ] });

</script>
