<?
	include_once("../include.php");
	
	if (isset($_POST["action"])) {
	
		if ($_POST["action"]=="addStatus") {
			// V�rifie le formulaire
		//	if ($_POST["bugTitle"]=="") {
		//		echo "/* Erreur */\n alert('Erreur!! Le texte n\'est pas rempli.');$('#bugTitle').focus();"; exit;
		//	}

		
			
			// Charge le bug
			$bug=$_SESSION["currentManager"]->loadBug($_POST["bugId"]);
			if ($bug->getPriority()!=$_POST["BugPriority"]) {
				$bug->setPriority($_POST["BugPriority"]);
				$_SESSION["currentManager"]->save($bug);
			}
			
			// Cr�e un nouveau status
			$status=new \holacracy\BugStatus();
			$status->setId(utf8_decode($_POST["bugStatus"]));
			$status->setComment(utf8_decode($_POST["bugComment"]));
			$status->setUserId($_SESSION["currentUser"]->getId());
			$status->setBugId($_POST["bugId"]);
			
			// Et le sauve
			$_SESSION["currentManager"]->save($status);
			$_POST["status"]=$status->getId();	
			
			// Envoie un e-mail � l'auteur si demand�
			if (isset($_POST["bugMail"]) && $_POST["bugMail"]=="1") {
				
				$txt="Votre commentaire intitul� <b>[".$bug->getTitle()."]</b> a �t� trait� ce jour par ".$_SESSION["currentUser"]->getFirstName()." ".$_SESSION["currentUser"]->getLastName()." qui souhaite vous informer de l'�tat d'avancement:<br/><br/><b>D�tail:</b><i>".$bug->getDescription()."</i><br/>Etat actuel du traitement: <b>Status: ".$bug->getStatus()->getLabel()." - Priorit� : ".$bug->getPriority()."</b>";
				$txt.="<br/><br/>";
				if ($status->getComment()!="") {
					$txt.="<b>". str_replace("\n","<br/>",$status->getComment())."</b>";
				}
				$bug->getAuthor()->sendMessage("Nouveau message associ� � l'un de vos bug / suggestion",$txt,$_SESSION["currentUser"]);
			}		
			
			// Confirme l'enregistrement
			echo "Rechargement de la page...";
			echo "<script>$('#dialogStdContent').load('/formulaires/form_editBug.php?id=".$_POST["bugId"]."');</script>";
			exit;
		}
			echo "/* Erreur */\n alert('Erreur!! Action non valide (".$_POST["action"].").');"; exit;
		
	}	
	
	// Affichage des infos sur le bug
	$bug=  $_SESSION["currentManager"]->loadBug($_GET["id"]);
	echo "<fieldset><legend><div id='mask1'></div><span>".$bug->getTitle()."</span><div id='mask2'></div></legend>";
	echo "<div>".$bug->getDescription()."</div>";
	echo "<div style='text-align: right; font-size:smaller'><i>Propos� par ".$bug->getAuthor()->getFirstName()." ".$bug->getAuthor()->getLastName()." (".$bug->getAuthor()->getUserName()."), le ".$bug->getCreationDate()."</i></div>";
	echo "</fieldset>";
	// Affichage de l'historique
	echo "<div><b>Historique</b></div>";
	$history=$bug->getHistory();
	if (count($history)>0) {
		echo "<table class='omo-table' cellspacing=0>";
		echo "<thead><tr><th>Status</th><th>Commentaire</th><th>Auteur</th><th>Date</th></tr></thead>";
		foreach($history as $status) {
			echo "<tr class='highlight'>";
			echo "<td>".$status->getLabel()."</td>";
			echo "<td>".$status->getComment()."</td>";
			echo "<td>".$status->getUser()->getUserName()."</td>";
			echo "<td>".$status->getDate()."</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
	// Formulaire pour ajouter une �volution de status ou un commentaire$
	 
			echo "<form id='formulaire'>";
	  		echo "<input type='hidden' id='form_target' value='/formulaires/form_editBug.php'>";
			echo "<input type='hidden' name='bugId' id='bugId' value='".$_GET["id"]."'>";
			//echo "<input type='hidden' name='id' value='".$id."'>";
			echo "<input type='hidden' name='action' value='addStatus'>";
			$status=  $_SESSION["currentManager"]->loadBugStatus();


			echo "<p>&nbsp;</p><div style='display:inline-block; margin-right:20px;'><strong>Status</strong><br/>";
			echo "<select name='bugStatus' id='bugStatus'>";
			foreach ($status as $stat) {
				echo "<option value='".$stat->getId()."'";
				if (count($history)>0 && $history[0]->getId()==$stat->getId())  echo " selected ";
				echo ">".$stat->getLabel()."</option>";
			}
			echo "</select>";
?>
				</div><div style='display:inline-block'><strong>Priorit�</strong><br/>
				<input type='text' name='BugPriority' id='BugPriority' value='<?=$bug->getPriority()?>'/><span style='display:inline-block; margin-left:20px;width:200px' id="slider"></span></div><br/><br/>
				<strong>Commentaire</strong><br/>
				<textarea rows="5" cols="80" name="bugComment" id="bugComment"></textarea>
				<div><input type='checkbox' value="1" name='bugMail'/> Envoyer une copie � l'auteur du bug par e-mail</div>
			</form>
<script>
$(function() {

    $( "#dialogStd" ).dialog({ buttons: [{ text: "Enregistrer", click: function() { $( "#formulaire").submit(); } },{ text: "Retourner sur la liste des bugs", click: function() { $('#dialogStdContent').load('/formulaires/form_bug.php'); } }, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });

	  $("#formulaire").submit(function() {
	  	// Grise le bouton "enregistrer"
		$(":button:contains('Enregistrer')").attr("disabled",true).addClass( 'ui-state-disabled' );

	  	
		// Envoie le formulaire en AJAX (m�thode POST), la destination �tant d�finie par l'�l�ment � l'ID form_target 
	 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
	        .done(function(data, textStatus, jqXHR) {
	            if (textStatus="success")
	            {
	            	// Traite une �ventuelle erreur
	            	if (data.indexOf("Erreur")>0) {
	            		eval(data);
	            		// Restaure le bouton enregistrer, pour un deuxi�me essai si n�cessaire
						$(":button:contains('Enregistrer')").attr("disabled",false).removeClass( 'ui-state-disabled' );

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
	        })
	        .fail(function() {
				alert( "Probl�me d'envoi des donn�es au serveur." );
	            // Restaure le bouton enregistrer, pour un deuxi�me essai si n�cessaire
				$(":button:contains('Enregistrer')").attr("disabled",false).removeClass( 'ui-state-disabled' );
			});
	        // Bloque la proc�dure standard d'envoi
	        return false;
	});
	
  });
</script>
	
