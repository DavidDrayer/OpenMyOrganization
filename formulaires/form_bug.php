<?
	include_once("../include.php");
	
	if (isset($_POST["action"])) {
	
		if ($_POST["action"]=="addBug") {
			// V�rifie le formulaire
			if ($_POST["bugTitle"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le texte n\'est pas rempli.');$('#bugTitle').focus();"; exit;
			}
			if ($_POST["bugType"]=="") {
				echo "/* Erreur */\n alert('Erreur!! Le type n\'est pas rempli.');$('#bugType').focus();"; exit;
			}
			if ($_POST["bug"]=="") {
				echo "/* Erreur */\n alert('Merci de d�crire un minimum votre probl�me dans la zone d�di�e � la description.');$('#bug').focus();"; exit;
			}
		
			
			// Cr�e un nouveau bug
			$bug=new \holacracy\Bug();
			$bug->setTitle(utf8_decode($_POST["bugTitle"]));
			$bug->setDescription(utf8_decode($_POST["bug"]));
			$bug->setBugTypeId(utf8_decode($_POST["bugType"]));
			$bug->setAuthorId($_SESSION["currentUser"]->getId());
			$bug->setBugStatusId(1);
			
			// Et le sauve
			$_SESSION["currentManager"]->save($bug);
			$_POST["bug"]=$bug->getId();			
			
			// Confirme l'enregistrement
			echo "Rechargement de la page...";
			echo "<script>$('#dialogStdContent').load('/formulaires/form_bug.php');</script>";
			exit;
		}
	}
?>
		<!-- Affichage d'un syst�me � onglet -->
		<div id="tabs-bug">
		  <ul>
		    <li><a href="#tabs-bug-1">Liste des bugs</a></li>
		    <li><div><a href="#tabs-bug-2" ><span class='ui-icon ui-icon-circle-plus' style='display:inline-block; vertical-align:bottom'></span>Ajouter</a></div></li>
		  </ul>
		  <div id="tabs-bug-1">
<?

	// Affichage de la liste des bugs actuellement dans la base de donn�e
	
		function writebuglist($bugs, $max=5) {
		echo "<table class='omo-table' cellspacing=0>";
		echo "<thead><tr><th>Type</th><th>Priorit�</th><th width='100%'>Description</th><th style='white-space: nowrap;'>Propos� par</th><th style='white-space: nowrap;'>Trait� par</th></tr></thead>";
		$count=0;
		foreach ($bugs as $bug) {
			if ($count==$max) echo "<tr><td colspan='5'><a href='#' class='omo-action-moretable'>Afficher ".(count($bugs)-$max)." de plus...</a></td></tr><tfoot style='display:none'>";
			echo "<tr class='highlight'>";
			echo "<td style='min-width:20px; width:20px;' class='omo-bug-".$bug->getBugTypeId()."'></td>";
			echo "<td style='white-space: nowrap;text-align:center'>".$bug->getPriority()."</td>";
			if ($_SESSION["currentUser"]->isDevelopper()) {
				echo "<td><a href='/formulaires/form_editBug.php?id=".$bug->getId()."' alt='Editer un bug' class='dialogPage'>".$bug->getTitle()."</a></td>";
			} else {
				echo "<td style='white-space: nowrap;'>".$bug->getTitle()."</td>";
			}
			echo "<td style='white-space: nowrap;text-align:center'>".$bug->getAuthor()->getUserName()."</td>";
			echo "<td style='white-space: nowrap;text-align:center'>";
				$status=$bug->getStatus();
				if (is_null($status)) {
					echo "n/a";
				} else {
					$user=$status->getUser();
					if (!is_null($user)) {
						echo $user->getUserName();
					}
				}
			echo "</td>";
			echo "</tr>";
			$count+=1;
		}
		if ($count>=$max+1) echo "</tfoot'>";
			
		echo "</table>";		
		}
	
		// R�cemment cl�tur�s
		
		$bugs=  $_SESSION["currentManager"]->loadBugList(4,'datestatus');
		if (count($bugs)>0) {
			echo "<h2>Derniers bugs corrig�s</h2>";
			writebuglist($bugs);
		}
		$bugs=  $_SESSION["currentManager"]->loadBugList(2);
		if (count($bugs)>0) {
			echo "<h2>En cours de correction</h2>";
			writebuglist($bugs,99);
		}
		$bugs=  $_SESSION["currentManager"]->loadBugList(1);
		if (count($bugs)>0) {
			echo "<h2>En attente</h2>";
			writebuglist($bugs,10);
		}					
		$bugs=  $_SESSION["currentManager"]->loadBugList(0);
		if (count($bugs)>0) {
			echo "<h2>Nouveaux bugs non �valu�s</h2>";
			writebuglist($bugs,10);
		}		
	
		$bugs=  $_SESSION["currentManager"]->loadBugList(3);
		if (count($bugs)>0) {
			echo "<h2>Bloqu�s</h2>";
			writebuglist($bugs,10);
		}
		
		
?>

		  </div>
		  <div id="tabs-bug-2">
			 <form id='formulaire'>
<?
	  		echo "<input type='hidden' id='form_target' value='/formulaires/form_bug.php'>";
			//echo "<input type='hidden' name='circle' value='".$circleId."'>";
			//echo "<input type='hidden' name='id' value='".$id."'>";
			echo "<input type='hidden' name='action' value='addBug'>";

?>				
				<strong>Titre</strong><br/>
				<input type='text' name='bugTitle' id='bugTitle' class='fill'/><br/><br/>
				<strong>Type de bug</strong><br/>
				<select name='bugType' id='bugType'>
					<option value=''>Choisissez...</option>
					<option value='1'>Bug m'emp�chant de r�aliser une op�ration</option>
					<option value='2'>Bug d'affichage (non bloquant)</option>
					<option value='3'>Fonctionnalit� manquante pour bien fonctionner en Holacracy</option>
					<option value='4'>Suggestion / Nice to have</option>
					<option value='5'>Compliment / Remerciement</option>
				</select><br/><br/>
				<strong>Description</strong><br/>
				<textarea rows="5" cols="80" name="bug" id="bug" class='tinymce'></textarea>
			</form>
			</div>
		</div>
	<script src="plugins/tinymce/tinymce.min.js"></script>	
	<script src="plugins/tinymce/jquery.tinymce.min.js"></script>	
	
<script>
  $(function() {
    	$(".omo-action-moretable").on('click', function (e) {
			$(this).closest("td").css("display","none");
			$(this).closest("tbody").next().css("display","");
		
		});
    		// transforme le textarea en �diteur - DDr 4.6.2014
       $('textarea.tinymce').tinymce({
            // Location of TinyMCE script
			menubar : false,
			plugins: "link",
			toolbar: "undo redo | bold italic | bullist numlist outdent indent | link",
			statusbar : false

        });

         // R�soud des probl�mes de Focus lorsque TinyMCE est utilis� dans des bo�tes de dialog - DDr
		$(document).on('focusin', function(e) {
		    if ($(event.target).closest(".mce-window").length) {
				e.stopImmediatePropagation();
			}
		});
		
	$( "#dialogStd" ).dialog({ buttons: [{ icons: { primary: "ui-icon-circle-plus"}, text: "Ajouter", click: function() { $( "#tabs-bug" ).tabs( "option", "active", 1 ); } }, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });

  	/* $(".dialogPage").click(function() {
	  	openDialog ($(this).attr("href"),$(this).attr("alt"));
	  	event.preventDefault();
		event.stopPropagation();
	  });
	*/ //Remove because multiple call (bug) - DDr 1.10.2014
    $( "#tabs-bug" ).tabs({
    activate: function(event ,ui){
                        //console.log(event);
                        if (ui.newTab.index()==0) {
           					$( "#dialogStd" ).dialog({ buttons: [{ icons: { primary: "ui-icon-circle-plus"}, text: "Signaler un bug", click: function() { $( "#tabs-bug" ).tabs( "option", "active", 1 ); } }, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });
						}                        
						if (ui.newTab.index()==1) {
           					$( "#dialogStd" ).dialog({ buttons: [{ text: "Enregistrer", click: function() { $( "#formulaire").submit(); } }, {text: "Fermer", click: function() { $( this ).dialog( "close" ); }} ] });
						}
						
                    }
});

	  $("#formulaire").submit(function() {
	  	// Grise le bouton
	  	$(":button:contains('Enregistrer')").attr("disabled",true).addClass( 'ui-state-disabled' );
		// Envoie le formulaire en AJAX (m�thode POST), la destination �tant d�finie par l'�l�ment � l'ID form_target 
	 	$.post($("#form_target")[0].value, $("#formulaire").serialize()) 
	        .done(function(data, textStatus, jqXHR) {
	            if (textStatus="success")
	            {
	            	// Traite une �ventuelle erreur
	            	if (data.indexOf("Erreur")>0) {
	            		eval(data);
	            		// R�active le bouton pour tenter une deuxi�me sauvegarde
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
